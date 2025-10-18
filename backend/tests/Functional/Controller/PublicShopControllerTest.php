<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

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
final class PublicShopControllerTest extends WebTestCase
{
    private const VALID_SHOP_ID = '550e8400-e29b-41d4-a716-446655440000';
    private const NON_EXISTENT_SHOP_ID = '00000000-0000-0000-0000-000000000000';

    public function testGetPageReturnsSuccessResponseWithValidShopAndPageType(): void
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

    public function testGetPageReturnsNotFoundWhenShopDoesNotExist(): void
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

    public function testGetPageReturnsNotFoundWhenPageDoesNotExistForShop(): void
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

    public function testGetPageReturnsServerErrorWithInvalidUuidFormat(): void
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

    public function testGetPageReturnsServerErrorWithInvalidPageType(): void
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

    public function testGetPageDefaultsToHomeWhenTypeNotProvided(): void
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

    public function testGetPageReturnsCorrectDataForCatalogPage(): void
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

    public function testGetPageResponseStructureMatchesSpecification(): void
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

    public function testGetPageDoesNotExposesSensitiveData(): void
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

    public function testGetPageWorksForAllValidPageTypes(): void
    {
        // Arrange
        $client = static::createClient();
        $validTypes = ['home', 'catalog'];

        foreach ($validTypes as $type) {
            // Act
            $client->request('GET', '/api/public/shops/' . self::VALID_SHOP_ID . '/pages/' . $type);

            // Assert
            $this->assertResponseIsSuccessful(
                sprintf('Failed for page type: %s', $type)
            );

            $content = json_decode($client->getResponse()->getContent(), true);
            $this->assertEquals($type, $content['type']);
        }
    }
}
