<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Criar papéis
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description' => 'Administrador do sistema',
                'is_active' => true
            ]
        );

        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'description' => 'Gerente da barbearia',
                'is_active' => true
            ]
        );

        $barber = Role::firstOrCreate(
            ['name' => 'barber'],
            [
                'description' => 'Barbeiro',
                'is_active' => true
            ]
        );

        $receptionist = Role::firstOrCreate(
            ['name' => 'receptionist'],
            [
                'description' => 'Recepcionista',
                'is_active' => true
            ]
        );

        // Obter todas as permissões
        $permissions = Permission::all();

        // Atribuir todas as permissões ao admin
        $admin->permissions()->sync($permissions->pluck('id'));

        // Atribuir permissões ao gerente
        $managerPermissions = $permissions->filter(function ($permission) {
            return !in_array($permission->module, ['Users', 'Roles', 'Permissions']);
        });
        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // Atribuir permissões ao barbeiro
        $barberPermissions = $permissions->filter(function ($permission) {
            return in_array($permission->module, ['Appointments', 'Clients', 'Services', 'Sales']);
        });
        $barber->permissions()->sync($barberPermissions->pluck('id'));

        // Atribuir permissões ao recepcionista
        $receptionistPermissions = $permissions->filter(function ($permission) {
            return in_array($permission->module, ['Appointments', 'Clients', 'Sales', 'CashRegister']);
        });
        $receptionist->permissions()->sync($receptionistPermissions->pluck('id'));
    }
} 