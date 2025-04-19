@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gerenciar Permissões</h5>
                    <form action="{{ route('permissions.sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Sincronizar Permissões
                        </button>
                    </form>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Função</th>
                                    @foreach ($permissions as $permission)
                                        <th class="text-center">{{ $permission->description }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        @foreach ($permissions as $permission)
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input permission-checkbox"
                                                           data-role-id="{{ $role->id }}"
                                                           data-permission-id="{{ $permission->id }}"
                                                           {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roleId = this.dataset.roleId;
            const permissionId = this.dataset.permissionId;
            const isChecked = this.checked;

            // Enviar requisição AJAX
            fetch(`/permissions/${roleId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    permissions: Array.from(document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]:checked`))
                        .map(cb => parseInt(cb.dataset.permissionId))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
                    
                    // Remover alerta após 3 segundos
                    setTimeout(() => alert.remove(), 3000);
                } else {
                    // Reverter checkbox em caso de erro
                    this.checked = !isChecked;
                    
                    // Mostrar mensagem de erro
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
                    
                    // Remover alerta após 3 segundos
                    setTimeout(() => alert.remove(), 3000);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                // Reverter checkbox em caso de erro
                this.checked = !isChecked;
                
                // Mostrar mensagem de erro
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    Erro ao atualizar permissão. Por favor, tente novamente.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
                
                // Remover alerta após 3 segundos
                setTimeout(() => alert.remove(), 3000);
            });
        });
    });
});
</script>
@endpush 