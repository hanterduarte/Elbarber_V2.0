<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run()
    {
        // Obtém os clientes
        $clients = Client::all();
        
        // Obtém os barbeiros
        $barbers = User::whereHas('roles', function ($query) {
            $query->where('name', 'barber');
        })->get();
        
        // Obtém os serviços
        $services = Service::all();
        
        // Cria 10 agendamentos
        for ($i = 0; $i < 10; $i++) {
            $client = $clients->random();
            $barber = $barbers->random();
            $service = $services->random();
            
            $startTime = Carbon::today()->addDays(rand(1, 30))->addHours(rand(9, 17));
            $endTime = $startTime->copy()->addMinutes($service->duration);
            
            $appointment = Appointment::create([
                'client_id' => $client->id,
                'barber_id' => $barber->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'scheduled',
                'notes' => 'Agendamento criado pelo seeder',
                'total' => $service->price,
                'duration' => $service->duration
            ]);
            
            $appointment->services()->attach($service->id, ['price' => $service->price]);
        }
    }
} 