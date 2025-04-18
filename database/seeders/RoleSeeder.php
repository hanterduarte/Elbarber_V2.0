<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Criar permissões padrão
        $permissions = [
            'users.index', 'users.create', 'users.edit', 'users.destroy',
            'roles.index', 'roles.create', 'roles.edit', 'roles.destroy',
            'permissions.index', 'permissions.create', 'permissions.edit', 'permissions.destroy',
            'clients.index', 'clients.create', 'clients.edit', 'clients.destroy',
            'barbers.index', 'barbers.create', 'barbers.edit', 'barbers.destroy',
            'services.index', 'services.create', 'services.edit', 'services.destroy',
            'products.index', 'products.create', 'products.edit', 'products.destroy',
            'appointments.index', 'appointments.create', 'appointments.edit', 'appointments.destroy',
            'sales.index', 'sales.create', 'sales.edit', 'sales.destroy',
            'cash_register.index', 'cash_register.create', 'cash_register.edit', 'cash_register.destroy',
            'reports.index'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $roles = [
            'admin' => [
                'description' => 'Administrador do sistema',
                'permissions' => Permission::pluck('id')->toArray()
            ],
            'manager' => [
                'description' => 'Gerente da barbearia',
                'permissions' => [
                    'clients.index', 'clients.create', 'clients.edit', 'clients.destroy',
                    'barbers.index', 'barbers.create', 'barbers.edit', 'barbers.destroy',
                    'services.index', 'services.create', 'services.edit', 'services.destroy',
                    'products.index', 'products.create', 'products.edit', 'products.destroy',
                    'appointments.index', 'appointments.create', 'appointments.edit', 'appointments.destroy',
                    'sales.index', 'sales.create', 'sales.edit', 'sales.destroy',
                    'cash_register.index', 'cash_register.create', 'cash_register.edit', 'cash_register.destroy',
                    'reports.index'
                ]
            ],
            'barber' => [
                'description' => 'Barbeiro',
                'permissions' => [
                    'clients.index',
                    'appointments.index', 'appointments.create', 'appointments.edit',
                    'services.index'
                ]
            ],
            'receptionist' => [
                'description' => 'Recepcionista',
                'permissions' => [
                    'clients.index', 'clients.create', 'clients.edit',
                    'appointments.index', 'appointments.create', 'appointments.edit',
                    'services.index',
                    'sales.index', 'sales.create'
                ]
            ]
        ];

        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'description' => $roleData['description']
            ]);

            if (is_array($roleData['permissions'])) {
                $permissionIds = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            }
        }
    }
} 