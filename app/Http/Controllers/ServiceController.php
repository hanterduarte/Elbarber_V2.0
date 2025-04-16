<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.form');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            Log::info('Tentando criar serviço', ['data' => $validated]);

            $service = Service::create($validated);

            Log::info('Serviço criado com sucesso', ['service' => $service->toArray()]);

            return redirect()->route('services.index')
                ->with('success', 'Serviço cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar serviço', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Erro ao cadastrar serviço. Por favor, tente novamente.');
        }
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            Log::info('Tentando atualizar serviço', [
                'service_id' => $service->id,
                'data' => $validated
            ]);

            $service->update($validated);

            Log::info('Serviço atualizado com sucesso', ['service' => $service->toArray()]);

            return redirect()->route('services.index')
                ->with('success', 'Serviço atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar serviço', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Erro ao atualizar serviço. Por favor, tente novamente.');
        }
    }

    public function destroy(Service $service)
    {
        try {
            $service->delete();
            return redirect()->route('services.index')
                ->with('success', 'Serviço excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir serviço', [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Erro ao excluir serviço. Por favor, tente novamente.');
        }
    }
} 