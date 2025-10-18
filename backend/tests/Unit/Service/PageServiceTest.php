<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Model\Enum\PageType;
use App\ReadModel\PageReadModel;
use App\Repository\PageRepository;
use App\Service\PageService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for PageService
 *
 * Tests the service layer with mocked repository to verify:
 * - Correct delegation to repository
 * - Proper null handling
 * - Correct parameter passing
 */
final class PageServiceTest extends TestCase
{
    private PageRepository $pageRepository;
    private PageService $pageService;

    protected function setUp(): void
    {
        $this->pageRepository = $this->createMock(PageRepository::class);
        $this->pageService = new PageService($this->pageRepository);
    }

    public function testGetPublicPageReturnsPageReadModelWhenPageExists(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';
        $pageType = PageType::HOME;
        $expectedReadModel = new PageReadModel('home', [
            ['id' => 'cmp_001', 'type' => 'hero', 'variant' => 'default', 'settings' => []],
        ]);

        $this->pageRepository
            ->expects($this->once())
            ->method('findPublicPageByShopAndType')
            ->with($shopId, $pageType)
            ->willReturn($expectedReadModel);

        // Act
        $result = $this->pageService->getPublicPage($shopId, $pageType);

        // Assert
        $this->assertSame($expectedReadModel, $result);
    }

    public function testGetPublicPageReturnsNullWhenPageDoesNotExist(): void
    {
        // Arrange
        $shopId = '00000000-0000-0000-0000-000000000000';
        $pageType = PageType::HOME;

        $this->pageRepository
            ->expects($this->once())
            ->method('findPublicPageByShopAndType')
            ->with($shopId, $pageType)
            ->willReturn(null);

        // Act
        $result = $this->pageService->getPublicPage($shopId, $pageType);

        // Assert
        $this->assertNull($result);
    }

    public function testGetPublicPagePassesCorrectParametersToRepository(): void
    {
        // Arrange
        $shopId = 'test-shop-id';
        $pageType = PageType::CATALOG;

        $this->pageRepository
            ->expects($this->once())
            ->method('findPublicPageByShopAndType')
            ->with(
                $this->identicalTo($shopId),
                $this->identicalTo($pageType)
            )
            ->willReturn(null);

        // Act
        $this->pageService->getPublicPage($shopId, $pageType);

        // Assert - expectations verified by PHPUnit
    }

    public function testGetPublicPageWorksForAllPageTypes(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';
        $pageTypes = [PageType::HOME, PageType::CATALOG, PageType::PRODUCT, PageType::CONTACT];

        foreach ($pageTypes as $index => $pageType) {
            // Create a fresh mock for each iteration
            $repository = $this->createMock(PageRepository::class);
            $service = new PageService($repository);

            $expectedReadModel = new PageReadModel($pageType->value, []);

            $repository
                ->expects($this->once())
                ->method('findPublicPageByShopAndType')
                ->with($shopId, $pageType)
                ->willReturn($expectedReadModel);

            // Act
            $result = $service->getPublicPage($shopId, $pageType);

            // Assert
            $this->assertSame($expectedReadModel, $result);
        }
    }
}
