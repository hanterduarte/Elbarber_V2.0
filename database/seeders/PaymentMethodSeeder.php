<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Dinheiro',
                'description' => 'Pagamento em dinheiro',
                'is_active' => true,
            ],
            [
                'name' => 'Cartão de Crédito',
                'description' => 'Pagamento com cartão de crédito',
                'is_active' => true,
            ],
            [
                'name' => 'Cartão de Débito',
                'description' => 'Pagamento com cartão de débito',
                'is_active' => true,
            ],
            [
                'name' => 'PIX',
                'description' => 'Pagamento via PIX',
                'is_active' => true,
            ],
            [
                'name' => 'Transferência Bancária',
                'description' => 'Pagamento via transferência bancária',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::create($paymentMethod);
        }
    }
} 