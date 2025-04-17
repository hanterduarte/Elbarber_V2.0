@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Produtos</h2>
        <div>
            <a href="{{ route('products.low-stock') }}" class="btn btn-warning me-2">
                <i class="fas fa-exclamation-triangle"></i> Estoque Baixo
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Produto
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Custo</th>
                            <th>Estoque</th>
                            <th>Estoque Mínimo</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 50px;">
                                    @else
                                        <span class="text-muted">Sem imagem</span>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($product->cost, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $product->stock <= $product->min_stock ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>{{ $product->min_stock }}</td>
                                <td>
                                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addStockModal{{ $product->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="document.getElementById('delete-form-{{ $product->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $product->id }}" 
                                          action="{{ route('products.destroy', $product) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <!-- Modal Adicionar Estoque -->
                                    <div class="modal fade" 
                                         id="addStockModal{{ $product->id }}" 
                                         tabindex="-1" 
                                         aria-labelledby="addStockModalLabel{{ $product->id }}" 
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('products.add-stock', $product) }}" method="POST">
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
                                                            <label for="current_stock" class="form-label">
                                                                Estoque Atual
                                                            </label>
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   id="current_stock" 
                                                                   value="{{ $product->stock }}" 
                                                                   readonly>
                                                        </div>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Nenhum produto cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $products->links() }}
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

                // Fechar o modal após o envio
                const modal = this.closest('.modal');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                
                this.submit();
                
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        });

        // Manipular exclusão de produto
        const deleteForms = document.querySelectorAll('form[id^="delete-form-"]');
        deleteForms.forEach(form => {
            const deleteButton = document.querySelector(`button[onclick="document.getElementById('${form.id}').submit();"]`);
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Tem certeza que deseja excluir este produto?')) {
                        form.submit();
                    }
                });
            }
        });
    });
</script>
@endpush 