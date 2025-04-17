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
                                <input type="text" 
                                       class="form-control money @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', isset($product) ? number_format($product->price, 2, ',', '.') : '') }}" 
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
                                <input type="text" 
                                       class="form-control money @error('cost') is-invalid @enderror" 
                                       id="cost" 
                                       name="cost" 
                                       value="{{ old('cost', isset($product) ? number_format($product->cost, 2, ',', '.') : '') }}">
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
                                <div class="mt-2" id="image-container">
                                    <img src="{{ url('storage/products/' . basename($product->image)) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px;"
                                         id="product-image">
                                    <div class="mt-1">
                                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                                        <button type="button" 
                                                class="btn btn-danger btn-sm" 
                                                id="remove-image-btn">
                                            <i class="fas fa-trash"></i> Remover imagem
                                        </button>
                                    </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const moneyInputs = document.querySelectorAll('.money');
        const removeImageBtn = document.getElementById('remove-image-btn');
        const imageContainer = document.getElementById('image-container');
        const removeImageInput = document.getElementById('remove_image');

        // Configurar o botão de remover imagem
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                if (imageContainer && removeImageInput) {
                    imageContainer.style.display = 'none';
                    removeImageInput.value = '1';
                }
            });
        }

        // Função para formatar valor monetário
        function formatMoney(value) {
            // Remove tudo que não é número
            value = value.replace(/\D/g, '');
            
            // Se não houver valor, retorna vazio
            if (value === '') {
                return '';
            }
            
            // Converte para número com 2 casas decimais
            value = (parseFloat(value) / 100).toFixed(2);
            
            // Formata para o padrão brasileiro
            return value.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Função para converter valor do formato brasileiro para o formato do backend
        function convertToBackendFormat(value) {
            if (!value) return '0';
            // Remove os pontos de milhar e substitui a vírgula por ponto
            return value.replace(/\./g, '').replace(',', '.');
        }

        // Formata os valores monetários durante a digitação
        moneyInputs.forEach(input => {
            // Formatar valor inicial se existir
            if (input.value) {
                input.value = formatMoney(input.value.replace(/\D/g, ''));
            }

            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value === '') {
                    e.target.value = '';
                    return;
                }
                e.target.value = formatMoney(value);
            });
        });

        // Trata o envio do formulário
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                try {
                    // Converte os valores monetários para o formato do backend
                    moneyInputs.forEach(input => {
                        if (input.value) {
                            input.value = convertToBackendFormat(input.value);
                        }
                    });

                    // Envia o formulário
                    form.submit();
                } catch (error) {
                    console.error('Erro ao processar o formulário:', error);
                    alert('Erro ao processar o formulário. Por favor, verifique os valores informados.');
                }
            });
        }
    });
</script>
@endpush 