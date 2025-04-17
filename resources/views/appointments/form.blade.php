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
                    @if (isset($appointment))
                        <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                        @method('PUT')
                    @else
                        <form action="{{ route('appointments.store') }}" method="POST">
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
                            <label for="date" class="form-label">Data e Hora <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date', isset($appointment) ? $appointment->date->format('Y-m-d\TH:i') : '') }}" 
                                   required>
                            @error('date')
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
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="services[]" 
                                                   value="{{ $service->id }}" 
                                                   id="service_{{ $service->id }}"
                                                   {{ isset($appointment) && $appointment->services->contains($service->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                {{ $service->name }} - R$ {{ number_format($service->price, 2, ',', '.') }}
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
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar se pelo menos um serviço foi selecionado
        const services = form.querySelectorAll('input[name="services[]"]:checked');
        if (services.length === 0) {
            alert('Selecione pelo menos um serviço!');
            return;
        }

        // Desabilitar o botão para evitar múltiplos envios
        submitButton.disabled = true;
        submitButton.innerHTML = 'Salvando...';

        // Enviar o formulário
        fetch(form.action, {
            method: form.method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form)))
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        const json = JSON.parse(text);
                        throw new Error(json.message || 'Erro ao salvar agendamento.');
                    } catch (e) {
                        console.error('Resposta do servidor:', text);
                        throw new Error('Erro ao salvar agendamento. Por favor, tente novamente.');
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || '{{ route('appointments.index') }}';
            } else {
                throw new Error(data.message || 'Erro ao salvar agendamento.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
            submitButton.disabled = false;
            submitButton.innerHTML = 'Salvar';
        });
    });
});
</script>
@endpush 