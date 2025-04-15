<?php

namespace App\Http\Controllers;

use App\Models\Barbershop;
use Illuminate\Http\Request;

class BarbershopController extends Controller
{
    public function index()
    {
        $barbershops = Barbershop::withCount(['barbers', 'sales'])->get();
        return view('barbershops.index', compact('barbershops'));
    }

    public function create()
    {
        return view('barbershops.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string'
        ]);

        Barbershop::create($validated);

        return redirect()
            ->route('barbershops.index')
            ->with('success', 'Barbearia cadastrada com sucesso!');
    }

    public function show(Barbershop $barbershop)
    {
        $barbershop->load(['barbers.user', 'sales' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('barbershops.show', compact('barbershop'));
    }

    public function edit(Barbershop $barbershop)
    {
        return view('barbershops.form', compact('barbershop'));
    }

    public function update(Request $request, Barbershop $barbershop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string'
        ]);

        $barbershop->update($validated);

        return redirect()
            ->route('barbershops.index')
            ->with('success', 'Barbearia atualizada com sucesso!');
    }

    public function destroy(Barbershop $barbershop)
    {
        if ($barbershop->barbers()->exists()) {
            return back()->with('error', 'Não é possível excluir uma barbearia que possui barbeiros vinculados.');
        }

        $barbershop->delete();

        return redirect()
            ->route('barbershops.index')
            ->with('success', 'Barbearia excluída com sucesso!');
    }
} 