@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Abrir Caixa') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('cash-register.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="opening_balance" class="col-md-4 col-form-label text-md-right">{{ __('Saldo Inicial') }}</label>

                            <div class="col-md-6">
                                <input id="opening_balance" type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" name="opening_balance" value="{{ old('opening_balance') }}" required autofocus>

                                @error('opening_balance')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Abrir Caixa') }}
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