<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    protected $attributes = [
        'guard_name' => 'web'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public static function defaultPermissions()
    {
        return [
            // Usuários
            'view users',
            'create users',
            'edit users',
            'delete users',
            
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
            
            // Relatórios
            'view reports',
            
            // Configurações
            'manage settings'
        ];
    }
} 