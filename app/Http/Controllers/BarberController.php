<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\User;
use App\Models\Barbershop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::with(['user', 'barbershop'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('barbers.index', compact('barbers'));
    }

    public function create()
    {
        $barbershops = Barbershop::all();
        return view('barbers.form', compact('barbershops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:barbers',
            'phone' => 'required|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('barbers', 'public');
            $validated['photo'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_id' => 2 // ID do perfil "Barbeiro"
            ]);

            $barber = Barber::create([
                'user_id' => $user->id,
                'barbershop_id' => $request->barbershop_id,
                'phone' => $request->phone,
                'commission_rate' => $request->commission_rate,
                'specialties' => $request->specialties,
                'status' => $request->status,
                'photo' => $validated['photo']
            ]);

            DB::commit();
            return redirect()->route('barbers.index')->with('success', 'Barbeiro cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao cadastrar barbeiro: ' . $e->getMessage());
        }
    }

    public function show(Barber $barber)
    {
        $barber->load(['user', 'barbershop', 'sales' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('barbers.show', compact('barber'));
    }

    public function edit(Barber $barber)
    {
        $barbershops = Barbershop::all();
        return view('barbers.form', compact('barber', 'barbershops'));
    }

    public function update(Request $request, Barber $barber)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:barbers,email,' . $barber->id,
            'phone' => 'required|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($barber->photo) {
                Storage::disk('public')->delete($barber->photo);
            }
            $path = $request->file('photo')->store('barbers', 'public');
            $validated['photo'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');

        DB::beginTransaction();
        try {
            $barber->user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

            if ($request->filled('password')) {
                $barber->user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            $barber->update([
                'barbershop_id' => $request->barbershop_id,
                'phone' => $request->phone,
                'commission_rate' => $request->commission_rate,
                'specialties' => $request->specialties,
                'status' => $request->status,
                'photo' => $validated['photo']
            ]);

            DB::commit();
            return redirect()->route('barbers.index')->with('success', 'Barbeiro atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao atualizar barbeiro: ' . $e->getMessage());
        }
    }

    public function destroy(Barber $barber)
    {
        if ($barber->sales()->exists()) {
            return back()->with('error', 'Não é possível excluir este barbeiro pois existem vendas associadas a ele.');
        }

        if ($barber->photo) {
            Storage::disk('public')->delete($barber->photo);
        }

        DB::beginTransaction();
        try {
            $barber->user->delete();
            $barber->delete();
            
            DB::commit();
            return redirect()->route('barbers.index')->with('success', 'Barbeiro excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao excluir barbeiro: ' . $e->getMessage());
        }
    }
} 