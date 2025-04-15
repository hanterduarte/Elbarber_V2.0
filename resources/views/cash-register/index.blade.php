@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Caixa</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($cashRegister)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Informações do Caixa</h6>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $cashRegister->status === 'open' ? 'success' : 'danger' }}">
                                        {{ $cashRegister->status === 'open' ? 'Aberto' : 'Fechado' }}
                                    </span>
                                </p>
                                <p><strong>Aberto em:</strong> {{ $cashRegister->opened_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Saldo Inicial:</strong> R$ {{ number_format($cashRegister->opening_balance, 2, ',', '.') }}</p>
                                <p><strong>Total de Vendas:</strong> R$ {{ number_format($cashRegister->total_sales, 2, ',', '.') }}</p>
                                <p><strong>Total de Saques:</strong> R$ {{ number_format($cashRegister->total_withdrawals, 2, ',', '.') }}</p>
                                <p><strong>Total de Depósitos:</strong> R$ {{ number_format($cashRegister->total_deposits, 2, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Movimentações</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cashRegister->movements as $movement)
                                                <tr>
                                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $movement->type }}</td>
                                                    <td>R$ {{ number_format($movement->amount, 2, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhuma movimentação registrada.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if ($cashRegister->status === 'open')
                            <form action="{{ route('cash-register.close') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="closing_balance" class="form-label">Saldo Final</label>
                                    <input type="number" step="0.01" class="form-control" id="closing_balance" name="closing_balance" required>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Fechar Caixa</button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('cash-register.open') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="opening_balance" class="form-label">Saldo Inicial</label>
                                <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Observações</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Abrir Caixa</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 