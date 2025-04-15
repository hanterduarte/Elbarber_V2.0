<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ELbarber') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                        <div class="position-sticky pt-3">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="fas fa-home"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Agendamentos</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                                        <i class="fas fa-users"></i> Clientes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                        <i class="fas fa-cut"></i> Serviços
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                        <i class="fas fa-box"></i> Produtos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                        <i class="fas fa-cash-register"></i>
                                        <span>PDV</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}" href="{{ route('payment-methods.index') }}">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Métodos de Pagamento</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('cash-register.*') ? 'active' : '' }}" href="{{ route('cash-register.index') }}">
                                        <i class="fas fa-cash-register"></i> Caixa
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.services') }}">
                                        <i class="fas fa-chart-bar"></i> Relatórios
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-white {{ request()->routeIs('barbers.*') ? 'active' : '' }}" href="{{ route('barbers.index') }}">
                                        <i class="fas fa-cut"></i> Barbeiros
                                    </a>
                                </li>
                                @auth
                                    @if(auth()->user()->hasRole('admin'))
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                            <i class="fas fa-user-cog"></i> Usuários
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                            <i class="fas fa-user-tag"></i> Funções
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                                            <i class="fas fa-key"></i> Permissões
                                        </a>
                                    </li>
                                    @endif
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <!-- Main content -->
                    <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 