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
            'id' => Uuid::v4()->toRfc4122(),
            'type' => 'HeaderNavigation',
            'variant' => 'static',
            'props' => [
                'logoUrl' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=200',
                'logoPosition' => 'center',
                'variant' => 'static',
            ],
        ];

        // Define page-specific configurations
        $pageConfigurations = [
            'home' => [
                'heading1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-image',
                    'props' => [
                        'text' => 'Home',
                        'level' => 'h1',
                        'variant' => 'background-image',
                        'backgroundImageUrl' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200',
                        'textColor' => '#ffffff',
                        'height' => 400,
                    ],
                ],
                'textSection1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'text-only',
                    'props' => [
                        'variant' => 'text-only',
                        'columnCount' => 1,
                        'columns' => [
                            [
                                'text' => 'Welcome to our store! We are passionate about bringing you the finest selection of curated products that enhance your everyday life. Our commitment to quality and customer satisfaction drives everything we do.',
                            ],
                        ],
                    ],
                ],
                'heading2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'text-only',
                    'props' => [
                        'text' => 'Why Choose Us',
                        'level' => 'h2',
                        'variant' => 'text-only',
                    ],
                ],
                'textSection2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'with-icons',
                    'props' => [
                        'variant' => 'with-icons',
                        'columnCount' => 3,
                        'columns' => [
                            [
                                'text' => 'Premium quality products sourced from trusted suppliers worldwide.',
                                'iconUrl' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=100',
                            ],
                            [
                                'text' => 'Fast and reliable shipping with real-time tracking for your peace of mind.',
                                'iconUrl' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=100',
                            ],
                            [
                                'text' => 'Dedicated customer support team ready to assist you every step of the way.',
                                'iconUrl' => 'https://images.unsplash.com/photo-1553835973-dec43bfddbeb?w=100',
                            ],
                        ],
                    ],
                ],
            ],
            'catalog' => [
                'heading1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-color',
                    'props' => [
                        'text' => 'Catalog',
                        'level' => 'h1',
                        'variant' => 'background-color',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'textColor' => '#1e40af',
                        'height' => 250,
                    ],
                ],
                'textSection1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'text-only',
                    'props' => [
                        'variant' => 'text-only',
                        'columnCount' => 1,
                        'columns' => [
                            [
                                'text' => 'Browse our extensive collection of thoughtfully selected products. Each item has been carefully chosen to meet our high standards of quality and design.',
                            ],
                        ],
                    ],
                ],
                'heading2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-color',
                    'props' => [
                        'text' => 'Featured Collections',
                        'level' => 'h2',
                        'variant' => 'background-color',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'textColor' => '#065f46',
                        'height' => 200,
                    ],
                ],
                'textSection2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'with-images',
                    'props' => [
                        'variant' => 'with-images',
                        'columnCount' => 2,
                        'columns' => [
                            [
                                'text' => 'New Arrivals - Discover the latest additions to our collection, featuring cutting-edge designs and innovative solutions.',
                                'imageUrl' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400',
                            ],
                            [
                                'text' => 'Best Sellers - Our most popular items, loved by customers for their exceptional quality and value.',
                                'imageUrl' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400',
                            ],
                        ],
                    ],
                ],
            ],
            'product' => [
                'heading1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'text-only',
                    'props' => [
                        'text' => 'Product',
                        'level' => 'h1',
                        'variant' => 'text-only',
                    ],
                ],
                'textSection1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'text-only',
                    'props' => [
                        'variant' => 'text-only',
                        'columnCount' => 1,
                        'columns' => [
                            [
                                'text' => 'Every product in our store comes with detailed specifications, customer reviews, and expert recommendations to help you make informed decisions.',
                            ],
                        ],
                    ],
                ],
                'heading2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-image',
                    'props' => [
                        'text' => 'Product Features',
                        'level' => 'h2',
                        'variant' => 'background-image',
                        'backgroundImageUrl' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1200',
                        'textColor' => '#ffffff',
                        'height' => 300,
                    ],
                ],
                'textSection2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'text-only',
                    'props' => [
                        'variant' => 'text-only',
                        'columnCount' => 4,
                        'columns' => [
                            [
                                'text' => 'Durable materials built to last for years of reliable use.',
                            ],
                            [
                                'text' => 'Ergonomic design crafted for maximum comfort and efficiency.',
                            ],
                            [
                                'text' => 'Eco-friendly production with sustainable practices and materials.',
                            ],
                            [
                                'text' => 'Warranty included for your complete confidence and protection.',
                            ],
                        ],
                    ],
                ],
            ],
            'contact' => [
                'heading1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-image',
                    'props' => [
                        'text' => 'Contact',
                        'level' => 'h1',
                        'variant' => 'background-image',
                        'backgroundImageUrl' => 'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1200',
                        'textColor' => '#ffffff',
                        'height' => 350,
                    ],
                ],
                'textSection1' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'text-only',
                    'props' => [
                        'variant' => 'text-only',
                        'columnCount' => 1,
                        'columns' => [
                            [
                                'text' => 'Have questions or need assistance? Our friendly support team is here to help! Reach out to us through any of the channels below.',
                            ],
                        ],
                    ],
                ],
                'heading2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'Heading',
                    'variant' => 'background-color',
                    'props' => [
                        'text' => 'Get In Touch',
                        'level' => 'h3',
                        'variant' => 'background-color',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'textColor' => '#92400e',
                        'height' => 180,
                    ],
                ],
                'textSection2' => [
                    'id' => Uuid::v4()->toRfc4122(),
                    'type' => 'TextSection',
                    'variant' => 'with-icons',
                    'props' => [
                        'variant' => 'with-icons',
                        'columnCount' => 2,
                        'columns' => [
                            [
                                'text' => 'Email us at support@example.com and we will respond within 24 hours on business days.',
                                'iconUrl' => 'https://images.unsplash.com/photo-1596526131083-e8c633c948d2?w=100',
                            ],
                            [
                                'text' => 'Call our hotline at +1 (555) 123-4567 Monday through Friday, 9 AM to 6 PM EST.',
                                'iconUrl' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=100',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Create pages with all components
        $pages = [];
        foreach ($pageConfigurations as $pageType => $components) {
            $layout = [
                $headerComponent,
                $components['heading1'],
                $components['textSection1'],
                $components['heading2'],
                $components['textSection2'],
            ];

            $pages[] = [
                'id' => Uuid::v4()->toRfc4122(),
                'shop_id' => $shopId,
                'type' => $pageType,
                'layout' => json_encode($layout),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert all pages
        $this->table('pages')->insert($pages)->saveData();
    }
}
