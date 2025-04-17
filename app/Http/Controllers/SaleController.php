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
                'payment_method_id' => 'required|exists:payment_methods,id',
                'products' => 'nullable|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
                'services' => 'nullable|array',
                'services.*.id' => 'required|exists:services,id',
                'services.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            // Iniciar transação
            DB::beginTransaction();

            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'payment_method_id' => $validated['payment_method_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'total' => 0,
            ]);

            $total = 0;

            // Processar produtos
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

                    $sale->products()->attach($product['id'], [
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $subtotal,
                    ]);

                    // Atualizar estoque
                    $productModel->decrement('stock', $quantity);
                    $total += $subtotal;
                }
            }

            // Processar serviços
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $service) {
                    $serviceModel = Service::findOrFail($service['id']);
                    $quantity = $service['quantity'];
                    $price = $serviceModel->price;
                    $subtotal = $price * $quantity;

                    $sale->services()->attach($service['id'], [
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $subtotal,
                    ]);

                    $total += $subtotal;
                }
            }

            // Atualizar o total da venda
            $sale->update(['total' => $total]);

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