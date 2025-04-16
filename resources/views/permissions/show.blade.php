@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Detalhes da Permissão</h3>
                        <div>
                            @can('permissions.edit')
                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endcan
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> {{ $permission->id }}</p>
                            <p><strong>Nome:</strong> {{ $permission->name }}</p>
                            <p><strong>Criado em:</strong> {{ $permission->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Atualizado em:</strong> {{ $permission->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 