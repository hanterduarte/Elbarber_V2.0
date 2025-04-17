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
                    <!-- Alert para mensagens -->
                    <div id="alert" class="alert d-none">
                        <span id="alert-message"></span>
                    </div>

                    @if (isset($sale))
                        <form id="saleForm" action="{{ route('sales.update', $sale) }}" method="POST">
                        @method('PUT')
                    @else
                        <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
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
                            <label class="form-label">Pagamento</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select @error('payments.0.payment_method_id') is-invalid @enderror" 
                                            id="payment_method_id" 
                                            name="payments[0][payment_method_id]" 
                                            required>
                                        <option value="">Selecione um método de pagamento</option>
                                        @foreach($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}" 
                                                    {{ old('payments.0.payment_method_id', $sale->payment_method_id ?? '') == $paymentMethod->id ? 'selected' : '' }}>
                                                {{ $paymentMethod->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payments.0.payment_method_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="number" 
                                           class="form-control @error('payments.0.amount') is-invalid @enderror" 
                                           name="payments[0][amount]" 
                                           placeholder="Valor"
                                           step="0.01"
                                           required
                                           value="{{ old('payments.0.amount') }}">
                                    @error('payments.0.amount')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Produtos</label>
                            <div class="row">
                                @foreach($products as $index => $product)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" 
                                                   type="checkbox" 
                                                   value="{{ $product->id }}" 
                                                   id="product_{{ $product->id }}"
                                                   data-price="{{ $product->price }}"
                                                   data-index="{{ $index }}"
                                                   {{ isset($sale) && $sale->products->contains($product->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="product_{{ $product->id }}">
                                                {{ $product->name }} - R$ {{ number_format($product->price, 2, ',', '.') }}
                                            </label>
                                            <input type="hidden" 
                                                   name="products[{{ $index }}][id]" 
                                                   value="{{ $product->id }}"
                                                   disabled>
                                            <input type="number" 
                                                   class="form-control form-control-sm product-quantity" 
                                                   name="products[{{ $index }}][quantity]" 
                                                   value="{{ old('products.' . $index . '.quantity', isset($sale) ? $sale->products->find($product->id)->pivot->quantity : 1) }}" 
                                                   min="1" 
                                                   style="width: 80px; display: inline-block;"
                                                   disabled>
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
        const form = document.getElementById('saleForm');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const quantities = document.querySelectorAll('.product-quantity');
        const totalInput = document.getElementById('total');
        const amountInput = document.querySelector('input[name="payments[0][amount]"]');
        const alert = document.getElementById('alert');
        const alertMessage = document.getElementById('alert-message');

        function calculateTotal() {
            let total = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const index = checkbox.dataset.index;
                    const quantity = document.querySelector(`input[name="products[${index}][quantity]"]`).value;
                    total += parseFloat(checkbox.dataset.price) * parseInt(quantity);
                }
            });
            totalInput.value = `R$ ${total.toFixed(2).replace('.', ',')}`;
            amountInput.value = total.toFixed(2);
        }

        function showAlert(message, type = 'danger') {
            alert.className = `alert alert-${type}`;
            alertMessage.textContent = message;
            alert.classList.remove('d-none');
            
            // Scroll to alert
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const index = this.dataset.index;
                const quantityInput = document.querySelector(`input[name="products[${index}][quantity]"]`);
                const idInput = document.querySelector(`input[name="products[${index}][id]"]`);
                
                quantityInput.disabled = !this.checked;
                idInput.disabled = !this.checked;
                
                if (!this.checked) {
                    quantityInput.value = 1;
                }
                calculateTotal();
            });
        });

        quantities.forEach(quantity => {
            quantity.addEventListener('change', calculateTotal);
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset alert
            alert.classList.add('d-none');

            // Disable submit button
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            // Get selected products
            const selectedProducts = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const index = checkbox.dataset.index;
                    selectedProducts.push({
                        id: checkbox.value,
                        quantity: document.querySelector(`input[name="products[${index}][quantity]"]`).value
                    });
                }
            });

            // Validate if at least one product is selected
            if (selectedProducts.length === 0) {
                showAlert('Selecione pelo menos um produto.');
                submitButton.disabled = false;
                return;
            }

            fetch(form.action, {
                method: form.method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    client_id: document.getElementById('client_id').value,
                    payments: [{
                        payment_method_id: document.getElementById('payment_method_id').value,
                        amount: amountInput.value
                    }],
                    products: selectedProducts,
                    notes: document.getElementById('notes').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    showAlert(data.message);
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                showAlert('Erro ao processar a venda. Por favor, tente novamente.');
                submitButton.disabled = false;
                console.error('Error:', error);
            });
        });

        // Calcular total inicial
        calculateTotal();
    });
</script>
@endpush
@endsection 