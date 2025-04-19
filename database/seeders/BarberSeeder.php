<?php

namespace Database\Seeders;

use App\Models\Barber;
use App\Models\User;
use App\Models\Barbershop;
use Illuminate\Database\Seeder;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barbershops = Barbershop::all();
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'barber');
        })->get();

        foreach ($barbershops as $barbershop) {
            foreach ($users as $user) {
                Barber::create([
                    'user_id' => $user->id,
                    'barbershop_id' => $barbershop->id,
                    'specialty' => 'Corte de Cabelo',
                    'commission_rate' => 30.00,
                    'is_active' => true,
                ]);
            }
        }
    }
} 