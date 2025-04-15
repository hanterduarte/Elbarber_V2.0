<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            // Usuários
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Funções
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            // Permissões
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            // Clientes
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            // Barbeiros
            'view barbers',
            'create barbers',
            'edit barbers',
            'delete barbers',
            // Serviços
            'view services',
            'create services',
            'edit services',
            'delete services',
            // Produtos
            'view products',
            'create products',
            'edit products',
            'delete products',
            // Agendamentos
            'view appointments',
            'create appointments',
            'edit appointments',
            'delete appointments',
            // Vendas
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            // Caixa
            'view cash register',
            'open cash register',
            'close cash register',
            'view cash register movements',
            'create cash register movements',
            'edit cash register movements',
            'delete cash register movements',
            // Relatórios
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar roles e atribuir permissões
        $roles = [
            'admin' => Permission::all(),
            'manager' => [
                'view users',
                'create users',
                'edit users',
                'view roles',
                'view permissions',
                'view clients',
                'create clients',
                'edit clients',
                'delete clients',
                'view barbers',
                'create barbers',
                'edit barbers',
                'delete barbers',
                'view services',
                'create services',
                'edit services',
                'delete services',
                'view products',
                'create products',
                'edit products',
                'delete products',
                'view appointments',
                'create appointments',
                'edit appointments',
                'delete appointments',
                'view sales',
                'create sales',
                'edit sales',
                'delete sales',
                'view cash register',
                'open cash register',
                'close cash register',
                'view cash register movements',
                'create cash register movements',
                'edit cash register movements',
                'delete cash register movements',
                'view reports',
            ],
            'barber' => [
                'view clients',
                'create clients',
                'edit clients',
                'view services',
                'view products',
                'view appointments',
                'create appointments',
                'edit appointments',
                'view sales',
                'create sales',
                'view cash register',
                'view cash register movements',
            ],
            'receptionist' => [
                'view clients',
                'create clients',
                'edit clients',
                'view services',
                'view products',
                'view appointments',
                'create appointments',
                'edit appointments',
                'view sales',
                'create sales',
                'view cash register',
                'view cash register movements',
            ],
        ];

        foreach ($roles as $role => $permissions) {
            $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
} 