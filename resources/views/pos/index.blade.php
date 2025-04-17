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

            <!-- Área de Gerenciamento do Caixa -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Gerenciamento do Caixa</h5>
                </div>
                <div class="card-body">
                    <div id="cashRegisterStatus">
                        <!-- Status do caixa será carregado via JavaScript -->
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-success" id="openRegister" style="display: none;">
                            <i class="fas fa-cash-register"></i> Abrir Caixa
                        </button>
                        <button type="button" class="btn btn-danger" id="closeRegister" style="display: none;">
                            <i class="fas fa-cash-register"></i> Fechar Caixa
                        </button>
                        <button type="button" class="btn btn-warning" id="withdrawalButton" style="display: none;">
                            <i class="fas fa-money-bill-wave"></i> Sangria
                        </button>
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
                    <form id="saleForm" action="{{ route('pos.store') }}" method="POST">
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

                        <!-- Desconto -->
                        <div class="mb-3">
                            <label for="discount_percentage" class="form-label">Desconto (%)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="discount_percentage" 
                                   name="discount_percentage" 
                                   min="0" 
                                   max="100" 
                                   step="0.01" 
                                   value="0">
                        </div>

                        <!-- Desconto em Valor -->
                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Desconto (R$)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="discount_amount" 
                                   name="discount_amount" 
                                   min="0" 
                                   step="0.01" 
                                   value="0">
                        </div>

                        <!-- Totais -->
                        <div class="table-responsive mb-3">
                            <table class="table">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end" id="subtotal">R$ 0,00</td>
                                </tr>
                                <tr>
                                    <td>Desconto:</td>
                                    <td class="text-end" id="discount">R$ 0,00</td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong id="finalTotal">R$ 0,00</strong></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Pagamentos -->
                        <div class="mb-3">
                            <label class="form-label">Formas de Pagamento</label>
                            <div id="payments">
                                <div class="input-group mb-2">
                                    <select class="form-select payment-method" required style="max-width: 200px;">
                                        <option value="">Selecione</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" 
                                           class="form-control payment-amount" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    <button type="button" class="btn btn-danger remove-payment">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" id="addPayment">
                                <i class="fas fa-plus"></i> Adicionar Forma de Pagamento
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Valor Recebido</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="amountReceived" 
                                       step="0.01" 
                                       min="0" 
                                       value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Troco</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="change" 
                                       readonly>
                            </div>
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
    const discountPercentageInput = document.getElementById('discount_percentage');
    const discountAmountInput = document.getElementById('discount_amount');

    // Adicionar item ao carrinho
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            
            if (type === 'product' && parseInt(this.dataset.stock) <= 0) {
                alert('Produto sem estoque!');
                return;
            }

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
                item.quantity = parseInt(this.value) || 1;
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
            removeBtn.className = 'btn btn-sm';
            removeBtn.innerHTML = '<i class="fas fa-trash text-danger"></i>';
            removeBtn.style.padding = '0.25rem 0.5rem';
            removeBtn.title = 'Remover item';
            removeBtn.addEventListener('click', function() {
                cart.splice(index, 1);
                updateCart();
            });
            removeCell.appendChild(removeBtn);
        });

        cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        updateTotals();
        
        // Atualizar o valor da primeira forma de pagamento com o total
        const firstPaymentAmount = document.querySelector('.payment-amount');
        if (firstPaymentAmount) {
            const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
            firstPaymentAmount.value = finalTotal.toFixed(2);
            updatePaymentTotals();
        }
    }

    // Função para atualizar os totais
    function updateTotals() {
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
        const discountAmountValue = parseFloat(discountAmountInput.value) || 0;
        
        const percentageDiscount = subtotal * (discountPercentage / 100);
        const totalDiscount = percentageDiscount + discountAmountValue;
        const finalTotal = subtotal - totalDiscount;

        document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('discount').textContent = `R$ ${totalDiscount.toFixed(2).replace('.', ',')}`;
        document.getElementById('finalTotal').textContent = `R$ ${finalTotal.toFixed(2).replace('.', ',')}`;
        
        // Se houver apenas uma forma de pagamento, atualizar seu valor
        const paymentInputs = document.querySelectorAll('.payment-amount');
        if (paymentInputs.length === 1) {
            paymentInputs[0].value = finalTotal.toFixed(2);
        }
        
        updatePaymentTotals();
    }

    // Eventos para atualizar totais quando os descontos mudarem
    discountPercentageInput.addEventListener('input', updateTotals);
    discountAmountInput.addEventListener('input', updateTotals);

    // Função para validar o valor recebido
    function validatePayment() {
        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        const totalPayments = Array.from(document.querySelectorAll('.payment-amount'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

        if (Math.abs(totalPayments - finalTotal) > 0.01) {
            alert('O valor total dos pagamentos deve ser igual ao valor da venda!');
            return false;
        }
        return true;
    }

    // Atualizar o formulário para incluir validação
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

        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        const payments = Array.from(document.querySelectorAll('.payment-amount')).map((amount, index) => ({
            payment_method_id: document.querySelectorAll('.payment-method')[index].value,
            amount: parseFloat(amount.value) || 0
        })).filter(payment => payment.amount > 0 && payment.payment_method_id);

        // Validar se o total dos pagamentos corresponde ao valor final
        const totalPayments = payments.reduce((sum, payment) => sum + payment.amount, 0);
        const roundedFinalTotal = Math.round(finalTotal * 100) / 100;
        const roundedTotalPayments = Math.round(totalPayments * 100) / 100;

        if (Math.abs(roundedTotalPayments - roundedFinalTotal) > 0.01) {
            alert('O total dos pagamentos deve ser igual ao valor final da venda!');
            return;
        }

        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Processando...';

        const products = cart.filter(item => item.type === 'product').map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        const services = cart.filter(item => item.type === 'service').map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        const data = {
            client_id: document.getElementById('client_id').value,
            payments: payments,
            products: products,
            services: services,
            notes: document.getElementById('notes').value,
            discount_percentage: parseFloat(document.getElementById('discount_percentage').value) || 0,
            discount_amount: parseFloat(document.getElementById('discount_amount').value) || 0
        };

        console.log('Dados sendo enviados:', data);

        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        // Tenta fazer parse do JSON
                        const json = JSON.parse(text);
                        throw new Error(json.message || 'Erro ao processar a venda.');
                    } catch (e) {
                        // Se não for JSON, provavelmente é um erro HTML
                        console.error('Resposta do servidor:', text);
                        throw new Error('Erro interno do servidor. Por favor, tente novamente.');
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                throw new Error(data.message || 'Erro ao processar a venda.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
            submitButton.disabled = false;
            submitButton.innerHTML = 'Finalizar Venda';
        });
    });

    // Função atualizada para adicionar forma de pagamento
    function addPaymentMethod(initialAmount = 0) {
        const paymentsDiv = document.getElementById('payments');
        const paymentGroup = document.createElement('div');
        paymentGroup.className = 'input-group mb-2';
        
        paymentGroup.innerHTML = `
            <select class="form-select payment-method" required style="max-width: 200px;">
                <option value="">Selecione</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                @endforeach
            </select>
            <input type="number" 
                   class="form-control payment-amount" 
                   step="0.01" 
                   min="0" 
                   value="${initialAmount.toFixed(2)}"
                   required>
            <button type="button" class="btn btn-danger remove-payment">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        // Adicionar evento para remover pagamento
        paymentGroup.querySelector('.remove-payment').addEventListener('click', function() {
            if (document.querySelectorAll('.payment-method').length > 1) {
                paymentGroup.remove();
                updatePaymentTotals();
            } else {
                alert('É necessário manter pelo menos uma forma de pagamento.');
            }
        });
        
        // Adicionar evento para atualizar totais quando o valor mudar
        paymentGroup.querySelector('.payment-amount').addEventListener('input', updatePaymentTotals);
        
        paymentsDiv.appendChild(paymentGroup);
        return paymentGroup;
    }

    // Função para atualizar totais dos pagamentos
    function updatePaymentTotals() {
        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        const totalPayments = Array.from(document.querySelectorAll('.payment-amount'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        
        // Arredondar os valores para 2 casas decimais
        const roundedFinalTotal = Math.round(finalTotal * 100) / 100;
        const roundedTotalPayments = Math.round(totalPayments * 100) / 100;
        
        // Atualizar valor recebido
        document.getElementById('amountReceived').value = roundedTotalPayments.toFixed(2);
        
        // Calcular e atualizar troco
        const change = roundedTotalPayments - roundedFinalTotal;
        document.getElementById('change').value = change >= 0 ? 
            change.toFixed(2) : 
            'Valor insuficiente';
    }

    // Limpar carrinho
    clearCartBtn.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            cart.length = 0;
            updateCart();
        }
    });

    // Adicionar evento para o botão de adicionar pagamento
    document.getElementById('addPayment').addEventListener('click', function() {
        addPaymentMethod(0);
    });

    // Inicialização dos pagamentos
    const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.')) || 0;
    if (document.querySelectorAll('.payment-method').length === 0) {
        addPaymentMethod(finalTotal);
    }
    updatePaymentTotals();

    // Funções do Caixa
    function loadCashRegisterStatus() {
        fetch('/api/cash-register/status')
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('cashRegisterStatus');
                const openBtn = document.getElementById('openRegister');
                const closeBtn = document.getElementById('closeRegister');
                const withdrawalBtn = document.getElementById('withdrawalButton');

                if (data.status === 'open') {
                    statusDiv.innerHTML = `
                        <div class="alert alert-success">
                            <strong>Caixa Aberto</strong><br>
                            Aberto em: ${new Date(data.opened_at).toLocaleString()}<br>
                            Saldo Atual: R$ ${data.current_balance.toFixed(2).replace('.', ',')}
                        </div>
                    `;
                    openBtn.style.display = 'none';
                    closeBtn.style.display = 'block';
                    withdrawalBtn.style.display = 'block';
                } else {
                    statusDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Caixa Fechado</strong><br>
                            ${data.last_closed_at ? `Último fechamento: ${new Date(data.last_closed_at).toLocaleString()}` : ''}
                        </div>
                    `;
                    openBtn.style.display = 'block';
                    closeBtn.style.display = 'none';
                    withdrawalBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar status do caixa:', error);
            });
    }

    // Abrir Caixa
    document.getElementById('openRegister').addEventListener('click', function() {
        const amount = prompt('Digite o valor inicial do caixa:');
        if (amount === null || amount === '') return;

        fetch('/api/cash-register/open', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ opening_balance: parseFloat(amount) })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Caixa aberto com sucesso!');
                loadCashRegisterStatus();
            } else {
                alert(data.message || 'Erro ao abrir o caixa');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao abrir o caixa');
        });
    });

    // Fechar Caixa
    document.getElementById('closeRegister').addEventListener('click', function() {
        if (!confirm('Tem certeza que deseja fechar o caixa?')) return;

        fetch('/api/cash-register/close', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Caixa fechado com sucesso!');
                loadCashRegisterStatus();
            } else {
                alert(data.message || 'Erro ao fechar o caixa');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao fechar o caixa');
        });
    });

    // Sangria
    document.getElementById('withdrawalButton').addEventListener('click', function() {
        const amount = prompt('Digite o valor da sangria:');
        if (amount === null || amount === '') return;

        const reason = prompt('Digite o motivo da sangria:');
        if (reason === null || reason === '') return;

        fetch('/api/cash-register/withdrawal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount: parseFloat(amount),
                description: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sangria realizada com sucesso!');
                loadCashRegisterStatus();
            } else {
                alert(data.message || 'Erro ao realizar sangria');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao realizar sangria');
        });
    });

    // Carregar status inicial do caixa
    loadCashRegisterStatus();

    // Atualizar totais quando um valor de pagamento for alterado
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('payment-amount')) {
            updatePaymentTotals();
        }
    });
});
</script>
@endpush
@endsection 