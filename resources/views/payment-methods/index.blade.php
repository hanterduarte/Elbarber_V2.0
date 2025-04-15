@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Métodos de Pagamento</h5>
                    <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Método
                    </a>
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
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentMethods as $paymentMethod)
                                    <tr>
                                        <td>{{ $paymentMethod->name }}</td>
                                        <td>{{ $paymentMethod->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $paymentMethod->is_active ? 'success' : 'danger' }}">
                                                {{ $paymentMethod->is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('payment-methods.edit', $paymentMethod) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum método de pagamento encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $paymentMethods->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 