<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $sale = Sale::create([
            'client_id' => $validated['client_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'notes' => $validated['notes'],
            'status' => 'completed',
        ]);

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

        $sale->update(['total' => $total]);

        return redirect()->route('sales.index')
            ->with('success', 'Venda registrada com sucesso!');
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