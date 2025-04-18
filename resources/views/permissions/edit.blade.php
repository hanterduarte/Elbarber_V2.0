@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Permission</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $permission->description) }}" required>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="module" class="form-label">Module</label>
                            <select class="form-select @error('module') is-invalid @enderror" id="module" name="module" required>
                                <option value="">Select a module</option>
                                <option value="users" {{ old('module', $permission->module) == 'users' ? 'selected' : '' }}>Users</option>
                                <option value="roles" {{ old('module', $permission->module) == 'roles' ? 'selected' : '' }}>Roles</option>
                                <option value="permissions" {{ old('module', $permission->module) == 'permissions' ? 'selected' : '' }}>Permissions</option>
                                <option value="appointments" {{ old('module', $permission->module) == 'appointments' ? 'selected' : '' }}>Appointments</option>
                                <option value="services" {{ old('module', $permission->module) == 'services' ? 'selected' : '' }}>Services</option>
                            </select>
                            @error('module')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $permission->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Permission
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 