<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Get all permissions
        $permissions = Permission::all();

        // Create Admin role with all permissions
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description' => 'System Administrator',
                'active' => true,
            ]
        );
        $admin->permissions()->sync($permissions->pluck('id'));

        // Create Manager role with specific permissions
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'description' => 'Business Manager',
                'active' => true,
            ]
        );
        $managerPermissions = $permissions->whereIn('module', [
            'clients',
            'services',
            'products',
            'sales',
            'cash_register',
            'reports',
            'appointments'
        ]);
        $manager->permissions()->sync($managerPermissions->pluck('id'));

        // Create Barber role with specific permissions
        $barber = Role::firstOrCreate(
            ['name' => 'barber'],
            [
                'description' => 'Barber',
                'active' => true,
            ]
        );
        $barberPermissions = $permissions->whereIn('module', [
            'clients',
            'services',
            'appointments'
        ]);
        $barber->permissions()->sync($barberPermissions->pluck('id'));

        // Create Receptionist role with specific permissions
        $receptionist = Role::firstOrCreate(
            ['name' => 'receptionist'],
            [
                'description' => 'Receptionist',
                'active' => true,
            ]
        );
        $receptionistPermissions = $permissions->whereIn('module', [
            'clients',
            'appointments',
            'sales',
            'cash_register'
        ]);
        $receptionist->permissions()->sync($receptionistPermissions->pluck('id'));
    }
} 