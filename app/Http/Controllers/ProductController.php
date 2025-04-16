<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.form');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Tentando criar novo produto', ['data' => $request->all()]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'cost' => 'nullable|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            // Converter valores monetários para float
            $validated['price'] = (float) str_replace(['.', ','], ['', '.'], $validated['price']);
            $validated['cost'] = isset($validated['cost']) ? (float) str_replace(['.', ','], ['', '.'], $validated['cost']) : 0;

            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'cost' => $validated['cost'],
                'stock' => $validated['stock'] ?? 0,
                'min_stock' => $validated['min_stock'] ?? 0,
                'is_active' => $request->boolean('is_active')
            ]);

            Log::info('Produto criado com sucesso', ['product' => $product]);

            return redirect()->route('products.index')
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar produto', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar produto. Por favor, tente novamente.');
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            Log::info('Tentando atualizar produto', [
                'product_id' => $product->id,
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'cost' => 'nullable|numeric|min:0',
                'stock' => 'nullable|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'is_active' => 'boolean'
            ]);

            // Converter valores monetários para float
            $validated['price'] = (float) str_replace(['.', ','], ['', '.'], $validated['price']);
            $validated['cost'] = isset($validated['cost']) ? (float) str_replace(['.', ','], ['', '.'], $validated['cost']) : 0;

            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'cost' => $validated['cost'],
                'stock' => $validated['stock'] ?? 0,
                'min_stock' => $validated['min_stock'] ?? 0,
                'is_active' => $request->boolean('is_active')
            ]);

            Log::info('Produto atualizado com sucesso', ['product' => $product]);

            return redirect()->route('products.index')
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'data' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar produto. Por favor, tente novamente.');
        }
    }

    public function destroy(Product $product)
    {
        try {
            Log::info('Tentando excluir produto', ['product_id' => $product->id]);

            $product->delete();

            Log::info('Produto excluído com sucesso', ['product_id' => $product->id]);

            return redirect()->route('products.index')
                ->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir produto', [
                'error' => $e->getMessage(),
                'product_id' => $product->id
            ]);

            return back()
                ->with('error', 'Erro ao excluir produto. Por favor, tente novamente.');
        }
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'operation' => 'required|in:add,remove'
        ]);

        if ($validated['operation'] === 'add') {
            $product->stock += $validated['quantity'];
        } else {
            if ($product->stock < $validated['quantity']) {
                return back()->with('error', 'Quantidade insuficiente em estoque!');
            }
            $product->stock -= $validated['quantity'];
        }

        $product->save();

        return back()->with('success', 'Estoque atualizado com sucesso!');
    }

    /**
     * Display a listing of products with low stock.
     *
     * @return \Illuminate\View\View
     */
    public function lowStock()
    {
        $products = Product::whereRaw('stock <= min_stock')
            ->orderBy('stock', 'asc')
            ->paginate(10);

        return view('products.low-stock', compact('products'));
    }

    /**
     * Add stock to a specific product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $product->stock += $request->quantity;
            $product->save();

            DB::commit();

            return redirect()->route('products.low-stock')
                ->with('success', "Estoque do produto {$product->name} atualizado com sucesso!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.low-stock')
                ->with('error', 'Erro ao atualizar o estoque do produto. Por favor, tente novamente.');
        }
    }
} 