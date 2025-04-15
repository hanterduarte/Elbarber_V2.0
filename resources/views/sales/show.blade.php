@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalhes da Venda #{{ $sale->id }}</h2>
        <div>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @if($sale->status !== 'completed')
                <form action="{{ route('sales.complete', $sale) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Concluir Venda
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações da Venda</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Data/Hora:</dt>
                        <dd class="col-sm-8">{{ $sale->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Cliente:</dt>
                        <dd class="col-sm-8">{{ $sale->customer_name }}</dd>

                        <dt class="col-sm-4">Barbeiro:</dt>
                        <dd class="col-sm-8">{{ $sale->barber->user->name }}</dd>

                        <dt class="col-sm-4">Forma de Pagamento:</dt>
                        <dd class="col-sm-8">{{ $sale->payment_method->name }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $sale->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                {{ $sale->status === 'completed' ? 'Concluída' : 'Pendente' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Observações:</dt>
                        <dd class="col-sm-8">{{ $sale->notes ?: 'Nenhuma observação' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resumo Financeiro</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Subtotal:</dt>
                        <dd class="col-sm-6 text-end">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</dd>

                        @if($sale->discount > 0)
                            <dt class="col-sm-6">Desconto:</dt>
                            <dd class="col-sm-6 text-end text-danger">- R$ {{ number_format($sale->discount, 2, ',', '.') }}</dd>
                        @endif

                        <dt class="col-sm-6 border-top pt-2">Total:</dt>
                        <dd class="col-sm-6 text-end border-top pt-2">
                            <strong>R$ {{ number_format($sale->total, 2, ',', '.') }}</strong>
                        </dd>

                        <dt class="col-sm-6">Comissão do Barbeiro ({{ $sale->barber->commission_rate }}%):</dt>
                        <dd class="col-sm-6 text-end">R$ {{ number_format($sale->total * ($sale->barber->commission_rate / 100), 2, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Itens da Venda</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Tipo</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr>
                                <td>
                                    @if($item->service_id)
                                        {{ $item->service->name }}
                                    @else
                                        {{ $item->product->name }}
                                    @endif
                                </td>
                                <td>{{ $item->service_id ? 'Serviço' : 'Produto' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end">R$ {{ number_format($sale->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        @if($sale->discount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Desconto:</strong></td>
                                <td class="text-end text-danger">- R$ {{ number_format($sale->discount, 2, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong>R$ {{ number_format($sale->total, 2, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style media="print">
    .btn, .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid #ddd !important;
    }
</style>
@endpush
@endsection 