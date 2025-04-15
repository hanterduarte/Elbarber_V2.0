<?php

namespace App\Http\Controllers;

use App\Models\{Sale, Service, Product, Barber};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function services(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        $barberId = $request->barber_id;

        $query = Sale::with(['items.service', 'barber.user'])
            ->whereHas('items', function ($q) {
                $q->whereNotNull('service_id');
            })
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->where('status', 'COMPLETED');

        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        $sales = $query->get();

        // Agrupa os serviços
        $services = [];
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                if ($item->service) {
                    $serviceId = $item->service->id;
                    if (!isset($services[$serviceId])) {
                        $services[$serviceId] = [
                            'name' => $item->service->name,
                            'quantity' => 0,
                            'total' => 0
                        ];
                    }
                    $services[$serviceId]['quantity'] += $item->quantity;
                    $services[$serviceId]['total'] += $item->total_price;
                }
            }
        }

        return view('reports.services', compact('services', 'startDate', 'endDate', 'sales'));
    }

    public function sales(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        $barberId = $request->barber_id;

        $query = Sale::with(['items.product', 'items.service', 'barber.user', 'paymentMethod'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->where('status', 'COMPLETED');

        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        $sales = $query->get();

        // Calcula totais por forma de pagamento
        $paymentTotals = $sales->groupBy('payment_method.name')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount')
                ];
            });

        // Calcula totais por barbeiro
        $barberTotals = $sales->groupBy('barber.user.name')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount')
                ];
            });

        return view('reports.sales', compact(
            'sales',
            'startDate',
            'endDate',
            'paymentTotals',
            'barberTotals'
        ));
    }

    public function products(Request $request)
    {
        $query = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed');

        if ($request->filled('start_date')) {
            $query->where('sales.created_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('sales.created_at', '<=', $request->end_date . ' 23:59:59');
        }

        if ($request->filled('product_id')) {
            $query->where('products.id', $request->product_id);
        }

        // Dados para a tabela principal
        $productSales = $query->select([
            'products.name',
            DB::raw('SUM(sale_items.quantity) as quantity'),
            'products.price',
            'products.cost',
            DB::raw('SUM(sale_items.quantity * sale_items.price) as total'),
            DB::raw('SUM(sale_items.quantity * (sale_items.price - products.cost)) as profit'),
            DB::raw('(SUM(sale_items.quantity * (sale_items.price - products.cost)) / SUM(sale_items.quantity * sale_items.price)) * 100 as margin')
        ])
        ->groupBy('products.id', 'products.name', 'products.price', 'products.cost')
        ->orderBy('quantity', 'desc')
        ->paginate(10);

        // Top 10 produtos mais vendidos para o gráfico
        $topProducts = $query->select([
            'products.name',
            DB::raw('SUM(sale_items.quantity) as quantity')
        ])
        ->groupBy('products.id', 'products.name')
        ->orderBy('quantity', 'desc')
        ->limit(10)
        ->get();

        // Vendas por mês para o gráfico
        $monthlySales = $query->select([
            DB::raw("DATE_FORMAT(sales.created_at, '%Y-%m') as month"),
            DB::raw('SUM(sale_items.quantity * sale_items.price) as total')
        ])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Totais para os cards
        $totals = $query->select([
            DB::raw('SUM(sale_items.quantity) as totalQuantity'),
            DB::raw('SUM(sale_items.quantity * sale_items.price) as totalRevenue'),
            DB::raw('SUM(sale_items.quantity * (sale_items.price - products.cost)) as totalProfit')
        ])->first();

        $totalQuantity = $totals->totalQuantity ?? 0;
        $totalRevenue = $totals->totalRevenue ?? 0;
        $totalProfit = $totals->totalProfit ?? 0;
        $averageTicket = $totalQuantity > 0 ? $totalRevenue / $totalQuantity : 0;

        // Lista de produtos para o filtro
        $products = Product::orderBy('name')->get();

        return view('reports.products', compact(
            'productSales',
            'topProducts',
            'monthlySales',
            'products',
            'totalQuantity',
            'totalRevenue',
            'totalProfit',
            'averageTicket'
        ));
    }

    public function appointments(Request $request)
    {
        $query = DB::table('appointments')
            ->join('barbers', 'appointments.barber_id', '=', 'barbers.id')
            ->join('users', 'barbers.user_id', '=', 'users.id')
            ->join('clients', 'appointments.client_id', '=', 'clients.id')
            ->leftJoin('appointment_service', 'appointments.id', '=', 'appointment_service.appointment_id')
            ->leftJoin('services', 'appointment_service.service_id', '=', 'services.id');

        if ($request->filled('start_date')) {
            $query->where('appointments.scheduled_at', '>=', $request->start_date . ' 00:00:00');
        }

        if ($request->filled('end_date')) {
            $query->where('appointments.scheduled_at', '<=', $request->end_date . ' 23:59:59');
        }

        if ($request->filled('barber_id')) {
            $query->where('appointments.barber_id', $request->barber_id);
        }

        if ($request->filled('status')) {
            $query->where('appointments.status', $request->status);
        }

        // Dados principais dos agendamentos
        $appointments = $query->select([
            'appointments.*',
            'clients.name as client_name',
            'users.name as barber_name',
            DB::raw('GROUP_CONCAT(services.name) as services_list'),
            DB::raw('SUM(services.duration) as total_duration'),
            DB::raw('SUM(services.price) as total_value')
        ])
        ->groupBy('appointments.id')
        ->orderBy('appointments.scheduled_at', 'desc')
        ->paginate(10);

        // Estatísticas gerais
        $stats = $query->select([
            DB::raw('COUNT(*) as total_appointments'),
            DB::raw('SUM(CASE WHEN appointments.status = "completed" THEN 1 ELSE 0 END) as completed_count'),
            DB::raw('SUM(CASE WHEN appointments.status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count')
        ])->first();

        $totalAppointments = $stats->total_appointments ?? 0;
        $completedCount = $stats->completed_count ?? 0;
        $cancelledCount = $stats->cancelled_count ?? 0;

        $attendanceRate = $totalAppointments > 0 
            ? ($completedCount / $totalAppointments) * 100 
            : 0;

        $cancellationRate = $totalAppointments > 0 
            ? ($cancelledCount / $totalAppointments) * 100 
            : 0;

        // Agendamentos por dia da semana
        $weekdayData = $query->select([
            DB::raw('DAYNAME(appointments.scheduled_at) as day_name'),
            DB::raw('COUNT(*) as count')
        ])
        ->groupBy('day_name')
        ->orderBy(DB::raw('DAYOFWEEK(appointments.scheduled_at)'))
        ->get();

        // Agendamentos por horário
        $timeData = $query->select([
            DB::raw('HOUR(appointments.scheduled_at) as hour'),
            DB::raw('COUNT(*) as count')
        ])
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

        // Média diária de agendamentos
        $dailyAverage = $query->select([
            DB::raw('COUNT(*) / COUNT(DISTINCT DATE(appointments.scheduled_at)) as daily_avg')
        ])->first()->daily_avg ?? 0;

        // Lista de barbeiros para o filtro
        $barbers = Barber::with('user')->orderBy('id')->get();

        return view('reports.appointments', compact(
            'appointments',
            'barbers',
            'totalAppointments',
            'attendanceRate',
            'cancellationRate',
            'dailyAverage',
            'weekdayData',
            'timeData'
        ));
    }
} 