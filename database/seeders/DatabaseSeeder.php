<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Configurações básicas
            PermissionSeeder::class,
            RoleSeeder::class,
            PaymentMethodSeeder::class,
            
            // Dados principais
            BarbershopSeeder::class,
            UserSeeder::class,
            BarberSeeder::class,
            ClientSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            
            // Dados de transação
            CashRegisterMovementSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
} 