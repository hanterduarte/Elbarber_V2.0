@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>{{ $barbershop->name }}</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('barbershops.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('barbershops.edit', $barbershop) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações da Barbearia</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nome:</dt>
                        <dd class="col-sm-8">{{ $barbershop->name }}</dd>

                        <dt class="col-sm-4">Endereço:</dt>
                        <dd class="col-sm-8">{{ $barbershop->address }}</dd>

                        <dt class="col-sm-4">Data de Cadastro:</dt>
                        <dd class="col-sm-8">{{ $barbershop->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Total de Barbeiros:</dt>
                        <dd class="col-sm-8">{{ $barbershop->barbers->count() }}</dd>

                        <dt class="col-sm-4">Total de Vendas:</dt>
                        <dd class="col-sm-8">{{ $barbershop->sales->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Barbeiros Vinculados</h5>
                </div>
                <div class="card-body">
                    @if($barbershop->barbers->count() > 0)
                        <div class="list-group">
                            @foreach($barbershop->barbers as $barber)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $barber->user->name }}</h6>
                                            <small>{{ $barber->specialties }}</small>
                                        </div>
                                        <span class="badge bg-{{ $barber->status ? 'success' : 'danger' }}">
                                            {{ $barber->status ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Nenhum barbeiro vinculado a esta barbearia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Últimas Vendas</h5>
        </div>
        <div class="card-body">
            @if($barbershop->sales->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Cliente</th>
                                <th>Barbeiro</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barbershop->sales as $sale)
                                <tr>
                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->client->name ?? 'Cliente não informado' }}</td>
                                    <td>{{ $sale->barber->user->name ?? 'Não atribuído' }}</td>
                                    <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ $sale->status === 'completed' ? 'Concluída' : 'Pendente' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    Nenhuma venda registrada para esta barbearia.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 