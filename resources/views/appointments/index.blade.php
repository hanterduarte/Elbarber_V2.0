@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Agendamentos</h1>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Agendamento
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Barbeiro</th>
                            <th>Serviços</th>
                            <th class="text-center">Duração</th>
                            <th class="text-end">Valor</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->start_time->format('d/m/Y H:i') }}</td>
                                <td>{{ $appointment->client->name }}</td>
                                <td>{{ $appointment->barber->user->name }}</td>
                                <td>
                                    @foreach($appointment->services as $service)
                                        <span class="badge bg-info">{{ $service->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">{{ $appointment->duration }} min</td>
                                <td class="text-end">R$ {{ number_format($appointment->total, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $statusClasses = [
                                            'scheduled' => 'bg-primary',
                                            'confirmed' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger'
                                        ];
                                        $statusLabels = [
                                            'scheduled' => 'Agendado',
                                            'confirmed' => 'Confirmado',
                                            'completed' => 'Concluído',
                                            'cancelled' => 'Cancelado'
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$appointment->status] }}">
                                        {{ $statusLabels[$appointment->status] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('appointments.show', $appointment) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('appointments.edit', $appointment) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('appointments.destroy', $appointment) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    title="Excluir" onclick="return confirm('Tem certeza?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Nenhum agendamento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection 