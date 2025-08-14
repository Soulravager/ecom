<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => ' ITEM 1',
            'description' => 'description description description.',
            'price' => 1999,
            'stock' => 50,
            'image' => 'products/1.jpg'
        ]);

        Product::create([
            'name' => 'ITEM 2',
            'description' => 'description description description.',
            'price' => 9999,
            'stock' => 30,
            'image' => 'products/2.jpg'
        ]);

        Product::create([
            'name' => 'ITEM 3',
            'description' => 'description description description.',
            'price' => 777,
            'stock' => 100,
            'image' => 'products/3.jpg'
        ]);
    }
}
