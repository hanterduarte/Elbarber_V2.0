@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ isset($sale) ? 'Editar Venda' : 'Nova Venda' }}
                </div>

                <div class="card-body">
                    @if (isset($sale))
                        <form action="{{ route('sales.update', $sale) }}" method="POST">
                        @method('PUT')
                    @else
                        <form action="{{ route('sales.store') }}" method="POST">
                    @endif
                        @csrf

                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" 
                                    name="client_id" 
                                    required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ old('client_id', $sale->client_id ?? '') == $client->id ? 'selected' : '' }}>
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
                            <label for="payment_method_id" class="form-label">Método de Pagamento</label>
                            <select class="form-select @error('payment_method_id') is-invalid @enderror" 
                                    id="payment_method_id" 
                                    name="payment_method_id" 
                                    required>
                                <option value="">Selecione um método de pagamento</option>
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{ $paymentMethod->id }}" 
                                            {{ old('payment_method_id', $sale->payment_method_id ?? '') == $paymentMethod->id ? 'selected' : '' }}>
                                        {{ $paymentMethod->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Produtos</label>
                            <div class="row">
                                @foreach($products as $product)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" 
                                                   type="checkbox" 
                                                   name="products[]" 
                                                   value="{{ $product->id }}" 
                                                   id="product_{{ $product->id }}"
                                                   data-price="{{ $product->price }}"
                                                   {{ isset($sale) && $sale->products->contains($product->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="product_{{ $product->id }}">
                                                {{ $product->name }} - R$ {{ number_format($product->price, 2, ',', '.') }}
                                            </label>
                                            <input type="number" 
                                                   class="form-control form-control-sm product-quantity" 
                                                   name="quantities[{{ $product->id }}]" 
                                                   value="{{ old('quantities.' . $product->id, isset($sale) ? $sale->products->find($product->id)->pivot->quantity : 1) }}" 
                                                   min="1" 
                                                   style="width: 80px; display: inline-block;"
                                                   {{ isset($sale) && $sale->products->contains($product->id) ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('products')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="total" 
                                   value="R$ 0,00" 
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3">{{ old('notes', $sale->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
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
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const quantities = document.querySelectorAll('.product-quantity');
        const totalInput = document.getElementById('total');

        function calculateTotal() {
            let total = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const quantity = document.querySelector(`input[name="quantities[${checkbox.value}]"]`).value;
                    total += parseFloat(checkbox.dataset.price) * parseInt(quantity);
                }
            });
            totalInput.value = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantityInput = document.querySelector(`input[name="quantities[${this.value}]"]`);
                quantityInput.disabled = !this.checked;
                if (!this.checked) {
                    quantityInput.value = 1;
                }
                calculateTotal();
            });
        });

        quantities.forEach(quantity => {
            quantity.addEventListener('change', calculateTotal);
        });

        // Calcular total inicial
        calculateTotal();
    });
</script>
@endpush
@endsection 