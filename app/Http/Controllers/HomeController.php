<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        // Removido o middleware auth pois já está sendo aplicado nas rotas
    }

    public function index()
    {
        // Obtém as estatísticas do dia
        $today = Carbon::today();
        
        $todayAppointments = Appointment::whereDate('start_time', $today)->count();
        $todaySales = Sale::whereDate('created_at', $today)->count();
        $todayRevenue = Sale::whereDate('created_at', $today)->sum('final_total');
        
        // Obtém os próximos agendamentos
        $upcomingAppointments = Appointment::with(['client', 'barber'])
            ->where('start_time', '>=', $today)
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->take(5)
            ->get();
        
        // Obtém os produtos com estoque baixo (menos de 5 unidades)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(5)
            ->get();
        
        // Obtém o total de clientes
        $totalClients = Client::count();
        
        return view('dashboard', compact(
            'todayAppointments',
            'todaySales',
            'todayRevenue',
            'upcomingAppointments',
            'lowStockProducts',
            'totalClients'
        ));
    }
} 