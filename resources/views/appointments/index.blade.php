@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Agendamentos</h2>
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">Novo Agendamento</a>
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
                                    <th>Cliente</th>
                                    <th>Serviços</th>
                                    <th>Barbeiro</th>
                                    <th>Data/Hora</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->client->name }}</td>
                                        <td>
                                            @foreach($appointment->services as $service)
                                                <span class="badge bg-info">{{ $service->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $appointment->barber->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @switch($appointment->status)
                                                @case('scheduled')
                                                    <span class="badge bg-warning">Agendado</span>
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
                                        <td>R$ {{ number_format($appointment->total, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($appointment->status === 'scheduled')
                                                    <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($appointment->status === 'confirmed')
                                                    <form action="{{ route('appointments.complete', $appointment) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check-double"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                                    <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 