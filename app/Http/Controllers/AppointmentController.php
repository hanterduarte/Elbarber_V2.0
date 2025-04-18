<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['client', 'barber', 'services'])
            ->orderBy('start_time', 'desc')
            ->get();
            
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $barbers = User::where('role', 'barber')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        
        return view('appointments.create', compact('clients', 'barbers', 'services'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validação dos dados
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'barber_id' => 'required|exists:users,id',
                'start_time' => 'required|date',
                'services' => 'required|array|min:1',
                'services.*' => 'exists:services,id',
                'notes' => 'nullable|string|max:500'
            ]);

            // Calcular duração total dos serviços
            $services = Service::whereIn('id', $validated['services'])->get();
            $totalDuration = $services->sum('duration');
            
            // Calcular horário de término
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            // Verificar conflitos de horário
            $conflicts = Appointment::where('barber_id', $validated['barber_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function($q) use ($startTime, $endTime) {
                              $q->where('start_time', '<', $startTime)
                                ->where('end_time', '>', $endTime);
                          });
                })
                ->exists();

            if ($conflicts) {
                throw new \Exception('Já existe um agendamento neste horário para este barbeiro.');
            }

            // Criar o agendamento
            $appointment = Appointment::create([
                'client_id' => $validated['client_id'],
                'barber_id' => $validated['barber_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'scheduled',
                'notes' => $validated['notes'] ?? null
            ]);

            // Adicionar os serviços
            $appointment->services()->attach($validated['services']);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Agendamento criado com sucesso!',
                    'redirect' => route('appointments.index')
                ]);
            }

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao criar agendamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Appointment $appointment)
    {
        $clients = Client::orderBy('name')->get();
        $barbers = User::where('role', 'barber')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        
        return view('appointments.edit', compact('appointment', 'clients', 'barbers', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'barber_id' => 'required|exists:users,id',
                'start_time' => 'required|date',
                'services' => 'required|array|min:1',
                'services.*' => 'exists:services,id',
                'notes' => 'nullable|string|max:500'
            ]);

            // Calcular duração total dos serviços
            $services = Service::whereIn('id', $validated['services'])->get();
            $totalDuration = $services->sum('duration');
            
            // Calcular horário de término
            $startTime = Carbon::parse($validated['start_time']);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            // Verificar conflitos de horário (excluindo o agendamento atual)
            $conflicts = Appointment::where('barber_id', $validated['barber_id'])
                ->where('id', '!=', $appointment->id)
                ->where('status', '!=', 'cancelled')
                ->where(function($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function($q) use ($startTime, $endTime) {
                              $q->where('start_time', '<', $startTime)
                                ->where('end_time', '>', $endTime);
                          });
                })
                ->exists();

            if ($conflicts) {
                throw new \Exception('Já existe um agendamento neste horário para este barbeiro.');
            }

            // Atualizar o agendamento
            $appointment->update([
                'client_id' => $validated['client_id'],
                'barber_id' => $validated['barber_id'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'notes' => $validated['notes'] ?? null
            ]);

            // Atualizar os serviços
            $appointment->services()->sync($validated['services']);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Agendamento atualizado com sucesso!',
                    'redirect' => route('appointments.index')
                ]);
            }

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao atualizar agendamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Agendamento excluído com sucesso!'
                ]);
            }

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento excluído com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir agendamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir agendamento.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao excluir agendamento.']);
        }
    }
} 