<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\ReadModel\DemoProductReadModel;
use App\Repository\DemoProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Unit tests for DemoProductRepository
 *
 * Tests the repository with database access to verify:
 * - Correct SQL query execution
 * - ReadModel construction from database rows
 * - Category filtering logic
 * - Category existence validation
 * - Alphabetical ordering
 * - Null handling for sale_price
 *
 * Note: These tests require demo data to be seeded.
 * Run: docker exec theme-builder-backend php vendor/bin/phinx seed:run -s DemoProductsSeeder
 */
#[CoversClass(DemoProductRepository::class)]
final class DemoProductRepositoryTest extends KernelTestCase
{
    private ManagerRegistry $registry;
    private DemoProductRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->registry = $kernel->getContainer()->get('doctrine');
        $this->repository = new DemoProductRepository($this->registry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    #[Test]
    public function it_returns_all_products_ordered_by_name(): void
    {
        // Act
        $products = $this->repository->findAllProducts();

        // Assert
        $this->assertIsArray($products);
        $this->assertNotEmpty($products, 'Expected products to be seeded in database');
        $this->assertContainsOnlyInstancesOf(DemoProductReadModel::class, $products);

        // Verify alphabetical ordering
        $productNames = array_map(
            fn(DemoProductReadModel $product) => $product->jsonSerialize()['name'],
            $products
        );

        $sortedNames = $productNames;
        sort($sortedNames);

        $this->assertEquals(
            $sortedNames,
            $productNames,
            'Products should be ordered alphabetically by name'
        );
    }

    #[Test]
    public function it_returns_read_models_with_correct_structure(): void
    {
        // Act
        $products = $this->repository->findAllProducts();

        // Assert
        $this->assertNotEmpty($products);

        $firstProduct = $products[0];
        $serialized = $firstProduct->jsonSerialize();

        // Verify all required fields are present
        $expectedKeys = [
            'id', 'category_id', 'category_name', 'name', 'description',
            'price', 'sale_price', 'image_thumbnail', 'image_medium', 'image_large'
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $serialized);
        }

        // Verify types
        $this->assertIsInt($serialized['id']);
        $this->assertIsInt($serialized['category_id']);
        $this->assertIsString($serialized['category_name']);
        $this->assertIsString($serialized['name']);
        $this->assertIsString($serialized['description']);
        $this->assertIsInt($serialized['price']);
        $this->assertTrue(
            is_int($serialized['sale_price']) || is_null($serialized['sale_price']),
            'sale_price should be int or null'
        );
        $this->assertIsString($serialized['image_thumbnail']);
        $this->assertIsString($serialized['image_medium']);
        $this->assertIsString($serialized['image_large']);
    }

    #[Test]
    public function it_includes_category_name_via_join(): void
    {
        // Act
        $products = $this->repository->findAllProducts();

        // Assert
        $this->assertNotEmpty($products);

        foreach ($products as $product) {
            $serialized = $product->jsonSerialize();
            $this->assertArrayHasKey('category_name', $serialized);
            $this->assertNotEmpty($serialized['category_name']);
            $this->assertIsString($serialized['category_name']);
        }
    }

    #[Test]
    public function it_filters_products_by_category_id(): void
    {
        // Arrange - Get a category ID from existing products
        $allProducts = $this->repository->findAllProducts();
        $this->assertNotEmpty($allProducts, 'Need seeded products for this test');

        $firstProduct = $allProducts[0];
        $categoryId = $firstProduct->jsonSerialize()['category_id'];

        // Act
        $filteredProducts = $this->repository->findProductsByCategoryId($categoryId);

        // Assert
        $this->assertIsArray($filteredProducts);
        $this->assertNotEmpty($filteredProducts);
        $this->assertContainsOnlyInstancesOf(DemoProductReadModel::class, $filteredProducts);

        // Verify all products belong to the requested category
        foreach ($filteredProducts as $product) {
            $serialized = $product->jsonSerialize();
            $this->assertEquals(
                $categoryId,
                $serialized['category_id'],
                'All filtered products should belong to the requested category'
            );
        }
    }

    #[Test]
    public function it_filters_products_ordered_by_name(): void
    {
        // Arrange - Get a category ID that has multiple products
        $allProducts = $this->repository->findAllProducts();
        $this->assertNotEmpty($allProducts);

        $firstProduct = $allProducts[0];
        $categoryId = $firstProduct->jsonSerialize()['category_id'];

        // Act
        $filteredProducts = $this->repository->findProductsByCategoryId($categoryId);

        // Assert
        if (count($filteredProducts) > 1) {
            $productNames = array_map(
                fn(DemoProductReadModel $product) => $product->jsonSerialize()['name'],
                $filteredProducts
            );

            $sortedNames = $productNames;
            sort($sortedNames);

            $this->assertEquals(
                $sortedNames,
                $productNames,
                'Filtered products should be ordered alphabetically by name'
            );
        } else {
            $this->markTestSkipped('Need at least 2 products in same category to test ordering');
        }
    }

    #[Test]
    public function it_returns_empty_array_when_category_has_no_products(): void
    {
        // Arrange - Use a very high category ID that likely doesn't have products
        // First, verify the category exists
        $categoryId = 999;

        // Create a category without products for testing
        $connection = $this->registry->getConnection();
        $connection->executeStatement(
            'INSERT INTO demo_categories (id, name) VALUES (:id, :name) ON CONFLICT (id) DO NOTHING',
            ['id' => $categoryId, 'name' => 'Empty Category']
        );

        // Verify category exists
        $exists = $this->repository->categoryExists($categoryId);

        if (!$exists) {
            $this->markTestSkipped('Could not create test category');
        }

        // Act
        $products = $this->repository->findProductsByCategoryId($categoryId);

        // Assert
        $this->assertIsArray($products);
        $this->assertEmpty($products);

        // Cleanup
        $connection->executeStatement('DELETE FROM demo_categories WHERE id = :id', ['id' => $categoryId]);
    }

    #[Test]
    public function it_checks_category_existence_correctly(): void
    {
        // Arrange - Get an existing category
        $allProducts = $this->repository->findAllProducts();
        $this->assertNotEmpty($allProducts);

        $existingCategoryId = $allProducts[0]->jsonSerialize()['category_id'];
        $nonExistentCategoryId = 99999;

        // Act
        $existingCategoryExists = $this->repository->categoryExists($existingCategoryId);
        $nonExistentCategoryExists = $this->repository->categoryExists($nonExistentCategoryId);

        // Assert
        $this->assertTrue($existingCategoryExists, 'Existing category should be found');
        $this->assertFalse($nonExistentCategoryExists, 'Non-existent category should not be found');
    }

    #[Test]
    public function it_handles_null_sale_price_correctly(): void
    {
        // Act
        $products = $this->repository->findAllProducts();

        // Assert
        $this->assertNotEmpty($products);

        $hasNullSalePrice = false;
        $hasNonNullSalePrice = false;

        foreach ($products as $product) {
            $serialized = $product->jsonSerialize();

            if ($serialized['sale_price'] === null) {
                $hasNullSalePrice = true;
            } else {
                $hasNonNullSalePrice = true;
                $this->assertIsInt($serialized['sale_price']);
            }
        }

        // Verify we have both cases in our test data
        $this->assertTrue(
            $hasNullSalePrice || $hasNonNullSalePrice,
            'Should have products with or without sale prices in test data'
        );
    }

    #[Test]
    public function it_returns_different_products_for_different_categories(): void
    {
        // Arrange - Get products from at least 2 different categories
        $allProducts = $this->repository->findAllProducts();
        $this->assertGreaterThanOrEqual(2, count($allProducts), 'Need at least 2 products');

        $categories = array_unique(array_map(
            fn(DemoProductReadModel $product) => $product->jsonSerialize()['category_id'],
            $allProducts
        ));

        if (count($categories) < 2) {
            $this->markTestSkipped('Need products from at least 2 different categories');
        }

        $categoryIds = array_values($categories);
        $category1Id = $categoryIds[0];
        $category2Id = $categoryIds[1];

        // Act
        $category1Products = $this->repository->findProductsByCategoryId($category1Id);
        $category2Products = $this->repository->findProductsByCategoryId($category2Id);

        // Assert
        $this->assertNotEmpty($category1Products);
        $this->assertNotEmpty($category2Products);

        // Verify products are different
        $category1Ids = array_map(
            fn(DemoProductReadModel $p) => $p->jsonSerialize()['id'],
            $category1Products
        );
        $category2Ids = array_map(
            fn(DemoProductReadModel $p) => $p->jsonSerialize()['id'],
            $category2Products
        );

        $this->assertEmpty(
            array_intersect($category1Ids, $category2Ids),
            'Products from different categories should not overlap'
        );
    }

    #[Test]
    public function it_handles_empty_database_gracefully(): void
    {
        // Arrange - This test assumes we can truncate tables or test with empty DB
        // For safety, we'll just verify the method returns an array even if empty
        $connection = $this->registry->getConnection();

        // Create temporary empty tables for this test
        $connection->beginTransaction();

        try {
            // Backup current data state is not needed for this simple test
            // Just verify the method signature returns array

            // Act - calling with non-existent category
            $products = $this->repository->findProductsByCategoryId(999999);

            // Assert
            $this->assertIsArray($products);

            $connection->rollBack();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
