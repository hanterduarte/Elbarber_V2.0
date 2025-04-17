@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ isset($product) ? 'Editar Produto' : 'Novo Produto' }}</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
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

            <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $product->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Preço <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $product->price ?? '') }}" 
                                       required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cost" class="form-label">Custo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('cost') is-invalid @enderror" 
                                       id="cost" 
                                       name="cost" 
                                       value="{{ old('cost', $product->cost ?? '') }}">
                            </div>
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Estoque Inicial</label>
                            <input type="number" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" 
                                   name="stock" 
                                   value="{{ old('stock', $product->stock ?? 0) }}" 
                                   {{ isset($product) ? 'readonly' : '' }}
                                   min="0">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="min_stock" class="form-label">Estoque Mínimo</label>
                            <input type="number" 
                                   class="form-control @error('min_stock') is-invalid @enderror" 
                                   id="min_stock" 
                                   name="min_stock" 
                                   value="{{ old('min_stock', $product->min_stock ?? 0) }}" 
                                   min="0">
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Imagem do Produto</label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($product) && $product->image)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;">
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Produto Ativo</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const priceInput = document.getElementById('price');
        const costInput = document.getElementById('cost');

        // Função para formatar valor monetário
        function formatCurrency(value) {
            if (!value) return '';
            
            // Remove tudo que não é número
            value = value.replace(/\D/g, '');
            
            // Converte para número e formata com 2 casas decimais
            value = (parseFloat(value) / 100).toFixed(2);
            
            // Formata para o padrão brasileiro
            return value.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Função para converter valor do formato brasileiro para o formato do backend
        function convertToBackendFormat(value) {
            if (!value) return '0';
            return value.replace(/\./g, '').replace(',', '.');
        }

        // Formata os valores monetários durante a digitação
        if (priceInput) {
            priceInput.addEventListener('input', function(e) {
                e.target.value = formatCurrency(e.target.value);
            });
        }

        if (costInput) {
            costInput.addEventListener('input', function(e) {
                e.target.value = formatCurrency(e.target.value);
            });
        }

        // Trata o envio do formulário
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Converte os valores para o formato do backend
                if (priceInput) {
                    priceInput.value = convertToBackendFormat(priceInput.value);
                }
                
                if (costInput && costInput.value) {
                    costInput.value = convertToBackendFormat(costInput.value);
                }

                // Envia o formulário
                form.submit();
            });
        }
    });
</script>
@endsection 