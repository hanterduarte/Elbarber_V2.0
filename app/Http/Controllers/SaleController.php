<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['client', 'products', 'payments.paymentMethod'])->get();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::all();
        $clients = Client::all();
        $paymentMethods = PaymentMethod::all();
        return view('sales.create', compact('products', 'clients', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'payments' => 'required|array',
                'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
                'payments.*.amount' => 'required|numeric|min:0',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            // Iniciar transação
            DB::beginTransaction();

            // Calcular total dos produtos
            $total = 0;
            $productItems = [];

            foreach ($validated['products'] as $product) {
                $productModel = Product::findOrFail($product['id']);
                
                // Verificar estoque
                if ($productModel->stock < $product['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$productModel->name}");
                }

                $quantity = $product['quantity'];
                $price = $productModel->price;
                $subtotal = $price * $quantity;

                $productItems[] = [
                    'id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $subtotal
                ];

                $total += $subtotal;
            }

            // Validar total dos pagamentos
            $totalPayments = array_reduce($validated['payments'], function($carry, $payment) {
                return $carry + $payment['amount'];
            }, 0);

            if (abs($totalPayments - $total) > 0.01) {
                throw new \Exception('O total dos pagamentos não corresponde ao valor final da venda.');
            }

            // Criar a venda
            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'total' => $total,
                'final_total' => $total,
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed'
            ]);

            // Registrar os pagamentos
            foreach ($validated['payments'] as $payment) {
                $sale->payments()->create([
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount']
                ]);
            }

            // Registrar os produtos
            foreach ($productItems as $item) {
                $sale->products()->attach($item['id'], [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total']
                ]);

                // Atualizar estoque
                Product::find($item['id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'redirect' => route('sales.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao registrar venda', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Erro ao registrar venda. Por favor, tente novamente.'
            ], 422);
        }
    }

    public function edit(Sale $sale)
    {
        $products = Product::all();
        $clients = Client::all();
        $paymentMethods = PaymentMethod::all();
        return view('sales.edit', compact('sale', 'products', 'clients', 'paymentMethods'));
    }

    public function update(Request $request, Sale $sale)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'payments' => 'required|array',
                'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
                'payments.*.amount' => 'required|numeric|min:0',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Restaurar o estoque dos produtos antigos
            foreach ($sale->products as $product) {
                $product->increment('stock', $product->pivot->quantity);
            }

            // Remover produtos e pagamentos antigos
            $sale->products()->detach();
            $sale->payments()->delete();

            // Calcular total dos produtos
            $total = 0;
            foreach ($validated['products'] as $product) {
                $productModel = Product::findOrFail($product['id']);
                $quantity = $product['quantity'];
                $price = $productModel->price;
                $subtotal = $price * $quantity;

                $sale->products()->attach($product['id'], [
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $subtotal
                ]);

                $total += $subtotal;
                $productModel->decrement('stock', $quantity);
            }

            // Validar total dos pagamentos
            $totalPayments = array_reduce($validated['payments'], function($carry, $payment) {
                return $carry + $payment['amount'];
            }, 0);

            if (abs($totalPayments - $total) > 0.01) {
                throw new \Exception('O total dos pagamentos não corresponde ao valor final da venda.');
            }

            // Atualizar a venda
            $sale->update([
                'client_id' => $validated['client_id'],
                'total' => $total,
                'final_total' => $total,
                'notes' => $validated['notes'] ?? null
            ]);

            // Registrar os novos pagamentos
            foreach ($validated['payments'] as $payment) {
                $sale->payments()->create([
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount' => $payment['amount']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda atualizada com sucesso!',
                'redirect' => route('sales.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao atualizar venda', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Erro ao atualizar venda. Por favor, tente novamente.'
            ], 422);
        }
    }

    public function destroy(Sale $sale)
    {
        // Restaurar o estoque dos produtos
        foreach ($sale->products as $product) {
            $product->increment('stock', $product->pivot->quantity);
        }

        $sale->products()->detach();
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Venda excluída com sucesso!');
    }
} 