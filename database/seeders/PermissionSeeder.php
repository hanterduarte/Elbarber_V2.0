<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Usuários
            [
                'name' => 'users.index',
                'description' => 'Visualizar lista de usuários',
                'module' => 'Users',
                'is_active' => true
            ],
            [
                'name' => 'users.create',
                'description' => 'Criar usuários',
                'module' => 'Users',
                'is_active' => true
            ],
            [
                'name' => 'users.edit',
                'description' => 'Editar usuários',
                'module' => 'Users',
                'is_active' => true
            ],
            [
                'name' => 'users.delete',
                'description' => 'Excluir usuários',
                'module' => 'Users',
                'is_active' => true
            ],

            // Papéis
            [
                'name' => 'roles.index',
                'description' => 'Visualizar lista de papéis',
                'module' => 'Roles',
                'is_active' => true
            ],
            [
                'name' => 'roles.create',
                'description' => 'Criar papéis',
                'module' => 'Roles',
                'is_active' => true
            ],
            [
                'name' => 'roles.edit',
                'description' => 'Editar papéis',
                'module' => 'Roles',
                'is_active' => true
            ],
            [
                'name' => 'roles.delete',
                'description' => 'Excluir papéis',
                'module' => 'Roles',
                'is_active' => true
            ],

            // Permissões
            [
                'name' => 'permissions.index',
                'description' => 'Visualizar lista de permissões',
                'module' => 'Permissions',
                'is_active' => true
            ],
            [
                'name' => 'permissions.create',
                'description' => 'Criar permissões',
                'module' => 'Permissions',
                'is_active' => true
            ],
            [
                'name' => 'permissions.edit',
                'description' => 'Editar permissões',
                'module' => 'Permissions',
                'is_active' => true
            ],
            [
                'name' => 'permissions.delete',
                'description' => 'Excluir permissões',
                'module' => 'Permissions',
                'is_active' => true
            ],

            // Clientes
            [
                'name' => 'clients.index',
                'description' => 'Visualizar lista de clientes',
                'module' => 'Clients',
                'is_active' => true
            ],
            [
                'name' => 'clients.create',
                'description' => 'Criar clientes',
                'module' => 'Clients',
                'is_active' => true
            ],
            [
                'name' => 'clients.edit',
                'description' => 'Editar clientes',
                'module' => 'Clients',
                'is_active' => true
            ],
            [
                'name' => 'clients.delete',
                'description' => 'Excluir clientes',
                'module' => 'Clients',
                'is_active' => true
            ],

            // Serviços
            [
                'name' => 'services.index',
                'description' => 'Visualizar lista de serviços',
                'module' => 'Services',
                'is_active' => true
            ],
            [
                'name' => 'services.create',
                'description' => 'Criar serviços',
                'module' => 'Services',
                'is_active' => true
            ],
            [
                'name' => 'services.edit',
                'description' => 'Editar serviços',
                'module' => 'Services',
                'is_active' => true
            ],
            [
                'name' => 'services.delete',
                'description' => 'Excluir serviços',
                'module' => 'Services',
                'is_active' => true
            ],

            // Produtos
            [
                'name' => 'products.index',
                'description' => 'Visualizar lista de produtos',
                'module' => 'Products',
                'is_active' => true
            ],
            [
                'name' => 'products.create',
                'description' => 'Criar produtos',
                'module' => 'Products',
                'is_active' => true
            ],
            [
                'name' => 'products.edit',
                'description' => 'Editar produtos',
                'module' => 'Products',
                'is_active' => true
            ],
            [
                'name' => 'products.delete',
                'description' => 'Excluir produtos',
                'module' => 'Products',
                'is_active' => true
            ],

            // Vendas
            [
                'name' => 'sales.index',
                'description' => 'Visualizar lista de vendas',
                'module' => 'Sales',
                'is_active' => true
            ],
            [
                'name' => 'sales.create',
                'description' => 'Criar vendas',
                'module' => 'Sales',
                'is_active' => true
            ],
            [
                'name' => 'sales.edit',
                'description' => 'Editar vendas',
                'module' => 'Sales',
                'is_active' => true
            ],
            [
                'name' => 'sales.delete',
                'description' => 'Excluir vendas',
                'module' => 'Sales',
                'is_active' => true
            ],

            // Caixa
            [
                'name' => 'cash-register.index',
                'description' => 'Visualizar caixa',
                'module' => 'Cash Register',
                'is_active' => true
            ],
            [
                'name' => 'cash-register.create',
                'description' => 'Abrir caixa',
                'module' => 'Cash Register',
                'is_active' => true
            ],
            [
                'name' => 'cash-register.edit',
                'description' => 'Fechar caixa',
                'module' => 'Cash Register',
                'is_active' => true
            ],

            // Relatórios
            [
                'name' => 'reports.index',
                'description' => 'Visualizar relatórios',
                'module' => 'Reports',
                'is_active' => true
            ],

            // Agendamentos
            [
                'name' => 'appointments.index',
                'description' => 'Visualizar lista de agendamentos',
                'module' => 'Appointments',
                'is_active' => true
            ],
            [
                'name' => 'appointments.create',
                'description' => 'Criar agendamentos',
                'module' => 'Appointments',
                'is_active' => true
            ],
            [
                'name' => 'appointments.edit',
                'description' => 'Editar agendamentos',
                'module' => 'Appointments',
                'is_active' => true
            ],
            [
                'name' => 'appointments.delete',
                'description' => 'Excluir agendamentos',
                'module' => 'Appointments',
                'is_active' => true
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
} 