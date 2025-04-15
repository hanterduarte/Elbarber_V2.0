@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Painel de Produtos e Serviços -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#products">Produtos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#services">Serviços</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Aba de Produtos -->
                        <div class="tab-pane fade show active" id="products">
                            <div class="row">
                                @foreach($products as $product)
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                                <p class="card-text">
                                                    <small class="text-muted">Estoque: {{ $product->stock }}</small>
                                                </p>
                                                <button type="button" 
                                                        class="btn btn-primary w-100 add-to-cart"
                                                        data-type="product"
                                                        data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-price="{{ $product->price }}"
                                                        data-stock="{{ $product->stock }}">
                                                    Adicionar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Aba de Serviços -->
                        <div class="tab-pane fade" id="services">
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $service->name }}</h5>
                                                <p class="card-text">R$ {{ number_format($service->price, 2, ',', '.') }}</p>
                                                <p class="card-text">
                                                    <small class="text-muted">Duração: {{ $service->duration }} min</small>
                                                </p>
                                                <button type="button" 
                                                        class="btn btn-primary w-100 add-to-cart"
                                                        data-type="service"
                                                        data-id="{{ $service->id }}"
                                                        data-name="{{ $service->name }}"
                                                        data-price="{{ $service->price }}"
                                                        data-duration="{{ $service->duration }}">
                                                    Adicionar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel do Carrinho e Finalização -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Carrinho</h5>
                </div>
                <div class="card-body">
                    <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        
                        <!-- Cliente -->
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Itens do Carrinho -->
                        <div class="table-responsive mb-3">
                            <table class="table" id="cartTable">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qtd</th>
                                        <th>Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Itens serão adicionados dinamicamente -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th id="cartTotal">R$ 0,00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Selecione a forma de pagamento</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>

                        <!-- Botões -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger" id="clearCart">
                                Limpar Carrinho
                            </button>
                            <button type="submit" class="btn btn-success" id="completeSale">
                                Finalizar Venda
                            </button>
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
    const cart = [];
    const cartTable = document.getElementById('cartTable').getElementsByTagName('tbody')[0];
    const cartTotal = document.getElementById('cartTotal');
    const clearCartBtn = document.getElementById('clearCart');
    const completeSaleBtn = document.getElementById('completeSale');
    const saleForm = document.getElementById('saleForm');

    // Adicionar item ao carrinho
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            
            // Verificar se é produto e tem estoque
            if (type === 'product' && parseInt(this.dataset.stock) <= 0) {
                alert('Produto sem estoque!');
                return;
            }

            // Adicionar ao carrinho
            cart.push({
                type,
                id,
                name,
                price,
                quantity: 1
            });

            updateCart();
        });
    });

    // Atualizar carrinho
    function updateCart() {
        cartTable.innerHTML = '';
        let total = 0;

        cart.forEach((item, index) => {
            const row = cartTable.insertRow();
            
            // Nome
            const nameCell = row.insertCell(0);
            nameCell.textContent = item.name;

            // Quantidade
            const quantityCell = row.insertCell(1);
            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.min = '1';
            quantityInput.value = item.quantity;
            quantityInput.className = 'form-control form-control-sm';
            quantityInput.style.width = '60px';
            quantityInput.addEventListener('change', function() {
                item.quantity = parseInt(this.value);
                updateCart();
            });
            quantityCell.appendChild(quantityInput);

            // Valor
            const valueCell = row.insertCell(2);
            const itemTotal = item.price * item.quantity;
            valueCell.textContent = `R$ ${itemTotal.toFixed(2).replace('.', ',')}`;
            total += itemTotal;

            // Remover
            const removeCell = row.insertCell(3);
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger btn-sm';
            removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
            removeBtn.addEventListener('click', function() {
                cart.splice(index, 1);
                updateCart();
            });
            removeCell.appendChild(removeBtn);
        });

        // Atualizar total
        cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

        // Habilitar/desabilitar botão de finalizar
        completeSaleBtn.disabled = cart.length === 0;
    }

    // Limpar carrinho
    clearCartBtn.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            cart.length = 0;
            updateCart();
        }
    });

    // Finalizar venda
    saleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (cart.length === 0) {
            alert('Adicione itens ao carrinho!');
            return;
        }

        if (!document.getElementById('client_id').value) {
            alert('Selecione um cliente!');
            return;
        }

        if (!document.getElementById('payment_method').value) {
            alert('Selecione uma forma de pagamento!');
            return;
        }

        // Adicionar itens ao formulário
        cart.forEach((item, index) => {
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = `items[${index}][type]`;
            typeInput.value = item.type;
            saleForm.appendChild(typeInput);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = `items[${index}][id]`;
            idInput.value = item.id;
            saleForm.appendChild(idInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = item.quantity;
            saleForm.appendChild(quantityInput);
        });

        // Enviar formulário
        this.submit();
    });
});
</script>
@endpush
@endsection 