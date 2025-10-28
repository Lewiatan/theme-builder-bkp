<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ShopNotFoundException;
use App\Model\Entity\User;
use App\Service\PageService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for authenticated user's page management.
 *
 * Provides authenticated access to page data for the user's shop.
 * All endpoints require valid JWT authentication and enforce data isolation.
 */
final class PageController extends AbstractController
{
    public function __construct(
        private readonly PageService $pageService,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Retrieves all pages for the authenticated user's shop.
     *
     * Returns the complete set of pages (home, catalog, product, contact)
     * with their layout configurations and metadata. Pages are automatically
     * filtered to only include those belonging to the authenticated user's shop.
     *
     * @param User $user Authenticated user (automatically injected by Symfony Security)
     * @return JsonResponse Page data (200), shop not found (404), or error (500)
     *
     * Response examples:
     * - 200 OK: {"pages": [{"type": "home", "layout": [...], "created_at": "...", "updated_at": "..."}]}
     * - 404 Not Found: {"error": "Shop not found for user"}
     * - 500 Internal Server Error: {"error": "An unexpected error occurred"}
     */
    #[Route('/api/pages', name: 'api_pages_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getPages(): JsonResponse
    {
        try {
            // Get the authenticated user from security context
            /** @var User $authenticatedUser */
            $authenticatedUser = $this->getUser();

            if (!$authenticatedUser instanceof User) {
                $this->logger->error('Authenticated user is not a User instance', [
                    'user_class' => get_class($authenticatedUser),
                ]);

                return new JsonResponse(
                    ['error' => 'Authentication error'],
                    JsonResponse::HTTP_UNAUTHORIZED
                );
            }

            // Retrieve pages for user's shop
            $pages = $this->pageService->getPagesByUserId($authenticatedUser->getId());

            // Return pages wrapped in response structure
            return new JsonResponse(
                ['pages' => $pages],
                JsonResponse::HTTP_OK
            );
        } catch (ShopNotFoundException $exception) {
            $this->logger->info('Shop not found for user', [
                'message' => $exception->getMessage(),
            ]);

            return new JsonResponse(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Unexpected error retrieving pages', [
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
