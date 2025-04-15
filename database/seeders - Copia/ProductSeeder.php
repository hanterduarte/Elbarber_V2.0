<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pomada Modeladora',
                'description' => 'Pomada modeladora para cabelo',
                'price' => 45.00,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Óleo para Barba',
                'description' => 'Óleo hidratante para barba',
                'price' => 35.00,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Shampoo Masculino',
                'description' => 'Shampoo especial para homens',
                'price' => 30.00,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Balm para Barba',
                'description' => 'Balm hidratante para barba',
                'price' => 40.00,
                'stock' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'Pente Profissional',
                'description' => 'Pente profissional para barbeiros',
                'price' => 25.00,
                'stock' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 