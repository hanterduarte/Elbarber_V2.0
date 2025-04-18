<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage settings');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('permissions.index', compact('roles', 'permissions'));
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();

            $permissions = $request->input('permissions', []);
            $role->permissions()->sync($permissions);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permissões atualizadas com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar permissões: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar permissões. Por favor, tente novamente.'
            ], 500);
        }
    }

    public function syncDefaultPermissions()
    {
        try {
            DB::beginTransaction();

            // Criar permissões padrão
            foreach (Permission::defaultPermissions() as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Criar roles padrão
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $managerRole = Role::firstOrCreate(['name' => 'manager']);
            $barberRole = Role::firstOrCreate(['name' => 'barber']);
            $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

            // Atribuir todas as permissões ao admin
            $adminRole->permissions()->sync(Permission::pluck('id'));

            // Atribuir permissões específicas aos outros roles
            $managerPermissions = Permission::whereIn('name', [
                'view users', 'create users', 'edit users',
                'view clients', 'create clients', 'edit clients',
                'view barbers', 'create barbers', 'edit barbers',
                'view services', 'create services', 'edit services',
                'view products', 'create products', 'edit products',
                'view appointments', 'create appointments', 'edit appointments',
                'view sales', 'create sales', 'edit sales',
                'view cash register', 'open cash register', 'close cash register',
                'view reports'
            ])->pluck('id');
            $managerRole->permissions()->sync($managerPermissions);

            $barberPermissions = Permission::whereIn('name', [
                'view clients',
                'view services',
                'view appointments', 'create appointments', 'edit appointments',
                'view sales', 'create sales'
            ])->pluck('id');
            $barberRole->permissions()->sync($barberPermissions);

            $cashierPermissions = Permission::whereIn('name', [
                'view clients',
                'view services',
                'view products',
                'view appointments',
                'view sales', 'create sales',
                'view cash register', 'open cash register', 'close cash register'
            ])->pluck('id');
            $cashierRole->permissions()->sync($cashierPermissions);

            DB::commit();

            return redirect()->route('permissions.index')
                ->with('success', 'Permissões sincronizadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao sincronizar permissões: ' . $e->getMessage());
            
            return redirect()->route('permissions.index')
                ->with('error', 'Erro ao sincronizar permissões. Por favor, tente novamente.');
        }
    }
} 