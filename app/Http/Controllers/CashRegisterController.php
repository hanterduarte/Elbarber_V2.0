<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashRegisterTransaction;
use App\Models\PaymentMethod;
use App\Models\Barber;
use App\Models\CashRegisterMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function status()
    {
        try {
            $currentRegister = CashRegister::where('status', 'open')->latest()->first();
            $lastClosedRegister = CashRegister::where('status', 'closed')->latest()->first();

            if ($currentRegister) {
                return response()->json([
                    'status' => 'open',
                    'opened_at' => $currentRegister->opened_at,
                    'current_balance' => $currentRegister->getCurrentBalance()
                ]);
            }

            return response()->json([
                'status' => 'closed',
                'last_closed_at' => $lastClosedRegister ? $lastClosedRegister->closed_at : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting cash register status: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao obter status do caixa'], 500);
        }
    }

    public function open(Request $request)
    {
        try {
            $request->validate([
                'opening_balance' => 'required|numeric|min:0'
            ]);

            if (CashRegister::where('status', 'open')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe um caixa aberto'
                ], 422);
            }

            $register = CashRegister::create([
                'user_id' => Auth::id(),
                'opening_balance' => $request->opening_balance,
                'status' => 'open',
                'opened_at' => now()
            ]);

            CashRegisterMovement::create([
                'user_id' => Auth::id(),
                'type' => 'opening',
                'amount' => $request->opening_balance,
                'description' => 'Abertura de caixa'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Caixa aberto com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Error opening cash register: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao abrir o caixa'
            ], 500);
        }
    }

    public function close()
    {
        try {
            $register = CashRegister::where('status', 'open')->latest()->first();

            if (!$register) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há caixa aberto'
                ], 422);
            }

            $currentBalance = $register->getCurrentBalance();

            $register->update([
                'status' => 'closed',
                'closing_balance' => $currentBalance,
                'closed_at' => now()
            ]);

            CashRegisterMovement::create([
                'user_id' => Auth::id(),
                'type' => 'closing',
                'amount' => $currentBalance,
                'description' => 'Fechamento de caixa'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Caixa fechado com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Error closing cash register: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fechar o caixa'
            ], 500);
        }
    }

    public function withdrawal(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string'
            ]);

            $register = CashRegister::where('status', 'open')->latest()->first();

            if (!$register) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há caixa aberto'
                ], 422);
            }

            $currentBalance = $register->getCurrentBalance();

            if ($currentBalance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo insuficiente para realizar a sangria'
                ], 422);
            }

            CashRegisterMovement::create([
                'user_id' => Auth::id(),
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'description' => $request->description
            ]);

            $register->increment('total_withdrawals', $request->amount);

            return response()->json([
                'success' => true,
                'message' => 'Sangria realizada com sucesso'
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar sangria'
            ], 500);
        }
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