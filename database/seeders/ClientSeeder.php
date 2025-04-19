<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Clientes fixos
        $fixedClients = [
            [
                'name' => 'João Silva',
                'email' => 'joao.silva@example.com',
                'phone' => '(11) 99999-9999',
                'address' => 'Rua das Flores, 123',
                'birth_date' => '1990-01-01',
                'gender' => 'M',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@example.com',
                'phone' => '(11) 98888-8888',
                'address' => 'Av. Paulista, 1000',
                'birth_date' => '1992-05-15',
                'gender' => 'F',
                'is_active' => true,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro.oliveira@example.com',
                'phone' => '(11) 97777-7777',
                'address' => 'Rua Augusta, 500',
                'birth_date' => '1988-11-30',
                'gender' => 'M',
                'is_active' => true,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@example.com',
                'phone' => '(11) 96666-6666',
                'address' => 'Rua Oscar Freire, 200',
                'birth_date' => '1995-03-20',
                'gender' => 'F',
                'is_active' => true,
            ],
        ];

        // Criar clientes fixos
        foreach ($fixedClients as $client) {
            Client::firstOrCreate(
                ['email' => $client['email']],
                $client
            );
        }

        // Criar 20 clientes aleatórios
        for ($i = 0; $i < 20; $i++) {
            Client::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => $faker->date('Y-m-d', '-18 years'),
                'gender' => $faker->randomElement(['M', 'F']),
                'is_active' => $faker->boolean(90), // 90% de chance de estar ativo
            ]);
        }
    }
} 