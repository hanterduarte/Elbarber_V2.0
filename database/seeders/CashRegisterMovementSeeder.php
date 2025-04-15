<?php

namespace Database\Seeders;

use App\Models\CashRegisterMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashRegisterMovementSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@elbarber.com')->first();

        $movements = [
            [
                'type' => 'deposit',
                'amount' => 1000.00,
                'description' => 'Abertura de caixa',
                'user_id' => $admin->id
            ],
            [
                'type' => 'withdrawal',
                'amount' => 50.00,
                'description' => 'Compra de material',
                'user_id' => $admin->id
            ],
            [
                'type' => 'deposit',
                'amount' => 200.00,
                'description' => 'Venda de produtos',
                'user_id' => $admin->id
            ]
        ];

        foreach ($movements as $movement) {
            CashRegisterMovement::create($movement);
        }
    }
} 