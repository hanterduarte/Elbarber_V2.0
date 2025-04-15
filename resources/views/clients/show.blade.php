@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Detalhes do Cliente</span>
                    <div class="btn-group">
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informações Pessoais</h5>
                            <table class="table">
                                <tr>
                                    <th>Nome:</th>
                                    <td>{{ $client->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $client->email }}</td>
                                </tr>
                                <tr>
                                    <th>Telefone:</th>
                                    <td>{{ $client->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Data de Nascimento:</th>
                                    <td>{{ $client->birth_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Data de Cadastro:</th>
                                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Endereço</h5>
                            <table class="table">
                                <tr>
                                    <th>Endereço:</th>
                                    <td>{{ $client->address }}</td>
                                </tr>
                                <tr>
                                    <th>Cidade:</th>
                                    <td>{{ $client->city }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>{{ $client->state }}</td>
                                </tr>
                                <tr>
                                    <th>CEP:</th>
                                    <td>{{ $client->zip_code }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($client->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Observações</h5>
                                <p>{{ $client->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Histórico de Serviços</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Serviço</th>
                                            <th>Barbeiro</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($client->appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->date->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @foreach($appointment->services as $service)
                                                        <span class="badge bg-primary">{{ $service->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $appointment->barber->name }}</td>
                                                <td>R$ {{ number_format($appointment->total_amount, 2, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Nenhum serviço realizado.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Histórico de Compras</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Produto</th>
                                            <th>Quantidade</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($client->sales as $sale)
                                            @foreach($sale->products as $product)
                                                <tr>
                                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->pivot->quantity }}</td>
                                                    <td>R$ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Nenhuma compra realizada.</td>
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
    </div>
</div>
@endsection 