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
                'description' => 'Corte tradicional masculino',
                'price' => 35.00,
                'duration' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Barba',
                'description' => 'Barba com toalha quente',
                'price' => 25.00,
                'duration' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Corte + Barba',
                'description' => 'Corte e barba completo',
                'price' => 55.00,
                'duration' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Pigmentação',
                'description' => 'Pigmentação de barba ou cabelo',
                'price' => 45.00,
                'duration' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Hidratação',
                'description' => 'Hidratação capilar',
                'price' => 40.00,
                'duration' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
} 