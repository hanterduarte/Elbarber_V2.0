@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalhes do Barbeiro</h2>
        <div>
            <a href="{{ route('barbers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('barbers.edit', $barber) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações Pessoais</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $barber->user->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $barber->user->email }}</dd>

                        <dt class="col-sm-4">Telefone:</dt>
                        <dd class="col-sm-8">{{ $barber->phone }}</dd>

                        <dt class="col-sm-4">Barbearia:</dt>
                        <dd class="col-sm-8">{{ $barber->barbershop->name }}</dd>

                        <dt class="col-sm-4">Taxa de Comissão:</dt>
                        <dd class="col-sm-8">{{ $barber->commission_rate }}%</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $barber->status ? 'bg-success' : 'bg-danger' }}">
                                {{ $barber->status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Especialidades:</dt>
                        <dd class="col-sm-8">{{ $barber->specialties ?: 'Não informado' }}</dd>

                        <dt class="col-sm-4">Cadastrado em:</dt>
                        <dd class="col-sm-8">{{ $barber->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Última atualização:</dt>
                        <dd class="col-sm-8">{{ $barber->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Últimas Vendas</h5>
                </div>
                <div class="card-body">
                    @if($barber->sales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barber->sales->take(5) as $sale)
                                        <tr>
                                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $sale->customer_name }}</td>
                                            <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                            <td>
                                                <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $sale->status === 'completed' ? 'Concluída' : 'Pendente' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <a href="{{ route('sales.index', ['barber_id' => $barber->id]) }}" class="btn btn-sm btn-primary">
                                Ver todas as vendas
                            </a>
                        </div>
                    @else
                        <p class="text-muted mb-0">Nenhuma venda registrada.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estatísticas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h6>Total de Vendas (Mês)</h6>
                                <h4>{{ $barber->sales->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h6>Faturamento (Mês)</h6>
                                <h4>R$ {{ number_format($barber->sales->where('created_at', '>=', now()->startOfMonth())->sum('total'), 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 