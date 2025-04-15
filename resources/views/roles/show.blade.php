@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Detalhes da Função</div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Nome</h5>
                            <p>{{ $role->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Permissões</h5>
                            <div class="row">
                                @foreach($role->permissions as $permission)
                                    <div class="col-md-3">
                                        <span class="badge bg-primary">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Voltar</a>
                        @can('roles.edit')
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">Editar</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 