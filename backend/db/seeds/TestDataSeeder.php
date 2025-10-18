<?php

use Phinx\Seed\AbstractSeed;

class TestDataSeeder extends AbstractSeed
{
    public function run(): void
    {
        // Insert test user
        $this->table('users')->insert([
            [
                'id' => '450e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ])->saveData();

        // Insert test shop
        $this->table('shops')->insert([
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'user_id' => '450e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test Shop',
                'theme_settings' => json_encode(['colors' => ['primary' => '#000000']]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ])->saveData();

        // Insert test pages
        $this->table('pages')->insert([
            [
                'id' => '650e8400-e29b-41d4-a716-446655440001',
                'shop_id' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'home',
                'layout' => json_encode([
                    ['id' => 'cmp_001', 'type' => 'hero', 'variant' => 'default', 'settings' => []],
                    ['id' => 'cmp_002', 'type' => 'features', 'variant' => 'grid', 'settings' => []],
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '650e8400-e29b-41d4-a716-446655440002',
                'shop_id' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'catalog',
                'layout' => json_encode([
                    ['id' => 'cmp_003', 'type' => 'product-grid', 'variant' => 'default', 'settings' => []],
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ])->saveData();
    }
}
