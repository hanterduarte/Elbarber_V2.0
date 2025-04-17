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
                                <!-- Pagamentos serão adicionados aqui -->
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="addPayment">
                                <i class="fas fa-plus"></i> Adicionar Forma de Pagamento
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Valor Recebido</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amountReceived" step="0.01" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Troco</label>
                            <input type="text" class="form-control" id="change" readonly>
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
    }

    // Função para atualizar os totais
    function updateTotals() {
        const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
        const discountAmountValue = parseFloat(discountAmountInput.value) || 0;
        
        // Calcular desconto em valor baseado na porcentagem
        const percentageDiscount = subtotal * (discountPercentage / 100);
        
        // Desconto total (porcentagem + valor fixo)
        const totalDiscount = percentageDiscount + discountAmountValue;
        
        // Valor final
        const finalTotal = subtotal - totalDiscount;

        document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('discount').textContent = `R$ ${totalDiscount.toFixed(2).replace('.', ',')}`;
        document.getElementById('finalTotal').textContent = `R$ ${finalTotal.toFixed(2).replace('.', ',')}`;
        
        updateChange();
    }

    // Eventos para atualizar totais quando os descontos mudarem
    discountPercentageInput.addEventListener('input', updateTotals);
    discountAmountInput.addEventListener('input', updateTotals);

    // Função para validar o valor recebido
    function validatePayment() {
        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        const totalPayments = Array.from(document.querySelectorAll('.payment-amount'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

        if (totalPayments < finalTotal) {
            alert('O valor total dos pagamentos é menor que o valor da venda!');
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

        if (!validatePayment()) {
            return;
        }

        // Desabilitar o botão de submit para evitar múltiplos envios
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Processando...';

        // Preparar dados para envio
        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        
        // Coletar pagamentos
        const payments = Array.from(document.querySelectorAll('.payment-row')).map(row => ({
            payment_method_id: row.querySelector('.payment-method').value,
            amount: parseFloat(row.querySelector('.payment-amount').value) || 0
        })).filter(payment => payment.amount > 0);

        // Preparar produtos
        const products = cart.filter(item => item.type === 'product').map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        // Criar objeto de dados
        const data = {
            client_id: document.getElementById('client_id').value,
            payments: payments,
            products: products,
            notes: document.getElementById('notes').value,
            discount_percentage: parseFloat(discountPercentageInput.value) || 0,
            discount_amount: parseFloat(discountAmountInput.value) || 0
        };

        // Enviar dados via fetch
        fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Erro ao processar a venda.');
                submitButton.disabled = false;
                submitButton.innerHTML = 'Finalizar Venda';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao processar a venda. Por favor, tente novamente.');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Finalizar Venda';
        });
    });

    // Função para adicionar forma de pagamento
    document.getElementById('addPayment').addEventListener('click', function() {
        const paymentsDiv = document.getElementById('payments');
        const paymentRow = document.createElement('div');
        paymentRow.className = 'row mb-2 payment-row';
        
        paymentRow.innerHTML = `
            <div class="col-5">
                <select class="form-select payment-method" required>
                    <option value="">Selecione...</option>
                    @foreach($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-5">
                <input type="number" class="form-control payment-amount" step="0.01" min="0" placeholder="Valor" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger btn-sm remove-payment">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Adicionar evento para remover pagamento
        paymentRow.querySelector('.remove-payment').addEventListener('click', function() {
            paymentRow.remove();
            updateTotals();
        });
        
        // Adicionar evento para atualizar totais quando o valor mudar
        paymentRow.querySelector('.payment-amount').addEventListener('input', updateTotals);
        
        paymentsDiv.appendChild(paymentRow);
    });

    // Função para atualizar o troco
    function updateChange() {
        const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace('R$ ', '').replace(',', '.'));
        const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
        const change = amountReceived - finalTotal;
        
        document.getElementById('change').value = change >= 0 ? 
            `R$ ${change.toFixed(2).replace('.', ',')}` : 
            'Valor insuficiente';
    }

    // Evento para atualizar o troco quando o valor recebido mudar
    document.getElementById('amountReceived').addEventListener('input', updateChange);

    // Limpar carrinho
    clearCartBtn.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja limpar o carrinho?')) {
            cart.length = 0;
            updateCart();
        }
    });

    // Adicionar primeira forma de pagamento
    document.getElementById('addPayment').click();

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
});
</script>
@endpush
@endsection 