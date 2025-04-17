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
        $sales = Sale::with(['client', 'products', 'paymentMethod'])->get();
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
                'products' => 'nullable|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'services' => 'nullable|array',
                'services.*.id' => 'required|exists:services,id',
                'services.*.quantity' => 'required|integer|min:1',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'notes' => 'nullable|string',
            ]);

            // Validar se há pelo menos produtos ou serviços
            if (empty($validated['products']) && empty($validated['services'])) {
                throw new \Exception('A venda deve conter pelo menos um produto ou serviço.');
            }

            // Iniciar transação
            DB::beginTransaction();

            // Calcular total dos produtos e serviços
            $total = 0;

            // Processar produtos
            $productItems = [];
            if (!empty($validated['products'])) {
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
            }

            // Processar serviços
            $serviceItems = [];
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $service) {
                    $serviceModel = Service::findOrFail($service['id']);
                    $quantity = $service['quantity'];
                    $price = $serviceModel->price;
                    $subtotal = $price * $quantity;

                    $serviceItems[] = [
                        'id' => $service['id'],
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $subtotal
                    ];

                    $total += $subtotal;
                }
            }

            // Calcular desconto
            $discountPercentage = $validated['discount_percentage'] ?? 0;
            $discountAmount = $total * ($discountPercentage / 100);
            $finalTotal = $total - $discountAmount;

            // Validar total dos pagamentos
            $totalPayments = array_reduce($validated['payments'], function($carry, $payment) {
                return $carry + $payment['amount'];
            }, 0);

            if (abs($totalPayments - $finalTotal) > 0.01) {
                throw new \Exception('O total dos pagamentos não corresponde ao valor final da venda.');
            }

            // Criar a venda
            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'total' => $total,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal,
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

            // Registrar os serviços
            foreach ($serviceItems as $item) {
                $sale->services()->attach($item['id'], [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total']
                ]);
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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        // Restaurar o estoque dos produtos antigos
        foreach ($sale->products as $product) {
            $product->increment('stock', $product->pivot->quantity);
        }

        $sale->products()->detach();

        $total = 0;
        foreach ($validated['products'] as $product) {
            $productModel = Product::find($product['id']);
            $quantity = $product['quantity'];
            $price = $productModel->price;
            $subtotal = $price * $quantity;

            $sale->products()->attach($product['id'], [
                'quantity' => $quantity,
                'price' => $price,
                'total' => $subtotal,
            ]);

            $total += $subtotal;
            $productModel->decrement('stock', $quantity);
        }

        $sale->update([
            'client_id' => $validated['client_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'notes' => $validated['notes'],
            'status' => $validated['status'],
            'total' => $total,
        ]);

        return redirect()->route('sales.index')
            ->with('success', 'Venda atualizada com sucesso!');
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