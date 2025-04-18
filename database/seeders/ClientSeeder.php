<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
                'phone' => '(11) 99999-9999',
                'birth_date' => '1990-01-01',
                'address' => 'Rua Exemplo, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-567',
                'notes' => 'Cliente preferencial',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'phone' => '(11) 98888-8888',
                'birth_date' => '1992-05-15',
                'address' => 'Av. Principal, 456',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-890',
                'notes' => 'Gosta de cortes modernos',
                'is_active' => true,
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@example.com',
                'phone' => '(11) 97777-7777',
                'birth_date' => '1988-11-30',
                'address' => 'Rua Secundária, 789',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-123',
                'notes' => 'Cliente frequente',
                'is_active' => true,
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
} 