@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalhes do Agendamento</h1>
        <div>
            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Informações do Agendamento</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Cliente:</strong> {{ $appointment->client->name }}</p>
                            <p><strong>Barbeiro:</strong> {{ $appointment->barber->user->name }}</p>
                            <p><strong>Data/Hora:</strong> {{ $appointment->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Duração:</strong> {{ $appointment->duration }} minutos</p>
                            <p><strong>Valor Total:</strong> R$ {{ number_format($appointment->total, 2, ',', '.') }}</p>
                            <p>
                                <strong>Status:</strong>
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
                            </p>
                        </div>
                    </div>

                    @if($appointment->notes)
                        <div class="mt-3">
                            <h6>Observações:</h6>
                            <p>{{ $appointment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Serviços</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th class="text-center">Duração</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointment->services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td class="text-center">{{ $service->pivot->duration }} min</td>
                                        <td class="text-end">R$ {{ number_format($service->pivot->price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th class="text-end">R$ {{ number_format($appointment->total, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pagamentos</h5>
                    @if($appointment->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointment->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->paymentMethod->name }}</td>
                                            <td class="text-end">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Pago:</th>
                                        <th class="text-end">R$ {{ number_format($appointment->payments->sum('amount'), 2, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Nenhum pagamento registrado.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Histórico</h5>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <p class="mb-0">Agendamento criado em {{ $appointment->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($appointment->updated_at != $appointment->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <p class="mb-0">Última atualização em {{ $appointment->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 15px;
    }
    .timeline-marker {
        position: absolute;
        left: -20px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #6c757d;
    }
    .timeline-content {
        padding-left: 10px;
    }
</style>
@endpush
@endsection 