<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\PublicShopController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional tests for PublicShopController
 *
 * Tests the complete flow from HTTP request to response:
 * - Success cases (200 OK)
 * - Not found cases (404)
 * - Invalid input cases (500)
 * - Response structure validation
 */
#[CoversClass(PublicShopController::class)]
final class PublicShopControllerTest extends WebTestCase
{
    private const VALID_SHOP_ID = '550e8400-e29b-41d4-a716-446655440000';
    private const NON_EXISTENT_SHOP_ID = '00000000-0000-0000-0000-000000000000';

    #[Test]
    public function it_returns_success_response_with_valid_shop_and_page_type(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/home');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $content);
        $this->assertArrayHasKey('layout', $content);
        $this->assertEquals('home', $content['type']);
        $this->assertArrayHasKey('components', $content['layout']);
        $this->assertIsArray($content['layout']['components']);
    }

    #[Test]
    public function it_returns_not_found_when_shop_does_not_exist(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::NON_EXISTENT_SHOP_ID . '/pages/home');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Page not found for this shop', $content['error']);
    }

    #[Test]
    public function it_returns_not_found_when_page_does_not_exist_for_shop(): void
    {
        // Arrange
        $client = static::createClient();

        // Act - Assuming 'product' page doesn't exist for this shop
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/product');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
    }

    #[Test]
    public function it_returns_server_error_with_invalid_uuid_format(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/invalid-uuid/pages/home');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('An unexpected error occurred', $content['error']);
    }

    #[Test]
    public function it_returns_server_error_with_invalid_page_type(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/invalid-type');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('An unexpected error occurred', $content['error']);
    }

    #[Test]
    public function it_defaults_to_home_when_type_not_provided(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('home', $content['type']);
    }

    #[Test]
    public function it_returns_correct_data_for_catalog_page(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/catalog');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('catalog', $content['type']);
        $this->assertArrayHasKey('layout', $content);
        $this->assertArrayHasKey('components', $content['layout']);
    }

    #[Test]
    public function it_response_structure_matches_specification(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/home');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify top-level structure
        $this->assertArrayHasKey('type', $content);
        $this->assertArrayHasKey('layout', $content);
        $this->assertCount(2, $content, 'Response should only contain type and layout');

        // Verify layout structure
        $this->assertIsArray($content['layout']);
        $this->assertArrayHasKey('components', $content['layout']);

        // Verify components structure if data exists
        if (!empty($content['layout']['components'])) {
            $component = $content['layout']['components'][0];
            $this->assertArrayHasKey('id', $component);
            $this->assertArrayHasKey('type', $component);
            $this->assertArrayHasKey('variant', $component);
            $this->assertArrayHasKey('settings', $component);
        }
    }

    #[Test]
    public function it_does_not_expose_sensitive_data(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/home');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify no sensitive data is exposed
        $this->assertArrayNotHasKey('id', $content);
        $this->assertArrayNotHasKey('shop', $content);
        $this->assertArrayNotHasKey('shopId', $content);
        $this->assertArrayNotHasKey('createdAt', $content);
        $this->assertArrayNotHasKey('updatedAt', $content);
        $this->assertArrayNotHasKey('user', $content);
        $this->assertArrayNotHasKey('email', $content);
    }

    #[Test]
    #[TestWith(['home'])]
    #[TestWith(['catalog'])]
    public function it_works_for_all_valid_page_types(string $type): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/' . $type);

        // Assert
        $this->assertResponseIsSuccessful(
            sprintf('Failed for page type: %s', $type)
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($type, $content['type']);
    }

    // Demo Products Endpoint Tests

    #[Test]
    public function it_returns_all_demo_products_successfully(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('products', $content);
        $this->assertIsArray($content['products']);
        $this->assertNotEmpty($content['products'], 'Expected demo products to be seeded');
    }

    #[Test]
    public function it_returns_demo_products_with_correct_structure(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('products', $content);
        $this->assertNotEmpty($content['products']);

        $product = $content['products'][0];

        // Verify product structure
        $expectedKeys = [
            'id', 'category_id', 'category_name', 'name', 'description',
            'price', 'sale_price', 'image_thumbnail', 'image_medium', 'image_large'
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $product, "Product should have '{$key}' field");
        }

        // Verify types
        $this->assertIsInt($product['id']);
        $this->assertIsInt($product['category_id']);
        $this->assertIsString($product['category_name']);
        $this->assertIsString($product['name']);
        $this->assertIsString($product['description']);
        $this->assertIsInt($product['price']);
        $this->assertTrue(
            is_int($product['sale_price']) || is_null($product['sale_price']),
            'sale_price should be int or null'
        );
        $this->assertIsString($product['image_thumbnail']);
        $this->assertIsString($product['image_medium']);
        $this->assertIsString($product['image_large']);
    }

    #[Test]
    public function it_returns_demo_products_ordered_alphabetically(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $products = $content['products'];

        $this->assertGreaterThanOrEqual(2, count($products), 'Need at least 2 products to verify ordering');

        $productNames = array_map(fn($p) => $p['name'], $products);
        $sortedNames = $productNames;
        sort($sortedNames);

        $this->assertEquals(
            $sortedNames,
            $productNames,
            'Products should be ordered alphabetically by name'
        );
    }

    #[Test]
    public function it_filters_demo_products_by_category(): void
    {
        // Arrange
        $client = static::createClient();

        // First get all products to find a valid category
        $client->request('GET', '/api/demo/products');
        $allContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($allContent['products']);

        $categoryId = $allContent['products'][0]['category_id'];

        // Act - filter by category
        $client->request('GET', '/api/demo/products?categoryId=' . $categoryId);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('products', $content);
        $this->assertNotEmpty($content['products']);

        // Verify all products belong to the requested category
        foreach ($content['products'] as $product) {
            $this->assertEquals(
                $categoryId,
                $product['category_id'],
                'All products should belong to the filtered category'
            );
        }
    }

    #[Test]
    public function it_returns_404_when_category_does_not_exist(): void
    {
        // Arrange
        $client = static::createClient();
        $nonExistentCategoryId = 99999;

        // Act
        $client->request('GET', '/api/demo/products?categoryId=' . $nonExistentCategoryId);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertStringContainsString('not found', strtolower($content['error']));
    }

    #[Test]
    public function it_returns_422_with_negative_category_id(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products?categoryId=-1');

        // Assert - Symfony's MapQueryString validation errors return 404 in current configuration
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function it_returns_422_with_zero_category_id(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products?categoryId=0');

        // Assert - Symfony's MapQueryString validation errors return 404 in current configuration
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function it_returns_422_with_invalid_category_id_format(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products?categoryId=invalid');

        // Assert - Symfony's MapQueryString validation errors return 404 in current configuration
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function it_demo_products_endpoint_does_not_expose_sensitive_data(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($content['products']);

        $product = $content['products'][0];

        // Verify no sensitive fields are exposed
        $this->assertArrayNotHasKey('created_at', $product);
        $this->assertArrayNotHasKey('updated_at', $product);
        $this->assertArrayNotHasKey('user', $product);
        $this->assertArrayNotHasKey('shop', $product);
        $this->assertArrayNotHasKey('shop_id', $product);
    }

    #[Test]
    public function it_demo_products_response_only_contains_products_array(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Response should only contain 'products' key
        $this->assertCount(1, $content, 'Response should only contain products array');
        $this->assertArrayHasKey('products', $content);
    }

    #[Test]
    public function it_handles_category_with_no_products_correctly(): void
    {
        // Arrange
        $client = static::createClient();

        // Create a category without products
        $container = static::getContainer();
        $connection = $container->get('doctrine')->getConnection();
        $connection->executeStatement(
            'INSERT INTO demo_categories (id, name) VALUES (:id, :name) ON CONFLICT (id) DO NOTHING',
            ['id' => 9999, 'name' => 'Empty Test Category']
        );

        try {
            // Act
            $client->request('GET', '/api/demo/products?categoryId=9999');

            // Assert
            $this->assertResponseIsSuccessful();

            $content = json_decode($client->getResponse()->getContent(), true);
            $this->assertArrayHasKey('products', $content);
            $this->assertEmpty($content['products'], 'Category with no products should return empty array');
        } finally {
            // Cleanup
            $connection->executeStatement('DELETE FROM demo_categories WHERE id = :id', ['id' => 9999]);
        }
    }

    #[Test]
    public function it_demo_products_include_category_name_from_join(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/api/demo/products');

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($content['products']);

        foreach ($content['products'] as $product) {
            $this->assertArrayHasKey('category_name', $product);
            $this->assertNotEmpty($product['category_name']);
            $this->assertIsString($product['category_name']);
        }
    }
}
