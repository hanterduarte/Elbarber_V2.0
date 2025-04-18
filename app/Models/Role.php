<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    protected $attributes = [
        'guard_name' => 'web'
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public static function defaultRoles()
    {
        return [
            'admin' => [
                'description' => 'Administrador do sistema',
                'permissions' => [
                    'view users', 'create users', 'edit users', 'delete users',
                    'view clients', 'create clients', 'edit clients', 'delete clients',
                    'view barbers', 'create barbers', 'edit barbers', 'delete barbers',
                    'view services', 'create services', 'edit services', 'delete services',
                    'view products', 'create products', 'edit products', 'delete products',
                    'view appointments', 'create appointments', 'edit appointments', 'delete appointments',
                    'view sales', 'create sales', 'edit sales', 'delete sales',
                    'view cash register', 'open cash register', 'close cash register',
                    'view reports',
                    'manage settings'
                ]
            ],
            'manager' => [
                'description' => 'Gerente da barbearia',
                'permissions' => [
                    'view users', 'create users', 'edit users',
                    'view clients', 'create clients', 'edit clients',
                    'view barbers', 'create barbers', 'edit barbers',
                    'view services', 'create services', 'edit services',
                    'view products', 'create products', 'edit products',
                    'view appointments', 'create appointments', 'edit appointments',
                    'view sales', 'create sales', 'edit sales',
                    'view cash register', 'open cash register', 'close cash register',
                    'view reports'
                ]
            ],
            'barber' => [
                'description' => 'Barbeiro',
                'permissions' => [
                    'view clients',
                    'view services',
                    'view appointments', 'create appointments', 'edit appointments',
                    'view sales', 'create sales'
                ]
            ],
            'cashier' => [
                'description' => 'Caixa',
                'permissions' => [
                    'view clients',
                    'view services',
                    'view products',
                    'view appointments',
                    'view sales', 'create sales',
                    'view cash register', 'open cash register', 'close cash register'
                ]
            ]
        ];
    }
} 