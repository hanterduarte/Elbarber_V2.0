@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ isset($profile) ? 'Editar Perfil' : 'Novo Perfil' }}
                </div>

                <div class="card-body">
                    @if (isset($profile))
                        <form action="{{ route('profiles.update', $profile) }}" method="POST">
                        @method('PUT')
                    @else
                        <form action="{{ route('profiles.store') }}" method="POST">
                    @endif
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $profile->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $profile->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="permissions" class="form-label">Permissões</label>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="permissions[]" 
                                       value="users_manage" 
                                       id="users_manage"
                                       {{ isset($profile) && is_array($profile->permissions) && in_array('users_manage', $profile->permissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="users_manage">
                                    Gerenciar Usuários
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="permissions[]" 
                                       value="profiles_manage" 
                                       id="profiles_manage"
                                       {{ isset($profile) && is_array($profile->permissions) && in_array('profiles_manage', $profile->permissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="profiles_manage">
                                    Gerenciar Perfis
                                </label>
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $profile->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Ativo</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('profiles.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 