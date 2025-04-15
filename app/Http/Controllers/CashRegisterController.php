<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\PaymentMethod;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $barber = $user->barber;

        if ($barber) {
            // Se for um barbeiro, mostra apenas o caixa dele
            $cashRegister = CashRegister::where('barber_id', $barber->id)
                ->whereNull('closed_at')
                ->first();
        } else {
            // Se for admin/gerente, mostra o caixa da barbearia
            $cashRegister = CashRegister::whereNull('barber_id')
                ->whereNull('closed_at')
                ->first();
        }

        return view('cash-register.index', compact('cashRegister'));
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();
        $barber = $user->barber;

        $cashRegister = new CashRegister();
        $cashRegister->user_id = $user->id;
        $cashRegister->barber_id = $barber ? $barber->id : null;
        $cashRegister->opening_balance = $request->opening_balance;
        $cashRegister->status = 'open';
        $cashRegister->opened_at = Carbon::now();
        $cashRegister->notes = $request->notes;
        $cashRegister->save();

        return redirect()->route('cash-register.index')
            ->with('success', 'Caixa aberto com sucesso.');
    }

    public function close(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();
        $barber = $user->barber;

        $cashRegister = CashRegister::where('user_id', $user->id)
            ->whereNull('closed_at')
            ->when($barber, function ($query) use ($barber) {
                return $query->where('barber_id', $barber->id);
            }, function ($query) {
                return $query->whereNull('barber_id');
            })
            ->firstOrFail();

        $cashRegister->closing_balance = $request->closing_balance;
        $cashRegister->status = 'closed';
        $cashRegister->closed_at = Carbon::now();
        $cashRegister->notes = $request->notes;
        $cashRegister->save();

        return redirect()->route('cash-register.index')
            ->with('success', 'Caixa fechado com sucesso.');
    }

    public function transaction()
    {
        $cashRegister = CashRegister::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->firstOrFail();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('cash-register.transaction', compact('cashRegister', 'paymentMethods'));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $cashRegister = CashRegister::where('user_id', Auth::id())
            ->whereNull('closed_at')
            ->firstOrFail();

        $transaction = CashRegisterTransaction::create([
            'cash_register_id' => $cashRegister->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        if ($validated['type'] === 'income') {
            $cashRegister->increment('total_sales', $validated['amount']);
        } else {
            $cashRegister->increment('total_expenses', $validated['amount']);
        }

        return redirect()->route('cash-register.index')
            ->with('success', 'Transação registrada com sucesso!');
    }
} 