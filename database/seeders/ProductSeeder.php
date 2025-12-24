<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop', 'description' => 'High performance laptop', 'price' => 999.99],
            ['name' => 'Smartphone', 'description' => 'Latest smartphone model', 'price' => 699.99],
            ['name' => 'Headphones', 'description' => 'Wireless noise-cancelling', 'price' => 199.99],
            ['name' => 'Keyboard', 'description' => 'Mechanical gaming keyboard', 'price' => 129.99],
            ['name' => 'Mouse', 'description' => 'Ergonomic wireless mouse', 'price' => 49.99],
            ['name' => 'Monitor', 'description' => '27-inch 4K display', 'price' => 399.99],
            ['name' => 'Tablet', 'description' => 'Drawing tablet with stylus', 'price' => 299.99],
            ['name' => 'Smartwatch', 'description' => 'Fitness tracking watch', 'price' => 249.99],
        ];

        foreach ($products as $index => $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sort_order' => $index + 1,
                'is_active' => true
            ]);
        }
    }
}