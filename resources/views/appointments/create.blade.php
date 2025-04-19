@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Novo Agendamento</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('appointments.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="client_id">Cliente</label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="services">Serviços</label>
                            <select name="services[]" id="services" class="form-control @error('services') is-invalid @enderror" multiple required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ in_array($service->id, old('services', [])) ? 'selected' : '' }}>
                                        {{ $service->name }} - R$ {{ number_format($service->price, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('services')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="barber_id">Barbeiro</label>
                            <select name="barber_id" id="barber_id" class="form-control @error('barber_id') is-invalid @enderror" required>
                                <option value="">Selecione um barbeiro</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barber_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="date">Data</label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="time">Hora</label>
                            <input type="time" name="time" id="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                            @error('time')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Observações</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Agendar</button>
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o select2 para os serviços
        $('#services').select2({
            placeholder: 'Selecione os serviços',
            allowClear: true
        });
    });
</script>
@endpush
@endsection 