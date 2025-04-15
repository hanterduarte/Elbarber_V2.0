<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\CashRegister;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function edit(Product $product)
    {
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
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