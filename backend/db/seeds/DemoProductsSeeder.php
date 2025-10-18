<?php

use Phinx\Seed\AbstractSeed;

/**
 * Seeder for demo products and categories data.
 *
 * Populates the demo_categories and demo_products tables with sample data
 * for testing and demonstration purposes in the Demo Shop.
 */
class DemoProductsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Seeds demo categories and products with realistic e-commerce data.
     * Products include varied prices, categories, and sale prices to demonstrate
     * different scenarios.
     */
    public function run(): void
    {
        // Insert demo categories
        $this->table('demo_categories')->insert([
            ['id' => 1, 'name' => 'Electronics'],
            ['id' => 2, 'name' => 'Clothing'],
            ['id' => 3, 'name' => 'Books'],
            ['id' => 4, 'name' => 'Home & Garden'],
        ])->saveData();

        // Insert demo products
        $this->table('demo_products')->insert([
            // Electronics
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Premium Wireless Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation, 30-hour battery life, and premium sound quality. Perfect for music lovers and professionals.',
                'price' => 29999, // $299.99
                'sale_price' => 24999, // $249.99
                'image_thumbnail' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1200',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Smart Watch Pro',
                'description' => 'Feature-rich smartwatch with heart rate monitoring, GPS, and water resistance. Stay connected and track your fitness goals.',
                'price' => 39999, // $399.99
                'sale_price' => null,
                'image_thumbnail' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1200',
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'Ultra HD Webcam',
                'description' => '4K webcam with auto-focus and noise-canceling microphone. Perfect for video conferencing and content creation.',
                'price' => 12999, // $129.99
                'sale_price' => 9999, // $99.99
                'image_thumbnail' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=1200',
            ],

            // Clothing
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Classic Denim Jacket',
                'description' => 'Timeless denim jacket made from premium cotton. A versatile piece that goes with any outfit.',
                'price' => 7999, // $79.99
                'sale_price' => null,
                'image_thumbnail' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=1200',
            ],
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'Cotton T-Shirt Pack',
                'description' => 'Pack of 3 premium cotton t-shirts in assorted colors. Comfortable, breathable, and perfect for everyday wear.',
                'price' => 2999, // $29.99
                'sale_price' => 2499, // $24.99
                'image_thumbnail' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=1200',
            ],

            // Books
            [
                'id' => 6,
                'category_id' => 3,
                'name' => 'Advanced JavaScript Patterns',
                'description' => 'Comprehensive guide to modern JavaScript design patterns and best practices. Perfect for intermediate to advanced developers.',
                'price' => 4999, // $49.99
                'sale_price' => null,
                'image_thumbnail' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=1200',
            ],
            [
                'id' => 7,
                'category_id' => 3,
                'name' => 'Clean Architecture',
                'description' => 'A craftsman\'s guide to software structure and design. Learn how to build maintainable and scalable applications.',
                'price' => 3999, // $39.99
                'sale_price' => 3499, // $34.99
                'image_thumbnail' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1589998059171-988d887df646?w=1200',
            ],
            [
                'id' => 8,
                'category_id' => 3,
                'name' => 'Domain-Driven Design',
                'description' => 'Essential reading for building complex software systems. Learn how to tackle complexity in the heart of software.',
                'price' => 5499, // $54.99
                'sale_price' => null,
                'image_thumbnail' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=1200',
            ],

            // Home & Garden
            [
                'id' => 9,
                'category_id' => 4,
                'name' => 'Ceramic Plant Pot Set',
                'description' => 'Set of 3 handcrafted ceramic plant pots with drainage holes. Perfect for indoor plants and succulents.',
                'price' => 3499, // $34.99
                'sale_price' => 2999, // $29.99
                'image_thumbnail' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=1200',
            ],
            [
                'id' => 10,
                'category_id' => 4,
                'name' => 'LED Garden Lights',
                'description' => 'Solar-powered LED garden lights. Eco-friendly lighting solution for pathways and landscaping.',
                'price' => 4999, // $49.99
                'sale_price' => null,
                'image_thumbnail' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=200',
                'image_medium' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=600',
                'image_large' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=1200',
            ],
        ])->saveData();
    }
}
