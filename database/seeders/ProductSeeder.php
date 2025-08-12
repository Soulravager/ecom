<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => '15-inch laptop with 16GB RAM',
            'price' => 85000,
            'stock' => 10
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest 5G Android phone',
            'price' => 35000,
            'stock' => 25
        ]);

        Product::create([
            'name' => 'Wireless Mouse',
            'description' => 'Bluetooth ergonomic mouse',
            'price' => 1200,
            'stock' => 50
        ]);
    }
}
