<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Enum\PageType;
use App\Service\PageService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Public API controller for Demo Shop page retrieval.
 *
 * Provides unauthenticated access to published page layouts for rendering
 * in the Demo Shop frontend. This controller handles the public-facing
 * API that does not require authentication.
 */
final class PublicShopController extends AbstractController
{
    public function __construct(
        private readonly PageService $pageService,
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
    public function getPage(string $shopId, string $type = 'home'): JsonResponse
    {
        try {
            // Convert string type to PageType enum
            $pageType = PageType::fromString($type);

            $pageReadModel = $this->pageService->getPublicPage($shopId, $pageType);

            if ($pageReadModel === null) {
                $this->logger->info('Page not found', [
                    'shopId' => $shopId,
                    'type' => $type,
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
                'type' => $type,
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
