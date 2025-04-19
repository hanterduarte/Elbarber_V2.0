<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['client', 'services', 'barber'])->get();
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $clients = Client::all();
        $services = Service::all();
        $barbers = User::role('barber')->get();
        return view('appointments.create', compact('clients', 'services', 'barbers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'barber_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Criar o agendamento
        $appointment = Appointment::create([
            'client_id' => $validated['client_id'],
            'barber_id' => $validated['barber_id'],
            'start_time' => $validated['date'] . ' ' . $validated['time'],
            'end_time' => date('Y-m-d H:i:s', strtotime($validated['date'] . ' ' . $validated['time'] . ' +1 hour')),
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'duration' => 60, // Duração padrão de 1 hora
        ]);

        // Calcular o total e associar os serviços
        $total = 0;
        foreach ($validated['services'] as $serviceId) {
            $service = Service::find($serviceId);
            $total += $service->price;
            
            // Associar o serviço ao agendamento com o preço
            $appointment->services()->attach($service->id, [
                'price' => $service->price
            ]);
        }

        // Atualizar o total do agendamento
        $appointment->update([
            'total' => $total
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento criado com sucesso.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['client', 'services', 'barber']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $clients = Client::all();
        $services = Service::all();
        $barbers = User::role('barber')->get();
        $appointment->load(['client', 'services', 'barber']);
        return view('appointments.edit', compact('appointment', 'clients', 'services', 'barbers'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'barber_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Atualizar o agendamento
        $appointment->update([
            'client_id' => $validated['client_id'],
            'barber_id' => $validated['barber_id'],
            'start_time' => $validated['date'] . ' ' . $validated['time'],
            'end_time' => date('Y-m-d H:i:s', strtotime($validated['date'] . ' ' . $validated['time'] . ' +1 hour')),
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Remover todos os serviços existentes
        $appointment->services()->detach();
        
        // Calcular o total e associar os novos serviços
        $total = 0;
        foreach ($validated['services'] as $serviceId) {
            $service = Service::find($serviceId);
            $total += $service->price;
            
            // Associar o serviço ao agendamento com o preço
            $appointment->services()->attach($service->id, [
                'price' => $service->price
            ]);
        }

        // Atualizar o total do agendamento
        $appointment->update([
            'total' => $total
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento atualizado com sucesso.');
    }

    public function destroy(Appointment $appointment)
    {
        // Remover todos os serviços associados
        $appointment->services()->detach();
        
        // Excluir o agendamento
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento excluído com sucesso.');
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento confirmado com sucesso.');
    }

    public function complete(Appointment $appointment)
    {
        $appointment->update(['status' => 'completed']);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento concluído com sucesso.');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento cancelado com sucesso.');
    }
} 