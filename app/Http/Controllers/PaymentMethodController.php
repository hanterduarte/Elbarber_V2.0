<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::paginate(10);
        return view('payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            $validated['is_active'] = $request->boolean('is_active');

            PaymentMethod::create($validated);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Método de pagamento cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Error creating payment method: ' . $e->getMessage());
            return back()->with('error', 'Erro ao cadastrar método de pagamento. Por favor, tente novamente.')
                ->withInput();
        }
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            $validated['is_active'] = $request->boolean('is_active');

            $paymentMethod->update($validated);

            return redirect()->route('payment-methods.index')
                ->with('success', 'Método de pagamento atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Error updating payment method: ' . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar método de pagamento. Por favor, tente novamente.')
                ->withInput();
        }
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();
            return redirect()->route('payment-methods.index')
                ->with('success', 'Método de pagamento excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Error deleting payment method: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir método de pagamento. Por favor, tente novamente.');
        }
    }
}