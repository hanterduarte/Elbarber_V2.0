@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalhes do Agendamento</h3>
                    <div class="card-tools">
                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('appointments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informações do Cliente</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nome</th>
                                    <td>{{ $appointment->client->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $appointment->client->email }}</td>
                                </tr>
                                <tr>
                                    <th>Telefone</th>
                                    <td>{{ $appointment->client->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informações do Serviço</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Serviço</th>
                                    <td>{{ $appointment->service->name }}</td>
                                </tr>
                                <tr>
                                    <th>Preço</th>
                                    <td>R$ {{ number_format($appointment->service->price, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Duração</th>
                                    <td>{{ $appointment->service->duration }} minutos</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Informações do Barbeiro</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nome</th>
                                    <td>{{ $appointment->barber->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $appointment->barber->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informações do Agendamento</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Data</th>
                                    <td>{{ $appointment->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Hora</th>
                                    <td>{{ $appointment->time }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'info') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($appointment->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Observações</h5>
                                <div class="card">
                                    <div class="card-body">
                                        {{ $appointment->notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 