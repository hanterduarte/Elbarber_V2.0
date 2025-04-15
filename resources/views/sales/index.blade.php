@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Vendas</span>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">Nova Venda</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Produtos</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->client->name }}</td>
                                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @foreach($sale->products as $product)
                                                <span class="badge bg-primary">
                                                    {{ $product->name }} ({{ $product->pivot->quantity }}x)
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                                        <td>
                                            @switch($sale->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pendente</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Concluída</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelada</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.edit', $sale) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    Editar
                                                </a>
                                                @if($sale->status === 'pending')
                                                    <form action="{{ route('sales.complete', $sale) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-success btn-sm">
                                                            Concluir
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($sale->status === 'pending')
                                                    <form action="{{ route('sales.cancel', $sale) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Tem certeza que deseja cancelar esta venda?')">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma venda encontrada.</td>
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