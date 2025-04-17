@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Produtos com Estoque Baixo</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Voltar</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Estoque Atual</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Preço de Custo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="{{ $product->stock === 0 ? 'table-danger' : 'table-warning' }}">
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->stock }}</td>
                                        <td>{{ $product->min_stock }}</td>
                                        <td>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#addStockModal{{ $product->id }}">
                                                Adicionar Estoque
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal para adicionar estoque -->
                                    <div class="modal fade" 
                                         id="addStockModal{{ $product->id }}" 
                                         tabindex="-1" 
                                         aria-labelledby="addStockModalLabel{{ $product->id }}" 
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('products.add-stock', $product->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addStockModalLabel{{ $product->id }}">
                                                            Adicionar Estoque - {{ $product->name }}
                                                        </h5>
                                                        <button type="button" 
                                                                class="btn-close" 
                                                                data-bs-dismiss="modal" 
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="quantity" class="form-label">
                                                                Quantidade a Adicionar
                                                            </label>
                                                            <input type="number" 
                                                                   class="form-control @error('quantity') is-invalid @enderror" 
                                                                   id="quantity" 
                                                                   name="quantity" 
                                                                   required 
                                                                   min="1">
                                                            @error('quantity')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" 
                                                                class="btn btn-secondary" 
                                                                data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            Adicionar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum produto com estoque baixo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manipular envio do formulário de adicionar estoque
        const addStockForms = document.querySelectorAll('form[action*="add-stock"]');
        addStockForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const quantity = this.querySelector('input[name="quantity"]').value;
                if (!quantity || quantity < 1) {
                    alert('Por favor, insira uma quantidade válida.');
                    return;
                }

                // Desabilitar o botão de submit para evitar múltiplos envios
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                
                // Enviar o formulário
                this.submit();
            });
        });
    });
</script>
@endpush 