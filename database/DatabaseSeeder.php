<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Barbershop;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar a barbearia principal
        $barbershop = Barbershop::create([
            'name' => 'El Barber',
            'address' => 'Endereço Principal',
            'phone' => '(00) 00000-0000',
            'email' => 'contato@elbarber.com',
            'is_active' => true
        ]);

        // Criar o superusuário
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@elbarber.com',
            'password' => Hash::make('admin123'),
            'profile' => 'admin',
            'is_active' => true
        ]);

        // Criar um barbeiro para o superusuário
        $user->barber()->create([
            'barbershop_id' => $barbershop->id,
            'commission_rate' => 50,
            'specialties' => 'Todos os serviços',
            'is_active' => true
        ]);
    }
} 