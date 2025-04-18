<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@elbarber.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->sync([$adminRole->id]);

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@elbarber.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        // Assign manager role
        $managerRole = Role::where('name', 'manager')->first();
        $manager->roles()->sync([$managerRole->id]);

        // Create barber user
        $barber = User::firstOrCreate(
            ['email' => 'barber@elbarber.com'],
            [
                'name' => 'Barber',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        // Assign barber role
        $barberRole = Role::where('name', 'barber')->first();
        $barber->roles()->sync([$barberRole->id]);

        // Create receptionist user
        $receptionist = User::firstOrCreate(
            ['email' => 'receptionist@elbarber.com'],
            [
                'name' => 'Receptionist',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        // Assign receptionist role
        $receptionistRole = Role::where('name', 'receptionist')->first();
        $receptionist->roles()->sync([$receptionistRole->id]);
    }
} 