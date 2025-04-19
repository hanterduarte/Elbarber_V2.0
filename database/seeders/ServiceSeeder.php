<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Corte de Cabelo',
                'description' => 'Corte de cabelo tradicional',
                'price' => 30.00,
                'duration' => 30,
                'is_active' => true
            ],
            [
                'name' => 'Barba',
                'description' => 'Aparar e modelar barba',
                'price' => 25.00,
                'duration' => 20,
                'is_active' => true
            ],
            [
                'name' => 'Corte + Barba',
                'description' => 'Corte de cabelo e barba',
                'price' => 50.00,
                'duration' => 50,
                'is_active' => true
            ],
            [
                'name' => 'Sobrancelha',
                'description' => 'Design de sobrancelha',
                'price' => 15.00,
                'duration' => 15,
                'is_active' => true
            ],
            [
                'name' => 'Pigmentação',
                'description' => 'Pigmentação de barba',
                'price' => 80.00,
                'duration' => 60,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
} 