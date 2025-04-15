<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@elbarber.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Criar usuário gerente
        $manager = User::firstOrCreate(
            ['email' => 'gerente@elbarber.com'],
            [
                'name' => 'Gerente',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole('manager');

        // Criar usuário barbeiro
        $barber = User::firstOrCreate(
            ['email' => 'barbeiro@elbarber.com'],
            [
                'name' => 'Barbeiro',
                'password' => Hash::make('password'),
            ]
        );
        $barber->assignRole('barber');

        // Criar usuário recepcionista
        $receptionist = User::firstOrCreate(
            ['email' => 'recepcionista@elbarber.com'],
            [
                'name' => 'Recepcionista',
                'password' => Hash::make('password'),
            ]
        );
        $receptionist->assignRole('receptionist');
    }
} 