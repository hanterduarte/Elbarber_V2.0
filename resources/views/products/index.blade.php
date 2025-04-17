@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Adicionar estilos CSS para o zoom da imagem -->
    <style>
        .product-image-container {
            position: relative;
            display: inline-block;
        }
        
        .product-image-zoom {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .product-image-preview {
            display: none;
            position: fixed;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .product-image-preview img {
            max-width: 300px;
            max-height: 300px;
            object-fit: contain;
        }
    </style>

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
                                        <div class="product-image-container">
                                            @php
                                                $imagePath = asset('storage/' . $product->image);
                                                $imageExists = file_exists(public_path('storage/' . $product->image));
                                            @endphp
                                            
                                            @if($imageExists)
                                                <img src="{{ $imagePath }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="img-thumbnail product-image-zoom" 
                                                     style="max-width: 50px; max-height: 50px; object-fit: cover;"
                                                     data-full-image="{{ $imagePath }}">
                                                <div class="product-image-preview">
                                                    <img src="{{ $imagePath }}" 
                                                         alt="{{ $product->name }}">
                                                </div>
                                            @else
                                                <span class="text-danger">Imagem não encontrada: {{ $product->image }}</span>
                                            @endif
                                        </div>
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
                                                class="btn btn-sm btn-danger delete-product" 
                                                data-product-id="{{ $product->id }}">
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
        const deleteButtons = document.querySelectorAll('.delete-product');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                const form = document.getElementById(`delete-form-${productId}`);
                
                if (confirm('Tem certeza que deseja excluir este produto?')) {
                    form.submit();
                }
            });
        });

        // Image zoom functionality
        const productImages = document.querySelectorAll('.product-image-zoom');
        productImages.forEach(img => {
            const container = img.closest('.product-image-container');
            const preview = container.querySelector('.product-image-preview');
            
            // Show preview on hover
            img.addEventListener('mouseenter', function() {
                if (preview) {
                    const rect = img.getBoundingClientRect();
                    preview.style.display = 'block';
                    preview.style.left = rect.right + 10 + 'px';
                    preview.style.top = rect.top + 'px';
                }
            });
            
            // Hide preview when mouse leaves
            container.addEventListener('mouseleave', function() {
                if (preview) {
                    preview.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush 