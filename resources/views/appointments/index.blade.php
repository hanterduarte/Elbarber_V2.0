@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Agendamentos</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Agendamento
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Barbeiro</th>
                            <th>Serviço</th>
                            <th>Data/Hora</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->client->name }}</td>
                                <td>{{ $appointment->barber->name }}</td>
                                <td>{{ $appointment->service->name }}</td>
                                <td>{{ $appointment->start_time->format('d/m/Y H:i') }}</td>
                                <td>
                                    @switch($appointment->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pendente</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge bg-info">Confirmado</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Concluído</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($appointment->status == 'pending')
                                            <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($appointment->status == 'confirmed')
                                            <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check2-all"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum agendamento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 