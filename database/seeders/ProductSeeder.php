<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name'  => 'Laptop Gaming',
                'code'  => 'LAP001',
                'price' => 12000000,
                'stock' => 10,
            ],
            [
                'name'  => 'Smartphone',
                'code'  => 'SPH001',
                'price' => 5000000,
                'stock' => 25,
            ],
            [
                'name'  => 'Mouse Wireless',
                'code'  => 'MSE001',
                'price' => 250000,
                'stock' => 50,
            ],
            [
                'name'  => 'Keyboard Mechanical',
                'code'  => 'KBD001',
                'price' => 800000,
                'stock' => 30,
            ],
            [
                'name'  => 'Monitor 24 inch',
                'code'  => 'MON001',
                'price' => 2000000,
                'stock' => 15,
            ],
            [
                'name'  => 'Headphones',
                'code'  => 'HPH001',
                'price' => 600000,
                'stock' => 40,
            ],
            [
                'name'  => 'USB Cable',
                'code'  => 'USB001',
                'price' => 75000,
                'stock' => 100,
            ],
            [
                'name'  => 'External Hard Drive',
                'code'  => 'HDD001',
                'price' => 1200000,
                'stock' => 20,
            ],
            [
                'name'  => 'Tablet',
                'code'  => 'TAB001',
                'price' => 3500000,
                'stock' => 12,
            ],
            [
                'name'  => 'Printer',
                'code'  => 'PRN001',
                'price' => 1800000,
                'stock' => 8,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
