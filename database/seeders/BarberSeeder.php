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

        Barber::create([
            'user_id' => $barberUser->id,
            'barbershop_id' => $barbershop->id,
            'name' => $barberUser->name,
            'email' => $barberUser->email,
            'phone' => '(11) 98888-8888',
            'specialties' => ['Corte masculino', 'Barba'],
            'is_active' => true
        ]);

        // Create additional barbers
        $barbers = [
            [
                'name' => 'João Silva',
                'email' => 'joao@elbarber.com',
                'phone' => '(11) 97777-7777',
                'specialties' => ['Corte masculino', 'Pigmentação']
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro@elbarber.com',
                'phone' => '(11) 96666-6666',
                'specialties' => ['Barba', 'Hidratação']
            ]
        ];

        foreach ($barbers as $barberData) {
            $user = User::create([
                'name' => $barberData['name'],
                'email' => $barberData['email'],
                'password' => bcrypt('password')
            ]);

            $user->assignRole('barber');

            Barber::create([
                'user_id' => $user->id,
                'barbershop_id' => $barbershop->id,
                'name' => $barberData['name'],
                'email' => $barberData['email'],
                'phone' => $barberData['phone'],
                'specialties' => $barberData['specialties'],
                'is_active' => true
            ]);
        }
    }
} 