<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Produtos fixos
        $fixedProducts = [
            [
                'name' => 'Pomada Modeladora',
                'description' => 'Pomada para modelar cabelo',
                'price' => 25.00,
                'stock' => 50,
                'min_stock' => 10,
                'sku' => 'POM001',
                'barcode' => '7891234567890',
                'is_active' => true,
            ],
            [
                'name' => 'Shampoo Anticaspa',
                'description' => 'Shampoo para tratamento de caspa',
                'price' => 35.00,
                'stock' => 30,
                'min_stock' => 5,
                'sku' => 'SHA002',
                'barcode' => '7891234567891',
                'is_active' => true,
            ],
            [
                'name' => 'Gel Fixador',
                'description' => 'Gel para fixação do cabelo',
                'price' => 20.00,
                'stock' => 40,
                'min_stock' => 8,
                'sku' => 'GEL003',
                'barcode' => '7891234567892',
                'is_active' => true,
            ],
            [
                'name' => 'Óleo para Barba',
                'description' => 'Óleo para hidratação da barba',
                'price' => 45.00,
                'stock' => 25,
                'min_stock' => 5,
                'sku' => 'OIL004',
                'barcode' => '7891234567893',
                'is_active' => true,
            ],
        ];

        // Criar produtos fixos
        foreach ($fixedProducts as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        // Criar 10 produtos aleatórios
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $lastId = $lastProduct ? $lastProduct->id : 0;

        for ($i = 0; $i < 10; $i++) {
            $sku = 'PROD' . str_pad($lastId + $i + 1, 3, '0', STR_PAD_LEFT);
            Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $faker->words(3, true),
                    'description' => $faker->sentence,
                    'price' => $faker->randomFloat(2, 10, 100),
                    'stock' => $faker->numberBetween(10, 100),
                    'min_stock' => $faker->numberBetween(5, 20),
                    'barcode' => $faker->ean13,
                    'is_active' => $faker->boolean(90), // 90% de chance de estar ativo
                ]
            );
        }
    }
} 