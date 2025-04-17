<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $clients = Client::where('is_active', true)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('pos.index', compact('products', 'services', 'clients', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'payments' => 'required|array',
                'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
                'payments.*.amount' => 'required|numeric|min:0',
                'products' => 'array',
                'products.*.id' => 'exists:products,id',
                'products.*.quantity' => 'required_with:products.*.id|integer|min:1',
                'services' => 'array',
                'services.*.id' => 'exists:services,id',
                'services.*.quantity' => 'required_with:services.*.id|integer|min:1',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'discount_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Calcular total dos produtos e serviços
            $subtotal = 0;
            $items = [];

            // Processar produtos
            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $product) {
                    $productModel = Product::findOrFail($product['id']);
                    
                    if ($productModel->stock < $product['quantity']) {
                        throw new \Exception("Estoque insuficiente para o produto: {$productModel->name}");
                    }

                    $quantity = $product['quantity'];
                    $price = $productModel->price;
                    $itemTotal = $price * $quantity;

                    $items[] = [
                        'model' => $productModel,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                        'type' => 'product'
                    ];

                    $subtotal += $itemTotal;
                }
            }

            // Processar serviços
            if (!empty($validated['services'])) {
                foreach ($validated['services'] as $service) {
                    $serviceModel = Service::findOrFail($service['id']);
                    
                    $quantity = $service['quantity'];
                    $price = $serviceModel->price;
                    $itemTotal = $price * $quantity;

                    $items[] = [
                        'model' => $serviceModel,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                        'type' => 'service'
                    ];

                    $subtotal += $itemTotal;
                }
            }

            // Calcular descontos
            $discountPercentage = $validated['discount_percentage'] ?? 0;
            $discountAmount = $validated['discount_amount'] ?? 0;
            $percentageDiscount = $subtotal * ($discountPercentage / 100);
            $totalDiscount = $percentageDiscount + $discountAmount;
            $finalTotal = $subtotal - $totalDiscount;

            // Validar total dos pagamentos
            $totalPayments = array_reduce($validated['payments'], function($carry, $payment) {
                return $carry + (float)$payment['amount'];
            }, 0);

            // Arredondar os valores para 2 casas decimais
            $finalTotal = round($finalTotal, 2);
            $totalPayments = round($totalPayments, 2);

            if (abs($totalPayments - $finalTotal) > 0.01) {
                throw new \Exception("O total dos pagamentos (R$ {$totalPayments}) não corresponde ao valor final da venda (R$ {$finalTotal})");
            }

            // Criar a venda
            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'subtotal' => $subtotal,
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

            // Registrar os itens e atualizar estoque
            foreach ($items as $item) {
                $sale->items()->create([
                    'saleable_type' => $item['type'] === 'product' ? Product::class : Service::class,
                    'saleable_id' => $item['model']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total']
                ]);

                if ($item['type'] === 'product') {
                    $item['model']->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda registrada com sucesso!',
                'redirect' => route('sales.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao registrar venda no PDV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Erro ao registrar venda. Por favor, tente novamente.'
            ], 422);
        }
    }
} 