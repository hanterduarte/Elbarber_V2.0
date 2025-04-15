@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Nova Movimentação</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cash-register.movement.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de Movimentação</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="sale">Venda</option>
                                <option value="withdrawal">Saque</option>
                                <option value="deposit">Depósito</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Valor</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cash-register.index') }}" class="btn btn-secondary">Voltar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 