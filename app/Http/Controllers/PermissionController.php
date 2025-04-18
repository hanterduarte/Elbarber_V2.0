<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string'],
            'module' => ['required', 'string', 'max:255'],
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function toggleStatus(Permission $permission)
    {
        $permission->update(['active' => !$permission->active]);
        return redirect()->route('permissions.index')->with('success', 'Permission status updated successfully.');
    }
} 