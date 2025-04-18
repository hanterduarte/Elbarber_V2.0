<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Criar permissões
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
            Permission::create(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrador do sistema']);
        $managerRole = Role::create(['name' => 'manager', 'description' => 'Gerente da barbearia']);
        $barberRole = Role::create(['name' => 'barber', 'description' => 'Barbeiro']);
        $receptionistRole = Role::create(['name' => 'receptionist', 'description' => 'Recepcionista']);

        // Atribuir permissões aos roles
        $adminRole->permissions()->sync(Permission::pluck('id')->toArray());
        
        $managerPermissions = [
            'clients.index', 'clients.create', 'clients.edit', 'clients.destroy',
            'barbers.index', 'barbers.create', 'barbers.edit', 'barbers.destroy',
            'services.index', 'services.create', 'services.edit', 'services.destroy',
            'products.index', 'products.create', 'products.edit', 'products.destroy',
            'appointments.index', 'appointments.create', 'appointments.edit', 'appointments.destroy',
            'sales.index', 'sales.create', 'sales.edit', 'sales.destroy',
            'cash_register.index', 'cash_register.create', 'cash_register.edit', 'cash_register.destroy',
            'reports.index'
        ];
        $managerRole->permissions()->sync(Permission::whereIn('name', $managerPermissions)->pluck('id')->toArray());

        $barberPermissions = [
            'clients.index',
            'appointments.index', 'appointments.create', 'appointments.edit',
            'services.index'
        ];
        $barberRole->permissions()->sync(Permission::whereIn('name', $barberPermissions)->pluck('id')->toArray());

        $receptionistPermissions = [
            'clients.index', 'clients.create', 'clients.edit',
            'appointments.index', 'appointments.create', 'appointments.edit',
            'services.index',
            'sales.index', 'sales.create'
        ];
        $receptionistRole->permissions()->sync(Permission::whereIn('name', $receptionistPermissions)->pluck('id')->toArray());

        // Criar usuários
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@elbarber.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        $manager = User::create([
            'name' => 'Gerente',
            'email' => 'gerente@elbarber.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        $barber = User::create([
            'name' => 'Barbeiro',
            'email' => 'barbeiro@elbarber.com',
            'password' => Hash::make('password'),
        ]);
        $barber->assignRole('barber');

        $receptionist = User::create([
            'name' => 'Recepcionista',
            'email' => 'recepcao@elbarber.com',
            'password' => Hash::make('password'),
        ]);
        $receptionist->assignRole('receptionist');

        // Chamar outros seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BarbershopSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            BarberSeeder::class,
            CashRegisterMovementSeeder::class
        ]);
    }
} 