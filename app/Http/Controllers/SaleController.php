<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Service;
use App\Models\Client;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        try {
            $sales = Sale::with(['client', 'user', 'items', 'payments'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('sales.index', compact('sales'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar vendas: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $clients = Client::where('is_active', true)->get();
            $products = Product::where('is_active', true)->get();
            $services = Service::where('is_active', true)->get();
            $paymentMethods = PaymentMethod::where('is_active', true)->get();
            
            return view('sales.create', compact('clients', 'products', 'services', 'paymentMethods'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
                'payment_methods' => 'required|array|min:1',
                'payment_methods.*.id' => 'required|exists:payment_methods,id',
                'payment_methods.*.amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'user_id' => auth()->id(),
                'total' => 0,
                'subtotal' => 0,
                'discount' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'product') {
                    $product = Product::findOrFail($item['id']);
                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => 'Produto ' . $product->name . ' não possui estoque suficiente.'
                        ]);
                    }
                    $product->decrement('stock', $item['quantity']);
                }

                $sale->items()->create([
                    'itemable_type' => $item['type'] === 'product' ? Product::class : Service::class,
                    'itemable_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                ]);

                $total += ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0);
            }

            $sale->update([
                'total' => $total,
                'subtotal' => $total,
            ]);

            foreach ($validated['payment_methods'] as $payment) {
                $sale->payments()->create([
                    'payment_method_id' => $payment['id'],
                    'amount' => $payment['amount'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales.show', $sale)->with('success', 'Venda criada com sucesso!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao criar venda: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        try {
            $sale->load(['client', 'user', 'items.itemable', 'payments.paymentMethod']);
            return view('sales.show', compact('sale'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar venda: ' . $e->getMessage());
        }
    }

    public function cancel(Sale $sale)
    {
        try {
            if ($sale->status === 'cancelled') {
                return redirect()->back()->with('error', 'Esta venda já está cancelada.');
            }

            DB::beginTransaction();

            foreach ($sale->items as $item) {
                if ($item->itemable_type === Product::class) {
                    $product = Product::find($item->itemable_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            $sale->update(['status' => 'cancelled']);
            
            DB::commit();
            return redirect()->route('sales.show', $sale)->with('success', 'Venda cancelada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }
    }
} 