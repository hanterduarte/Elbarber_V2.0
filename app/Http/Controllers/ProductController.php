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

            // Validar os dados antes de converter os valores monetários
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required',
                'cost' => 'nullable',
                'stock' => 'nullable|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Preparar os dados validados
            $validated = $request->only([
                'name', 'description', 'stock', 'min_stock', 'is_active'
            ]);

            // Converter e validar valores monetários
            $price = str_replace(['.', ','], ['', '.'], $request->price);
            if (!is_numeric($price)) {
                throw new \Exception('O preço informado é inválido.');
            }
            $validated['price'] = (float) $price;

            if ($request->filled('cost')) {
                $cost = str_replace(['.', ','], ['', '.'], $request->cost);
                if (!is_numeric($cost)) {
                    throw new \Exception('O custo informado é inválido.');
                }
                $validated['cost'] = (float) $cost;
            } else {
                $validated['cost'] = 0;
            }

            // Validar se os valores são positivos
            if ($validated['price'] < 0) {
                throw new \Exception('O preço não pode ser negativo.');
            }
            if ($validated['cost'] < 0) {
                throw new \Exception('O custo não pode ser negativo.');
            }

            // Tratar upload de imagem
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $product = Product::create($validated);

            Log::info('Produto criado com sucesso', ['product' => $product]);

            return redirect()->route('products.index')
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar produto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Erro ao criar produto. Por favor, tente novamente.');
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

            // Validar os dados antes de converter os valores monetários
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required',
                'cost' => 'nullable',
                'stock' => 'nullable|integer|min:0',
                'min_stock' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'remove_image' => 'nullable|boolean'
            ]);

            // Preparar os dados validados
            $validated = $request->only([
                'name', 'description', 'stock', 'min_stock', 'is_active'
            ]);

            // Converter e validar valores monetários
            $price = str_replace(['.', ','], ['', '.'], $request->price);
            if (!is_numeric($price)) {
                throw new \Exception('O preço informado é inválido.');
            }
            $validated['price'] = (float) $price;

            if ($request->filled('cost')) {
                $cost = str_replace(['.', ','], ['', '.'], $request->cost);
                if (!is_numeric($cost)) {
                    throw new \Exception('O custo informado é inválido.');
                }
                $validated['cost'] = (float) $cost;
            } else {
                $validated['cost'] = 0;
            }

            // Validar se os valores são positivos
            if ($validated['price'] < 0) {
                throw new \Exception('O preço não pode ser negativo.');
            }
            if ($validated['cost'] < 0) {
                throw new \Exception('O custo não pode ser negativo.');
            }

            // Remover a imagem se solicitado
            if ($request->boolean('remove_image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $validated['image'] = null;
            }
            // Tratar upload de nova imagem
            elseif ($request->hasFile('image')) {
                // Excluir imagem antiga se existir
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $product->update($validated);

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
                ->with('error', $e->getMessage() ?: 'Erro ao atualizar produto. Por favor, tente novamente.');
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
        try {
            Log::info('Tentando adicionar estoque ao produto', [
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);

            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $product->stock += $validated['quantity'];
            $product->save();

            Log::info('Estoque adicionado com sucesso', [
                'product_id' => $product->id,
                'new_stock' => $product->stock
            ]);

            return back()->with('success', 'Estoque adicionado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar estoque', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);

            return back()->with('error', 'Erro ao adicionar estoque. Por favor, tente novamente.');
        }
    }
} 