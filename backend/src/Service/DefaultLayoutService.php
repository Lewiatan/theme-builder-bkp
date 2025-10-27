<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Enum\PageType;
use App\Model\ValueObject\ComponentDefinition;
use App\Model\ValueObject\Layout;

/**
 * Provides default page layouts for new shops.
 *
 * Layouts match the structure defined in ExampleShopSeeder.php
 * to ensure consistency across demo shops and newly registered users.
 */
final readonly class DefaultLayoutService
{
    public function getDefaultLayout(PageType $pageType): Layout
    {
        return match($pageType) {
            PageType::HOME => $this->getHomeLayout(),
            PageType::CATALOG => $this->getCatalogLayout(),
            PageType::PRODUCT => $this->getProductLayout(),
            PageType::CONTACT => $this->getContactLayout(),
        };
    }

    private function getHeaderComponent(string $idSuffix): ComponentDefinition
    {
        return ComponentDefinition::fromArray([
            'id' => 'default-header-' . $idSuffix,
            'type' => 'HeaderNavigation',
            'variant' => 'static',
            'settings' => [
                'logoUrl' => 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=200',
                'logoPosition' => 'center',
                'variant' => 'static',
            ],
        ]);
    }

    private function getHomeLayout(): Layout
    {
        $components = [
            $this->getHeaderComponent('home'),
            ComponentDefinition::fromArray([
                'id' => 'default-heading1-home',
                'type' => 'Heading',
                'variant' => 'background-image',
                'settings' => [
                    'text' => 'Home',
                    'level' => 'h1',
                    'variant' => 'background-image',
                    'backgroundImageUrl' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200',
                    'textColor' => '#ffffff',
                    'height' => 400,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection1-home',
                'type' => 'TextSection',
                'variant' => 'text-only',
                'settings' => [
                    'variant' => 'text-only',
                    'columnCount' => 1,
                    'columns' => [
                        [
                            'text' => 'Welcome to our store! We are passionate about bringing you the finest selection of curated products that enhance your everyday life. Our commitment to quality and customer satisfaction drives everything we do.',
                        ],
                    ],
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-heading2-home',
                'type' => 'Heading',
                'variant' => 'text-only',
                'settings' => [
                    'text' => 'Why Choose Us',
                    'level' => 'h2',
                    'variant' => 'text-only',
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection2-home',
                'type' => 'TextSection',
                'variant' => 'with-icons',
                'settings' => [
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
            ]),
        ];

        return new Layout($components);
    }

    private function getCatalogLayout(): Layout
    {
        $components = [
            $this->getHeaderComponent('catalog'),
            ComponentDefinition::fromArray([
                'id' => 'default-heading1-catalog',
                'type' => 'Heading',
                'variant' => 'background-color',
                'settings' => [
                    'text' => 'Catalog',
                    'level' => 'h1',
                    'variant' => 'background-color',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'textColor' => '#1e40af',
                    'height' => 250,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-categorypills-catalog',
                'type' => 'CategoryPills',
                'variant' => 'center',
                'settings' => [
                    'variant' => 'center',
                    'showAllOption' => true,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-productlistgrid-catalog',
                'type' => 'ProductListGrid',
                'variant' => '3',
                'settings' => [
                    'productsPerRow' => 3,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection1-catalog',
                'type' => 'TextSection',
                'variant' => 'text-only',
                'settings' => [
                    'variant' => 'text-only',
                    'columnCount' => 1,
                    'columns' => [
                        [
                            'text' => 'Browse our extensive collection of thoughtfully selected products. Each item has been carefully chosen to meet our high standards of quality and design.',
                        ],
                    ],
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-heading2-catalog',
                'type' => 'Heading',
                'variant' => 'background-color',
                'settings' => [
                    'text' => 'Featured Collections',
                    'level' => 'h2',
                    'variant' => 'background-color',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'textColor' => '#065f46',
                    'height' => 200,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection2-catalog',
                'type' => 'TextSection',
                'variant' => 'with-images',
                'settings' => [
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
            ]),
        ];

        return new Layout($components);
    }

    private function getProductLayout(): Layout
    {
        $components = [
            $this->getHeaderComponent('product'),
            ComponentDefinition::fromArray([
                'id' => 'default-heading1-product',
                'type' => 'Heading',
                'variant' => 'text-only',
                'settings' => [
                    'text' => 'Product',
                    'level' => 'h1',
                    'variant' => 'text-only',
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection1-product',
                'type' => 'TextSection',
                'variant' => 'text-only',
                'settings' => [
                    'variant' => 'text-only',
                    'columnCount' => 1,
                    'columns' => [
                        [
                            'text' => 'Every product in our store comes with detailed specifications, customer reviews, and expert recommendations to help you make informed decisions.',
                        ],
                    ],
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-heading2-product',
                'type' => 'Heading',
                'variant' => 'background-image',
                'settings' => [
                    'text' => 'Product Features',
                    'level' => 'h2',
                    'variant' => 'background-image',
                    'backgroundImageUrl' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=1200',
                    'textColor' => '#ffffff',
                    'height' => 300,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection2-product',
                'type' => 'TextSection',
                'variant' => 'text-only',
                'settings' => [
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
            ]),
        ];

        return new Layout($components);
    }

    private function getContactLayout(): Layout
    {
        $components = [
            $this->getHeaderComponent('contact'),
            ComponentDefinition::fromArray([
                'id' => 'default-heading1-contact',
                'type' => 'Heading',
                'variant' => 'background-image',
                'settings' => [
                    'text' => 'Contact',
                    'level' => 'h1',
                    'variant' => 'background-image',
                    'backgroundImageUrl' => 'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1200',
                    'textColor' => '#ffffff',
                    'height' => 350,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection1-contact',
                'type' => 'TextSection',
                'variant' => 'text-only',
                'settings' => [
                    'variant' => 'text-only',
                    'columnCount' => 1,
                    'columns' => [
                        [
                            'text' => 'Have questions or need assistance? Our friendly support team is here to help! Reach out to us through any of the channels below.',
                        ],
                    ],
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-heading2-contact',
                'type' => 'Heading',
                'variant' => 'background-color',
                'settings' => [
                    'text' => 'Get In Touch',
                    'level' => 'h3',
                    'variant' => 'background-color',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'textColor' => '#92400e',
                    'height' => 180,
                ],
            ]),
            ComponentDefinition::fromArray([
                'id' => 'default-textsection2-contact',
                'type' => 'TextSection',
                'variant' => 'with-icons',
                'settings' => [
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
            ]),
        ];

        return new Layout($components);
    }
}
