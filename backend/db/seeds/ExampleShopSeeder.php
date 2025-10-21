<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Symfony\Component\Uid\Uuid;

/**
 * Seeder for example shop with user and page layouts.
 *
 * Creates a single example shop with:
 * - One user (demo@example.com)
 * - One shop (Example Store)
 * - Four pages (home, catalog, product, contact) each with a HeaderNavigation component
 */
class ExampleShopSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Seeds an example shop with default page layouts featuring HeaderNavigation components.
     */
    public function run(): void
    {
        // Generate UUIDs for the entities
        $userId = Uuid::v4()->toRfc4122();
        $shopId = Uuid::v4()->toRfc4122();

        // Hash the password using PHP's password_hash function
        $hashedPassword = password_hash('test123', PASSWORD_DEFAULT);

        // Get current timestamp
        $now = date('Y-m-d H:i:s');

        // Insert example user
        $this->table('users')->insert([
            [
                'id' => $userId,
                'email' => 'demo@example.com',
                'password' => $hashedPassword,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ])->saveData();

        // Insert example shop
        $this->table('shops')->insert([
            [
                'id' => $shopId,
                'user_id' => $userId,
                'name' => 'Example Store',
                'theme_settings' => json_encode(new stdClass()), // Empty object
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ])->saveData();

        // Define the HeaderNavigation component configuration
        $headerComponent = [
            'type' => 'HeaderNavigation',
            'variant' => 'static',
            'props' => [
                'logoUrl' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=200',
                'logoPosition' => 'center',
            ],
        ];

        // Define page types
        $pageTypes = ['home', 'catalog', 'product', 'contact'];

        // Create pages with HeaderNavigation component
        $pages = [];
        foreach ($pageTypes as $pageType) {
            $pages[] = [
                'id' => Uuid::v4()->toRfc4122(),
                'shop_id' => $shopId,
                'type' => $pageType,
                'layout' => json_encode([$headerComponent]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert all pages
        $this->table('pages')->insert($pages)->saveData();
    }
}
