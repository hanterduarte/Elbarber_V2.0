<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $appointments = Appointment::with(['client', 'barber', 'services'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $barbers = Barber::with('user')->orderBy('id')->get();
        $services = Service::orderBy('name')->get();

        return view('appointments.create', compact('clients', 'barbers', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'barber_id' => 'required|exists:barbers,id',
            'start_time' => 'required|date',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $services = Service::whereIn('id', $request->services)->get();
            $totalDuration = $services->sum('duration');
            $total = $services->sum('price');

            $appointment = Appointment::create([
                'client_id' => $request->client_id,
                'barber_id' => $request->barber_id,
                'start_time' => $request->start_time,
                'end_time' => Carbon::parse($request->start_time)->addMinutes($totalDuration),
                'status' => 'scheduled',
                'notes' => $request->notes,
                'total' => $total,
                'duration' => $totalDuration
            ]);

            foreach ($services as $service) {
                $appointment->services()->attach($service->id, [
                    'price' => $service->price,
                    'duration' => $service->duration
                ]);
            }

            DB::commit();

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar agendamento: ' . $e->getMessage());
        }
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['client', 'barber', 'services', 'payments']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $clients = Client::orderBy('name')->get();
        $barbers = Barber::with('user')->orderBy('id')->get();
        $services = Service::orderBy('name')->get();
        $appointment->load('services');

        return view('appointments.edit', compact('appointment', 'clients', 'barbers', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'barber_id' => 'required|exists:barbers,id',
            'start_time' => 'required|date',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $services = Service::whereIn('id', $request->services)->get();
            $totalDuration = $services->sum('duration');
            $total = $services->sum('price');

            $appointment->update([
                'client_id' => $request->client_id,
                'barber_id' => $request->barber_id,
                'start_time' => $request->start_time,
                'end_time' => Carbon::parse($request->start_time)->addMinutes($totalDuration),
                'status' => $request->status,
                'notes' => $request->notes,
                'total' => $total,
                'duration' => $totalDuration
            ]);

            $appointment->services()->detach();
            foreach ($services as $service) {
                $appointment->services()->attach($service->id, [
                    'price' => $service->price,
                    'duration' => $service->duration
                ]);
            }

            DB::commit();

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar agendamento: ' . $e->getMessage());
        }
    }

    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();
            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir agendamento: ' . $e->getMessage());
        }
    }
} 