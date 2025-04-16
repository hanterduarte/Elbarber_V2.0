<x-layouts.guest>
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-0 text-white">El Barber</h1>
    </div>

    <h2 class="h3 mb-4 text-white">Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label text-uppercase small text-white">E-mail:</label>
            <input type="email" class="form-control form-control-lg bg-light @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label text-uppercase small text-white">Senha:</label>
            <input type="password" class="form-control form-control-lg bg-light @error('password') is-invalid @enderror" 
                   id="password" name="password" required>
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-success btn-lg text-uppercase">
                Entrar
            </button>
        </div>

        <div class="d-flex justify-content-between">
            <span class="text-decoration-none text-white">
                <strong>CADASTRAR</strong>
            </span>
            <span class="text-decoration-none text-white">
                <strong>ESQUECEU A SENHA?</strong>
            </span>
        </div>
    </form>

    @push('styles')
    <style>
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ asset("images/barbershop-bg.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .btn-success {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }
    .btn-success:hover {
        background-color: #45a049;
        border-color: #45a049;
    }
    .form-control {
        border: none;
        background-color: rgba(255, 255, 255, 0.9) !important;
    }
    .form-control:focus {
        box-shadow: none;
        border: 1px solid #4CAF50;
        background-color: rgba(255, 255, 255, 1) !important;
    }
    .bg-white {
        background-color: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px);
    }
    </style>
    @endpush
</x-layouts.guest> 