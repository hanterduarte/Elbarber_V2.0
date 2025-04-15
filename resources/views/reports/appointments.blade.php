@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Relatório de Agendamentos</h1>
        <button onclick="window.print()" class="btn btn-secondary no-print">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>

    <form method="GET" class="card mb-4 no-print">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Data Inicial</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">Data Final</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="barber_id">Barbeiro</label>
                        <select class="form-control" id="barber_id" name="barber_id">
                            <option value="">Todos os Barbeiros</option>
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->id }}" {{ request('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos os Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total de Agendamentos</h6>
                    <h2 class="card-title">{{ number_format($totalAppointments, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Taxa de Comparecimento</h6>
                    <h2 class="card-title">{{ number_format($attendanceRate, 1, ',', '.') }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Taxa de Cancelamento</h6>
                    <h2 class="card-title">{{ number_format($cancellationRate, 1, ',', '.') }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Média Diária</h6>
                    <h2 class="card-title">{{ number_format($dailyAverage, 1, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Agendamentos por Dia da Semana</h5>
                    <canvas id="weekdayChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Agendamentos por Horário</h5>
                    <canvas id="timeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Lista de Agendamentos</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->scheduled_at->format('d/m/Y H:i') }}</td>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </div>
    </div>
</div>

@push('styles')
<style media="print">
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .pagination {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do gráfico de agendamentos por dia da semana
    const weekdayCtx = document.getElementById('weekdayChart').getContext('2d');
    new Chart(weekdayCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weekdayData->pluck('day_name')) !!},
            datasets: [{
                label: 'Quantidade de Agendamentos',
                data: {!! json_encode($weekdayData->pluck('count')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Configuração do gráfico de agendamentos por horário
    const timeCtx = document.getElementById('timeChart').getContext('2d');
    new Chart(timeCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($timeData->pluck('hour')) !!},
            datasets: [{
                label: 'Quantidade de Agendamentos',
                data: {!! json_encode($timeData->pluck('count')) !!},
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection 