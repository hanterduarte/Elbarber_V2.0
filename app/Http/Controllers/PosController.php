<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Client;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

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
} 