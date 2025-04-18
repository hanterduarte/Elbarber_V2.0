<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'El Barber') }}</title>

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
                                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="text-white">
                                        <i class="fas fa-home"></i> Dashboard
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('appointments.index') }}" :active="request()->routeIs('appointments.*')" class="text-white">
                                        <i class="fas fa-calendar-alt"></i> Agendamentos
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.*')" class="text-white">
                                        <i class="fas fa-users"></i> Clientes
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('services.index') }}" :active="request()->routeIs('services.*')" class="text-white">
                                        <i class="fas fa-cut"></i> Serviços
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')" class="text-white">
                                        <i class="fas fa-box"></i> Produtos
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('pos.index') }}" :active="request()->routeIs('pos.*')" class="text-white">
                                        <i class="fas fa-cash-register"></i> PDV
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('payment-methods.index') }}" :active="request()->routeIs('payment-methods.*')" class="text-white">
                                        <i class="fas fa-credit-card"></i> Métodos de Pagamento
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('cash-register.index') }}" :active="request()->routeIs('cash-register.*')" class="text-white">
                                        <i class="fas fa-cash-register"></i> Caixa
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('reports.services') }}" :active="request()->routeIs('reports.*')" class="text-white">
                                        <i class="fas fa-chart-bar"></i> Relatórios
                                    </x-nav-link>
                                </li>
                                <li class="nav-item">
                                    <x-nav-link href="{{ route('barbers.index') }}" :active="request()->routeIs('barbers.*')" class="text-white">
                                        <i class="fas fa-cut"></i> Barbeiros
                                    </x-nav-link>
                                </li>
                                @auth
                                    @if(auth()->user()->hasRole('admin'))
                                    <li class="nav-item">
                                        <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" class="text-white">
                                            <i class="fas fa-user-cog"></i> Usuários
                                        </x-nav-link>
                                    </li>
                                    <li class="nav-item">
                                        <x-nav-link href="{{ route('roles.index') }}" :active="request()->routeIs('roles.*')" class="text-white">
                                            <i class="fas fa-user-tag"></i> Funções
                                        </x-nav-link>
                                    </li>
                                    <li class="nav-item">
                                        <x-nav-link href="{{ route('permissions.index') }}" :active="request()->routeIs('permissions.*')" class="text-white">
                                            <i class="fas fa-key"></i> Permissões
                                        </x-nav-link>
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

    @stack('scripts')
</body>
</html> 