@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Fechar Caixa') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('cash-register.close', $cashRegister) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Saldo Inicial') }}</label>
                            <div class="col-md-6">
                                <p class="form-control-static">R$ {{ number_format($cashRegister->opening_balance, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Total de Vendas') }}</label>
                            <div class="col-md-6">
                                <p class="form-control-static">R$ {{ number_format($cashRegister->total_sales, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Total de Despesas') }}</label>
                            <div class="col-md-6">
                                <p class="form-control-static">R$ {{ number_format($cashRegister->total_expenses, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Saldo Atual') }}</label>
                            <div class="col-md-6">
                                <p class="form-control-static">R$ {{ number_format($cashRegister->current_balance, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="closing_balance" class="col-md-4 col-form-label text-md-right">{{ __('Saldo Final') }}</label>

                            <div class="col-md-6">
                                <input id="closing_balance" type="number" step="0.01" class="form-control @error('closing_balance') is-invalid @enderror" name="closing_balance" value="{{ old('closing_balance') }}" required>

                                @error('closing_balance')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">{{ __('Observações') }}</label>

                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>

                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Fechar Caixa') }}
                                </button>
                                <a href="{{ route('cash-register.index') }}" class="btn btn-secondary">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 