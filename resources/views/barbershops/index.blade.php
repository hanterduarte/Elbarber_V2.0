@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Barbearias</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('barbershops.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Barbearia
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Endereço</th>
                            <th>Data de Cadastro</th>
                            <th>Barbeiros</th>
                            <th>Vendas</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barbershops as $barbershop)
                            <tr>
                                <td>{{ $barbershop->name }}</td>
                                <td>{{ $barbershop->address }}</td>
                                <td>{{ $barbershop->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $barbershop->barbers_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $barbershop->sales_count }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('barbershops.show', $barbershop) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('barbershops.edit', $barbershop) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('barbershops.destroy', $barbershop) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta barbearia?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhuma barbearia cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-group {
        gap: 5px;
    }
</style>
@endpush 