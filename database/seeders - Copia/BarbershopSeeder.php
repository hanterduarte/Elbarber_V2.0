<?php

namespace Database\Seeders;

use App\Models\Barbershop;
use Illuminate\Database\Seeder;

class BarbershopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barbershops = [
            [
                'name' => 'El Barber - Centro',
                'address' => 'Rua Principal, 123 - Centro',
                'phone' => '(11) 99999-9999',
                'email' => 'centro@elbarber.com',
                'cnpj' => '12.345.678/0001-90',
                'logo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'El Barber - Zona Sul',
                'address' => 'Av. Sul, 456 - Zona Sul',
                'phone' => '(11) 88888-8888',
                'email' => 'zonasul@elbarber.com',
                'cnpj' => '12.345.678/0002-71',
                'logo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($barbershops as $barbershop) {
            Barbershop::create($barbershop);
        }
    }
} 