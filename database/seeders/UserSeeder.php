<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Criar usuário admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@elbarber.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-9999',
                'is_active' => true
            ]
        );
        $admin->roles()->sync([Role::where('name', 'admin')->first()->id]);

        // Criar usuário gerente
        $manager = User::firstOrCreate(
            ['email' => 'manager@elbarber.com'],
            [
                'name' => 'Gerente',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-9998',
                'is_active' => true
            ]
        );
        $manager->roles()->sync([Role::where('name', 'manager')->first()->id]);

        // Criar usuário barbeiro
        $barber = User::firstOrCreate(
            ['email' => 'barber@elbarber.com'],
            [
                'name' => 'Barbeiro',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-9997',
                'is_active' => true
            ]
        );
        $barber->roles()->sync([Role::where('name', 'barber')->first()->id]);

        // Criar usuário recepcionista
        $receptionist = User::firstOrCreate(
            ['email' => 'receptionist@elbarber.com'],
            [
                'name' => 'Recepcionista',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-9996',
                'is_active' => true
            ]
        );
        $receptionist->roles()->sync([Role::where('name', 'receptionist')->first()->id]);
    }
} 