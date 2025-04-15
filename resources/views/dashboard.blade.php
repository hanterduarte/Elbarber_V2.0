@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Cards de Estatísticas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Agendamentos Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayAppointments }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Vendas Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todaySales }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Receita Hoje</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($todayRevenue, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total de Clientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClients }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximos Agendamentos -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Próximos Agendamentos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Serviço</th>
                                    <th>Data/Hora</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->client->name }}</td>
                                        <td>{{ $appointment->service->name }}</td>
                                        <td>{{ $appointment->start_time->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @switch($appointment->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pendente</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">Confirmado</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Concluído</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelado</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum agendamento encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos com Estoque Baixo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produtos com Estoque Baixo</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Nenhum produto com estoque baixo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.icon-shape {
    width: 48px;
    height: 48px;
    background-position: center;
    border-radius: 0.75rem;
}
.icon-shape i {
    color: #fff;
    opacity: 0.8;
    top: 11px;
    position: relative;
}
.bg-gradient-primary {
    background: linear-gradient(310deg,#7928CA,#FF0080);
}
.bg-gradient-success {
    background: linear-gradient(310deg,#17ad37,#98ec2d);
}
.bg-gradient-warning {
    background: linear-gradient(310deg,#f53939,#fbcf33);
}
.bg-gradient-info {
    background: linear-gradient(310deg,#2152ff,#21d4fd);
}
</style>
@endpush 