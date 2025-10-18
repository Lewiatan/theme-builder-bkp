<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\CategoryNotFoundException;
use App\Request\GetDemoProductsRequest;
use App\Request\GetDemoShopPageRequest;
use App\Service\DemoProductService;
use App\Service\PageService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public API controller for Demo Shop data retrieval.
 *
 * Provides unauthenticated access to published page layouts and demo products
 * for rendering in the Demo Shop frontend. This controller handles the public-facing
 * API that does not require authentication.
 */
final class PublicShopController extends AbstractController
{
    public function __construct(
        private readonly PageService $pageService,
        private readonly DemoProductService $demoProductService,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Retrieves a published page layout for a specific shop and page type.
     *
     * This endpoint is used by the Demo Shop frontend to fetch page configurations
     * for rendering. Path parameters are automatically injected and converted to
     * the appropriate types by Symfony's routing system.
     *
     * @param string $shopId UUID of the shop
     * @param string $type Page type (home, catalog, product, contact) - defaults to 'home'
     * @return JsonResponse Page data (200), not found (404), or error (500)
     *
     * Response examples:
     * - 200 OK: {"type": "home", "layout": {"components": [...]}}
     * - 404 Not Found: {"error": "Page not found for this shop"}
     * - 500 Internal Server Error: {"error": "An unexpected error occurred"}
     */
    #[Route('/api/public/shops/{shopId}/pages/{type}', name: 'public_shop_page', defaults: ['type' => 'home'], methods: ['GET'])]
    public function getPage(
        #[MapQueryString] GetDemoShopPageRequest $request
    ): JsonResponse
    {
        try {
            // Convert string type to PageType enum
            $pageType = $request->getPageType();
            $shopId = $request->getShopId();

            $pageReadModel = $this->pageService->getPublicPage($shopId, $pageType);

            if ($pageReadModel === null) {
                $this->logger->info('Page not found', [
                    'shopId' => $shopId,
                    'type' => $pageType,
                ]);

                return new JsonResponse(
                    ['error' => 'Page not found for this shop'],
                    JsonResponse::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse($pageReadModel, JsonResponse::HTTP_OK);
        } catch (\Throwable $exception) {
            $this->logger->error('Unexpected error retrieving page', [
                'shopId' => $shopId,
                'type' => $pageType,
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new JsonResponse(
                ['error' => 'An unexpected error occurred'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Retrieves demo products with optional category filtering.
     *
     * This endpoint provides product data for the Demo Shop frontend.
     * Supports filtering by category via the category_id query parameter.
     * Products are returned ordered alphabetically by name.
     *
     * Symfony automatically validates the request DTO using the MapQueryString attribute.
     * Validation errors are automatically returned as 422 responses.
     *
     * @param GetDemoProductsRequest $requestDto Automatically mapped and validated query parameters
     * @return JsonResponse Product data (200), category not found (404), or error (500)
     *
     * Response examples:
     * - 200 OK: {"products": [{"id": 1, "name": "Product", ...}]}
     * - 404 Not Found: {"error": "Category with ID 999 not found"}
     * - 422 Unprocessable Entity: Validation errors (handled automatically by Symfony)
     * - 500 Internal Server Error: {"error": "An unexpected error occurred"}
     */
    #[Route('/api/demo/products', name: 'demo_products_list', methods: ['GET'])]
    public function getDemoProducts(
        #[MapQueryString] GetDemoProductsRequest $requestDto
    ): JsonResponse
    {
        try {
            // Retrieve products from service
            $products = $this->demoProductService->getProducts($requestDto->getCategoryId());

            return new JsonResponse(
                ['products' => $products],
                JsonResponse::HTTP_OK
            );
        } catch (CategoryNotFoundException $exception) {
            $this->logger->info('Category not found', [
                'category_id' => $requestDto->getCategoryId(),
                'message' => $exception->getMessage(),
            ]);

            return new JsonResponse(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Unexpected error retrieving demo products', [
                'category_id' => $requestDto->getCategoryId(),
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new JsonResponse(
                ['error' => 'An unexpected error occurred'],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
