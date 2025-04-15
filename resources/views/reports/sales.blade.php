@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Relatório de Vendas
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.sales') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Data Inicial</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Data Final</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Forma de Pagamento</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="">Todas</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                        <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>Cartão de Débito</option>
                                        <option value="pix" {{ request('payment_method') == 'pix' ? 'selected' : '' }}>PIX</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Forma de Pagamento</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sale->client->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                                        <td>R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhuma venda encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th>R$ {{ number_format($sales->sum('total_amount'), 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 