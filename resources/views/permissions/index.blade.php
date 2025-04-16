@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Permissões</h3>
                        @can('permissions.create')
                            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nova Permissão
                            </a>
                        @endcan
                    </div>
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
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->id }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>
                                            @can('permissions.edit')
                                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            @endcan
                                            @can('permissions.destroy')
                                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta permissão?')">
                                                        <i class="fas fa-trash"></i> Excluir
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 