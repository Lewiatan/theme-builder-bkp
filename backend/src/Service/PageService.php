<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ShopNotFoundException;
use App\ReadModel\PageReadModel;
use App\Repository\PageRepository;
use App\Repository\ShopRepository;

/**
 * Service for authenticated page-related business operations.
 *
 * Orchestrates page data retrieval for authenticated users,
 * ensuring data isolation by only returning pages for the user's shop.
 */
final readonly class PageService
{
    public function __construct(
        private ShopRepository $shopRepository,
        private PageRepository $pageRepository
    ) {}

    /**
     * Retrieves all pages for the authenticated user's shop.
     *
     * Enforces data isolation by first finding the user's shop,
     * then retrieving only pages belonging to that shop.
     *
     * @param string $userId UUID of the authenticated user
     * @return PageReadModel[] Array of page read models
     * @throws ShopNotFoundException If user doesn't have an associated shop
     */
    public function getPagesByUserId(string $userId): array
    {
        // Find shop by user ID
        $shop = $this->shopRepository->findByUserId($userId);

        // If user has no shop, throw exception (edge case)
        if ($shop === null) {
            throw new ShopNotFoundException($userId);
        }

        // Retrieve all pages for the shop
        return $this->pageRepository->findAllByShopId($shop->getId());
    }
}
