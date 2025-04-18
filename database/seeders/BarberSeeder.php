<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barber;
use App\Models\Barbershop;
use App\Models\User;

class BarberSeeder extends Seeder
{
    public function run()
    {
        $barbershop = Barbershop::first();
        $barberUser = User::where('email', 'barbeiro@elbarber.com')->first();

        // Criar o barbeiro associado ao usuário barbeiro
        Barber::create([
            'user_id' => $barberUser->id,
            'barbershop_id' => $barbershop->id,
            'name' => $barberUser->name,
            'email' => $barberUser->email,
            'phone' => '(11) 98888-8888',
            'specialties' => ['Corte masculino', 'Barba'],
            'is_active' => true
        ]);

        // Criar usuários adicionais para os outros barbeiros
        $users = [
            [
                'name' => 'Carlos Silva',
                'email' => 'carlos@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Roberto Santos',
                'email' => 'roberto@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Marcos Oliveira',
                'email' => 'marcos@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->assignRole('barber');

            Barber::create([
                'user_id' => $user->id,
                'barbershop_id' => $barbershop->id,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => '(11) 99999-9999',
                'specialties' => ['Corte de cabelo', 'Barba'],
                'is_active' => true
            ]);
        }
    }
} 