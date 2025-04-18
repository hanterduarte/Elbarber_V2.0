<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Barber;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $barbers = Barber::all();
        $services = Service::all();

        // Criar 20 agendamentos de exemplo
        for ($i = 0; $i < 20; $i++) {
            $client = $clients->random();
            $barber = $barbers->random();
            $selectedServices = $services->random(rand(1, 3));
            
            $startTime = Carbon::now()->addDays(rand(-30, 30))->addHours(rand(9, 18))->addMinutes(rand(0, 11) * 5);
            $totalDuration = $selectedServices->sum('duration');
            $total = $selectedServices->sum('price');

            $appointment = Appointment::create([
                'client_id' => $client->id,
                'barber_id' => $barber->id,
                'start_time' => $startTime,
                'end_time' => $startTime->copy()->addMinutes($totalDuration),
                'status' => collect(['scheduled', 'confirmed', 'completed', 'cancelled'])->random(),
                'notes' => rand(0, 1) ? 'Observação de teste para o agendamento ' . ($i + 1) : null,
                'total' => $total,
                'duration' => $totalDuration
            ]);

            foreach ($selectedServices as $service) {
                $appointment->services()->attach($service->id, [
                    'price' => $service->price,
                    'duration' => $service->duration
                ]);
            }
        }
    }
} 