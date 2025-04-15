<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterMovementController extends Controller
{
    public function create()
    {
        return view('cash-register.movement.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $movement = new CashRegisterMovement();
        $movement->type = $request->type;
        $movement->amount = $request->amount;
        $movement->description = $request->description;
        $movement->user_id = Auth::id();
        $movement->save();

        return redirect()->route('cash-register.index')
            ->with('success', 'Movimentação registrada com sucesso!');
    }
} 