<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barber;
use App\Models\User;

class BarberSeeder extends Seeder
{
    public function run()
    {
        $barbers = [
            [
                'name' => 'João Silva',
                'email' => 'joao@barber.com',
                'phone' => '(11) 99999-9999',
                'specialties' => ['Corte', 'Barba'],
                'is_active' => true,
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro@barber.com',
                'phone' => '(11) 98888-8888',
                'specialties' => ['Corte', 'Barba', 'Pigmentação'],
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos@barber.com',
                'phone' => '(11) 97777-7777',
                'specialties' => ['Corte', 'Hidratação'],
                'is_active' => true,
            ],
        ];

        foreach ($barbers as $barber) {
            $user = User::create([
                'name' => $barber['name'],
                'email' => $barber['email'],
                'password' => bcrypt('password'),
            ]);

            $user->assignRole('barber');

            Barber::create([
                'user_id' => $user->id,
                'name' => $barber['name'],
                'email' => $barber['email'],
                'phone' => $barber['phone'],
                'specialties' => $barber['specialties'],
                'is_active' => $barber['is_active'],
            ]);
        }
    }
} 