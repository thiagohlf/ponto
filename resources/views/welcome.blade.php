<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistema de Ponto Eletrônico') }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen"
    style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 25%, #2d4a2d 50%, #3d6b3d 75%, #4d8c4d 100%);">

    <!-- Header simples -->
    <header class="p-6">
        <div class="max-w-md mx-auto text-center">
            <h1 class="text-2xl font-bold text-white">Sistema de Ponto</h1>

            @if (Route::has('register') && config('app.registration_enabled', true) && !auth()->check())
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="inline-block py-2 px-4 rounded text-white font-medium"
                        style="background-color: #2563eb; transition: background-color 0.2s; text-decoration: none;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'">
                        Registrar
                    </a>
                </div>
            @endif
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main class="flex items-center justify-center min-h-screen px-6">
        <div style="width: 600px;">

            @guest
                <!-- Formulário de Login -->
                <div class="bg-white border border-gray-400 rounded-lg p-6"
                    style="box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Login</h2>

                    <!-- Mensagens -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-700 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded text-red-700 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                E-mail
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full px-3 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                placeholder="seu@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Senha
                            </label>
                            <input id="password" type="password" name="password" required
                                class="w-full px-3 py-1.5 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                placeholder="••••••••">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lembrar-me -->
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                    style="margin-right: 6px;">
                                <span class="text-sm text-gray-700 font-medium">Lembrar-me</span>
                            </label>
                        </div>

                        <!-- Botões lado a lado -->
                        <div class="flex justify-center" style="gap: 10px;">
                            <button type="submit" class="py-2 px-4 rounded text-white font-medium"
                                style="width: 145px; background-color: #2563eb; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#1d4ed8'"
                                onmouseout="this.style.backgroundColor='#2563eb'">
                                Entrar
                            </button>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="inline-flex items-center justify-center py-2 px-4 rounded text-white font-medium"
                                    style="width: 145px; background-color: #6b7280; transition: background-color 0.2s; text-decoration: none; white-space: nowrap; font-size: 14px;"
                                    onmouseover="this.style.backgroundColor='#4b5563'"
                                    onmouseout="this.style.backgroundColor='#6b7280'">
                                    Recuperar senha
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            @else
                <!-- Usuário logado -->
                <div class="bg-white border border-gray-400 rounded-lg p-6 text-center"
                    style="box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Bem-vindo!</h2>
                    <p class="text-gray-600 mb-6">Você já está logado no sistema.</p>
                    <a href="{{ url('/dashboard') }}" class="inline-block py-2 px-4 rounded text-white font-medium"
                        style="background-color: #2563eb; transition: background-color 0.2s; text-decoration: none;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'">
                        Ir para Dashboard
                    </a>
                </div>
            @endguest

        </div>
    </main>

</body>

</html>
