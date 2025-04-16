<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Barber;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['barber', 'client', 'services'])->get();
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $barbers = Barber::all();
        $clients = Client::all();
        $services = Service::all();
        return view('appointments.create', compact('barbers', 'clients', 'services'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'barber_id' => 'required|exists:barbers,id',
                'client_id' => 'required|exists:clients,id',
                'date' => 'required|date',
                'services' => 'required|array|min:1',
                'services.*' => 'exists:services,id',
                'notes' => 'nullable|string',
            ]);

            $appointment = Appointment::create([
                'barber_id' => $validated['barber_id'],
                'client_id' => $validated['client_id'],
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'scheduled',
            ]);

            $appointment->services()->attach($validated['services']);

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento criado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar agendamento. Por favor, tente novamente.');
        }
    }

    public function edit(Appointment $appointment)
    {
        $barbers = Barber::all();
        $clients = Client::all();
        $services = Service::all();
        return view('appointments.edit', compact('appointment', 'barbers', 'clients', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        try {
            $validated = $request->validate([
                'barber_id' => 'required|exists:barbers,id',
                'client_id' => 'required|exists:clients,id',
                'date' => 'required|date',
                'services' => 'required|array|min:1',
                'services.*' => 'exists:services,id',
                'notes' => 'nullable|string',
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            $appointment->update([
                'barber_id' => $validated['barber_id'],
                'client_id' => $validated['client_id'],
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            $appointment->services()->sync($validated['services']);

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar agendamento. Por favor, tente novamente.');
        }
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->services()->detach();
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento excluído com sucesso!');
    }
} 