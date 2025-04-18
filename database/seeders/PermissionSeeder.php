<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Users Module
            [
                'name' => 'manage_users',
                'description' => 'Manage users',
                'module' => 'users',
                'active' => true,
            ],
            [
                'name' => 'view_users',
                'description' => 'View users',
                'module' => 'users',
                'active' => true,
            ],

            // Roles Module
            [
                'name' => 'manage_roles',
                'description' => 'Manage roles',
                'module' => 'roles',
                'active' => true,
            ],
            [
                'name' => 'view_roles',
                'description' => 'View roles',
                'module' => 'roles',
                'active' => true,
            ],

            // Permissions Module
            [
                'name' => 'manage_permissions',
                'description' => 'Manage permissions',
                'module' => 'permissions',
                'active' => true,
            ],
            [
                'name' => 'view_permissions',
                'description' => 'View permissions',
                'module' => 'permissions',
                'active' => true,
            ],

            // Clients Module
            [
                'name' => 'manage_clients',
                'description' => 'Manage clients',
                'module' => 'clients',
                'active' => true,
            ],
            [
                'name' => 'view_clients',
                'description' => 'View clients',
                'module' => 'clients',
                'active' => true,
            ],

            // Services Module
            [
                'name' => 'manage_services',
                'description' => 'Manage services',
                'module' => 'services',
                'active' => true,
            ],
            [
                'name' => 'view_services',
                'description' => 'View services',
                'module' => 'services',
                'active' => true,
            ],

            // Products Module
            [
                'name' => 'manage_products',
                'description' => 'Manage products',
                'module' => 'products',
                'active' => true,
            ],
            [
                'name' => 'view_products',
                'description' => 'View products',
                'module' => 'products',
                'active' => true,
            ],

            // Sales Module
            [
                'name' => 'manage_sales',
                'description' => 'Manage sales',
                'module' => 'sales',
                'active' => true,
            ],
            [
                'name' => 'view_sales',
                'description' => 'View sales',
                'module' => 'sales',
                'active' => true,
            ],

            // Cash Register Module
            [
                'name' => 'manage_cash_register',
                'description' => 'Manage cash register',
                'module' => 'cash_register',
                'active' => true,
            ],
            [
                'name' => 'view_cash_register',
                'description' => 'View cash register',
                'module' => 'cash_register',
                'active' => true,
            ],

            // Reports Module
            [
                'name' => 'view_reports',
                'description' => 'View reports',
                'module' => 'reports',
                'active' => true,
            ],

            // Appointments Module
            [
                'name' => 'manage_appointments',
                'description' => 'Manage appointments',
                'module' => 'appointments',
                'active' => true,
            ],
            [
                'name' => 'view_appointments',
                'description' => 'View appointments',
                'module' => 'appointments',
                'active' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
} 