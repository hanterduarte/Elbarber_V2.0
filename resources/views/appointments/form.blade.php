@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ isset($appointment) ? 'Editar Agendamento' : 'Novo Agendamento' }}
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (isset($appointment))
                        <form action="{{ route('appointments.update', $appointment) }}" method="POST" id="appointment-form">
                        @method('PUT')
                    @else
                        <form action="{{ route('appointments.store') }}" method="POST" id="appointment-form">
                    @endif
                        @csrf

                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" 
                                    name="client_id" 
                                    required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ old('client_id', $appointment->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="barber_id" class="form-label">Barbeiro <span class="text-danger">*</span></label>
                            <select class="form-select @error('barber_id') is-invalid @enderror" 
                                    id="barber_id" 
                                    name="barber_id" 
                                    required>
                                <option value="">Selecione um barbeiro</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" 
                                            {{ old('barber_id', $appointment->barber_id ?? '') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barber_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label">Data e Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" 
                                   name="start_time" 
                                   value="{{ old('start_time', isset($appointment) ? $appointment->start_time->format('Y-m-d\TH:i') : '') }}" 
                                   required>
                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Serviços <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input service-checkbox" 
                                                   type="checkbox" 
                                                   name="services[]" 
                                                   value="{{ $service->id }}" 
                                                   id="service_{{ $service->id }}"
                                                   {{ in_array($service->id, old('services', isset($appointment) ? $appointment->services->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                {{ $service->name }} - R$ {{ number_format($service->price, 2, ',', '.') }}
                                                <small class="text-muted">({{ $service->duration }} min)</small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('services')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $appointment->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="submit-button">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Erro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="errorModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('appointment-form');
    const submitButton = document.getElementById('submit-button');
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar se pelo menos um serviço foi selecionado
        const selectedServices = Array.from(serviceCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedServices.length === 0) {
            showError('Selecione pelo menos um serviço!');
            return;
        }

        // Desabilitar o botão para evitar múltiplos envios
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

        // Criar FormData com todos os campos do formulário
        const formData = new FormData(form);
        
        // Log dos dados que serão enviados
        console.log('Dados do formulário:', {
            client_id: formData.get('client_id'),
            barber_id: formData.get('barber_id'),
            start_time: formData.get('start_time'),
            services: formData.getAll('services[]'),
            notes: formData.get('notes')
        });

        // Enviar o formulário
        fetch(form.action, {
            method: form.method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    console.log('Error response:', data);
                    throw new Error(data.message || 'Erro ao salvar agendamento.');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if (data.success) {
                window.location.href = data.redirect || '{{ route('appointments.index') }}';
            } else {
                throw new Error(data.message || 'Erro ao salvar agendamento.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(error.message || 'Erro ao salvar agendamento. Por favor, tente novamente.');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Salvar';
        });
    });

    function showError(message) {
        document.getElementById('errorModalBody').textContent = message;
        errorModal.show();
    }
});
</script>
@endpush 