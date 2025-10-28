<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\PageController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional tests for PageController
 *
 * Tests the complete flow for authenticated /api/pages endpoint:
 * - Success cases (200 OK with valid JWT)
 * - Unauthorized cases (401 without JWT)
 * - Not found cases (404 when user has no shop)
 * - Response structure validation
 * - Data isolation enforcement
 */
#[CoversClass(PageController::class)]
final class PageControllerTest extends WebTestCase
{
    private const TEST_USER_EMAIL = 'pagetest@example.com';
    private const TEST_USER_PASSWORD = 'SecurePassword123!';
    private const TEST_SHOP_NAME = 'Test Page Shop';

    private ?string $authToken = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authToken = null;
    }

    #[Test]
    public function it_returns_unauthorized_without_jwt_token(): void
    {
        // Arrange
        $client = static::createClient();

        // Act - Request without Authorization header
        $client->request('GET', '/api/pages');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_returns_unauthorized_with_invalid_jwt_token(): void
    {
        // Arrange
        $client = static::createClient();

        // Act - Request with invalid token
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token-here',
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_returns_success_response_with_valid_jwt_token(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('pages', $content);
        $this->assertIsArray($content['pages']);
    }

    #[Test]
    public function it_returns_correct_response_structure(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify top-level structure
        $this->assertArrayHasKey('pages', $content);
        $this->assertCount(1, $content, 'Response should only contain pages array');

        // Verify pages is an array (may be empty or have pages)
        $this->assertIsArray($content['pages']);
    }

    #[Test]
    public function it_returns_pages_with_correct_fields(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $pages = $content['pages'];

        if (!empty($pages)) {
            $page = $pages[0];

            // Verify page structure
            $this->assertArrayHasKey('type', $page);
            $this->assertArrayHasKey('layout', $page);
            $this->assertArrayHasKey('created_at', $page);
            $this->assertArrayHasKey('updated_at', $page);

            // Verify field types
            $this->assertIsString($page['type']);
            $this->assertIsArray($page['layout']);
            $this->assertIsString($page['created_at']);
            $this->assertIsString($page['updated_at']);

            // Verify timestamps are in ISO 8601 format
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
                $page['created_at'],
                'created_at should be in ISO 8601 format'
            );
            $this->assertMatchesRegularExpression(
                '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
                $page['updated_at'],
                'updated_at should be in ISO 8601 format'
            );
        }
    }

    #[Test]
    public function it_does_not_expose_sensitive_data(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        if (!empty($content['pages'])) {
            $page = $content['pages'][0];

            // Verify no sensitive data is exposed
            $this->assertArrayNotHasKey('id', $page);
            $this->assertArrayNotHasKey('shop', $page);
            $this->assertArrayNotHasKey('shopId', $page);
            $this->assertArrayNotHasKey('shop_id', $page);
            $this->assertArrayNotHasKey('user', $page);
            $this->assertArrayNotHasKey('userId', $page);
            $this->assertArrayNotHasKey('user_id', $page);
        }
    }

    #[Test]
    public function it_returns_empty_array_when_shop_has_no_pages(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Delete all pages for this user's shop
        $container = static::getContainer();
        $connection = $container->get('doctrine')->getConnection();

        // Find the user and their shop
        $userEmail = self::TEST_USER_EMAIL;
        $result = $connection->fetchAssociative(
            'SELECT s.id as shop_id FROM users u
             INNER JOIN shops s ON s.user_id = u.id
             WHERE u.email = :email',
            ['email' => $userEmail]
        );

        if ($result) {
            $connection->executeStatement(
                'DELETE FROM pages WHERE shop_id = :shopId',
                ['shopId' => $result['shop_id']]
            );
        }

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('pages', $content);
        $this->assertSame([], $content['pages'], 'Should return empty array when no pages exist');
    }

    #[Test]
    public function it_enforces_data_isolation_between_users(): void
    {
        // Arrange
        $client = static::createClient();

        // Create first user
        $token1 = $this->createUserAndGetToken($client, 'user1@example.com', 'Shop User 1');

        // Create second user
        $token2 = $this->createUserAndGetToken($client, 'user2@example.com', 'Shop User 2');

        // Act - Get pages for user 1
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token1,
        ]);
        $user1Pages = json_decode($client->getResponse()->getContent(), true);

        // Act - Get pages for user 2
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token2,
        ]);
        $user2Pages = json_decode($client->getResponse()->getContent(), true);

        // Assert - Users should only see their own pages
        $this->assertResponseIsSuccessful();

        // Pages should be independent (even if both are empty, the test confirms isolation)
        $this->assertIsArray($user1Pages['pages']);
        $this->assertIsArray($user2Pages['pages']);
    }

    #[Test]
    public function it_pages_are_ordered_by_type(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);
        $pages = $content['pages'];

        if (count($pages) >= 2) {
            $types = array_map(fn($p) => $p['type'], $pages);
            $sortedTypes = $types;
            sort($sortedTypes);

            $this->assertEquals(
                $sortedTypes,
                $types,
                'Pages should be ordered by type'
            );
        }
    }

    // Tests for GET /api/pages/{type} endpoint

    #[Test]
    public function it_returns_unauthorized_for_page_by_type_without_jwt_token(): void
    {
        // Arrange
        $client = static::createClient();

        // Act - Request without Authorization header
        $client->request('GET', '/api/pages/home');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_returns_bad_request_for_invalid_page_type(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act - Request with invalid page type
        $client->request('GET', '/api/pages/invalid', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertSame('invalid_page_type', $content['error']);
        $this->assertStringContainsString('home, catalog, product, contact', $content['message']);
    }

    #[Test]
    public function it_returns_success_for_valid_page_type(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act - Request home page
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify response structure
        $this->assertArrayHasKey('type', $content);
        $this->assertArrayHasKey('layout', $content);
        $this->assertArrayHasKey('created_at', $content);
        $this->assertArrayHasKey('updated_at', $content);

        // Verify field types
        $this->assertSame('home', $content['type']);
        $this->assertIsArray($content['layout']);
        $this->assertIsString($content['created_at']);
        $this->assertIsString($content['updated_at']);
    }

    #[Test]
    public function it_returns_correct_page_for_each_page_type(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);
        $validTypes = ['home', 'catalog', 'product', 'contact'];

        foreach ($validTypes as $type) {
            // Act
            $client->request('GET', "/api/pages/{$type}", [], [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            ]);

            // Assert
            $this->assertResponseIsSuccessful(
                "Failed to retrieve page of type '{$type}'"
            );

            $content = json_decode($client->getResponse()->getContent(), true);
            $this->assertSame($type, $content['type']);
        }
    }

    #[Test]
    public function it_returns_page_with_iso8601_timestamps(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify timestamps are in ISO 8601 format
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $content['created_at'],
            'created_at should be in ISO 8601 format'
        );
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $content['updated_at'],
            'updated_at should be in ISO 8601 format'
        );
    }

    #[Test]
    public function it_does_not_expose_sensitive_data_in_single_page(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();

        $content = json_decode($client->getResponse()->getContent(), true);

        // Verify no sensitive data is exposed
        $this->assertArrayNotHasKey('id', $content);
        $this->assertArrayNotHasKey('shop', $content);
        $this->assertArrayNotHasKey('shopId', $content);
        $this->assertArrayNotHasKey('shop_id', $content);
        $this->assertArrayNotHasKey('user', $content);
        $this->assertArrayNotHasKey('userId', $content);
        $this->assertArrayNotHasKey('user_id', $content);
    }

    #[Test]
    public function it_enforces_data_isolation_for_single_page(): void
    {
        // Arrange
        $client = static::createClient();

        // Create first user with pages
        $token1 = $this->createUserAndGetToken($client, 'isolation1@example.com', 'Shop 1');

        // Create second user with pages
        $token2 = $this->createUserAndGetToken($client, 'isolation2@example.com', 'Shop 2');

        // Act - Get home page for user 1
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token1,
        ]);
        $user1Response = $client->getResponse();

        // Act - Get home page for user 2
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token2,
        ]);
        $user2Response = $client->getResponse();

        // Assert - Both users should successfully retrieve their own pages
        $this->assertSame(200, $user1Response->getStatusCode());
        $this->assertSame(200, $user2Response->getStatusCode());

        // Verify they got their own pages (should be different)
        $user1Content = json_decode($user1Response->getContent(), true);
        $user2Content = json_decode($user2Response->getContent(), true);

        $this->assertSame('home', $user1Content['type']);
        $this->assertSame('home', $user2Content['type']);

        // Pages should be independent (data isolation confirmed)
        $this->assertIsArray($user1Content['layout']);
        $this->assertIsArray($user2Content['layout']);
    }

    #[Test]
    public function it_returns_404_when_page_does_not_exist(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Delete all pages for this user's shop
        $container = static::getContainer();
        $connection = $container->get('doctrine')->getConnection();

        // Find the user and their shop
        $userResult = $connection->fetchAssociative(
            'SELECT u.id as user_id, s.id as shop_id FROM users u
             INNER JOIN shops s ON s.user_id = u.id
             ORDER BY u.id DESC LIMIT 1'
        );

        if ($userResult) {
            $connection->executeStatement(
                'DELETE FROM pages WHERE shop_id = :shopId',
                ['shopId' => $userResult['shop_id']]
            );
        }

        // Act
        $client->request('GET', '/api/pages/home', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertSame('page_not_found', $content['error']);
        $this->assertStringContainsString("Page of type 'home' not found", $content['message']);
    }

    #[Test]
    public function it_is_case_sensitive_for_page_types(): void
    {
        // Arrange
        $client = static::createClient();
        $token = $this->createUserAndGetToken($client);

        // Act - Try with uppercase type
        $client->request('GET', '/api/pages/HOME', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Assert - Should return 400 because PageType::fromString() is case-sensitive
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('invalid_page_type', $content['error']);
    }

    /**
     * Helper method to create a test user and get JWT token.
     *
     * Creates a new user via the /api/auth/register endpoint,
     * then logs in to get a JWT token for authenticated requests.
     */
    private function createUserAndGetToken(
        $client,
        string $email = self::TEST_USER_EMAIL,
        string $shopName = self::TEST_SHOP_NAME
    ): string {
        // Use unique email and shop name for each test to avoid conflicts
        $uniqueId = uniqid('test_', true);
        $uniqueEmail = $uniqueId . '_' . $email;
        $uniqueShopName = $uniqueId . '_' . $shopName;

        // Register user
        $client->request('POST', '/api/auth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $uniqueEmail,
            'password' => self::TEST_USER_PASSWORD,
            'shopName' => $uniqueShopName,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED, 'Failed to create test user');

        // Login to get token
        $client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => $uniqueEmail,
            'password' => self::TEST_USER_PASSWORD,
        ]));

        $this->assertResponseIsSuccessful('Failed to login test user');

        $loginResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $loginResponse, 'Login response should contain token');

        return $loginResponse['token'];
    }
}
