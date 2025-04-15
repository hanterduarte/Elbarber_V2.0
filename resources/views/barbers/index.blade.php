@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Barbeiros') }}</h5>
                    <a href="{{ route('barbers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Novo Barbeiro') }}
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
                                    <th>{{ __('Telefone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barbers as $barber)
                                    <tr>
                                        <td>{{ $barber->name }}</td>
                                        <td>{{ $barber->phone }}</td>
                                        <td>{{ $barber->email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $barber->is_active ? 'success' : 'danger' }}">
                                                {{ $barber->is_active ? __('Ativo') : __('Inativo') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('barbers.edit', $barber) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('barbers.destroy', $barber) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Tem certeza que deseja excluir este barbeiro?') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('Nenhum barbeiro cadastrado.') }}</td>
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

@push('styles')
<style>
    .btn-group {
        gap: 5px;
    }
</style>
@endpush 