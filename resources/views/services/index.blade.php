@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Serviços') }}</h5>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Novo Serviço') }}
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Nome') }}</th>
                                    <th>{{ __('Preço') }}</th>
                                    <th>{{ __('Duração') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                                        <td>{{ $service->duration }} min</td>
                                        <td>
                                            <span class="badge badge-{{ $service->is_active ? 'success' : 'danger' }}">
                                                {{ $service->is_active ? __('Ativo') : __('Inativo') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Tem certeza que deseja excluir este serviço?') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('Nenhum serviço cadastrado.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 