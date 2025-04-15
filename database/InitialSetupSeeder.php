<?php

namespace Database\Seeders;

use App\Models\Barbershop;
use App\Models\PaymentMethod;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run()
    {
        // Create profiles
        $adminProfile = Profile::create([
            'name' => 'Administrador',
            'description' => 'Acesso total ao sistema',
            'status' => true
        ]);

        Profile::create([
            'name' => 'Barbeiro',
            'description' => 'Acesso às funcionalidades do barbeiro',
            'status' => true
        ]);

        Profile::create([
            'name' => 'Atendente',
            'description' => 'Acesso às funcionalidades de atendimento',
            'status' => true
        ]);

        // Create default barbershop
        $barbershop = Barbershop::create([
            'name' => 'Barbearia Principal',
            'address' => 'Endereço da Barbearia Principal'
        ]);

        // Create superuser
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@elbarber.com',
            'password' => Hash::make('admin123'),
            'profile_id' => $adminProfile->id,
            'status' => true
        ]);

        // Create payment methods
        PaymentMethod::create([
            'name' => 'Dinheiro',
            'description' => 'Pagamento em dinheiro',
            'abbreviation' => 'DIN'
        ]);

        PaymentMethod::create([
            'name' => 'Cartão de Crédito',
            'description' => 'Pagamento com cartão de crédito',
            'abbreviation' => 'CC'
        ]);

        PaymentMethod::create([
            'name' => 'Cartão de Débito',
            'description' => 'Pagamento com cartão de débito',
            'abbreviation' => 'CD'
        ]);

        PaymentMethod::create([
            'name' => 'PIX',
            'description' => 'Pagamento via PIX',
            'abbreviation' => 'PIX'
        ]);
    }
} 