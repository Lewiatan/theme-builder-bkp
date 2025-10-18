<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Model\Entity\Page;
use App\Model\Enum\PageType;
use App\ReadModel\PageReadModel;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Unit tests for PageRepository
 *
 * Tests the findPublicPageByShopAndType method with various scenarios:
 * - Page exists for shop and type
 * - Shop doesn't exist
 * - Page doesn't exist for the given type
 */
#[CoversClass(PageRepository::class)]
final class PageRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private function getRepository(): PageRepository
    {
        /** @var PageRepository $repository */
        $repository = $this->entityManager->getRepository(Page::class);
        return $repository;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    #[Test]
    public function it_returns_page_read_model_when_page_exists(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';
        $pageType = PageType::HOME;

        // Act
        $result = $this->getRepository()->findPublicPageByShopAndType($shopId, $pageType);

        // Assert
        $this->assertInstanceOf(PageReadModel::class, $result);
        $serialized = $result->jsonSerialize();

        $this->assertArrayHasKey('type', $serialized);
        $this->assertArrayHasKey('layout', $serialized);
        $this->assertEquals('home', $serialized['type']);
        $this->assertArrayHasKey('components', $serialized['layout']);
        $this->assertIsArray($serialized['layout']['components']);
    }

    #[Test]
    public function it_returns_null_when_shop_does_not_exist(): void
    {
        // Arrange
        $nonExistentShopId = '00000000-0000-0000-0000-000000000000';
        $pageType = PageType::HOME;

        // Act
        $result = $this->getRepository()->findPublicPageByShopAndType($nonExistentShopId, $pageType);

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_page_type_does_not_exist(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';
        $nonExistentPageType = PageType::PRODUCT; // Assuming this doesn't exist in test data

        // Act
        $result = $this->getRepository()->findPublicPageByShopAndType($shopId, $nonExistentPageType);

        // Assert
        $this->assertNull($result);
    }

    #[Test]
    public function it_page_read_model_structure_is_correct(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';
        $pageType = PageType::HOME;

        // Act
        $result = $this->getRepository()->findPublicPageByShopAndType($shopId, $pageType);

        // Assert
        $this->assertNotNull($result);
        $serialized = $result->jsonSerialize();

        // Verify structure matches expected format
        $this->assertIsString($serialized['type']);
        $this->assertIsArray($serialized['layout']);
        $this->assertArrayHasKey('components', $serialized['layout']);

        // Verify components structure if data exists
        if (!empty($serialized['layout']['components'])) {
            $component = $serialized['layout']['components'][0];
            $this->assertArrayHasKey('id', $component);
            $this->assertArrayHasKey('type', $component);
            $this->assertArrayHasKey('variant', $component);
            $this->assertArrayHasKey('settings', $component);
        }
    }

    #[Test]
    public function it_returns_different_pages_for_different_types(): void
    {
        // Arrange
        $shopId = '550e8400-e29b-41d4-a716-446655440000';

        // Act
        $homePage = $this->getRepository()->findPublicPageByShopAndType($shopId, PageType::HOME);
        $catalogPage = $this->getRepository()->findPublicPageByShopAndType($shopId, PageType::CATALOG);

        // Assert
        $this->assertNotNull($homePage);
        $this->assertNotNull($catalogPage);

        $homeData = $homePage->jsonSerialize();
        $catalogData = $catalogPage->jsonSerialize();

        $this->assertEquals('home', $homeData['type']);
        $this->assertEquals('catalog', $catalogData['type']);

        // Verify they have different layouts
        $this->assertNotEquals($homeData['layout'], $catalogData['layout']);
    }
}
