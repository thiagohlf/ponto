@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Registrar Ponto') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <!-- Data e Hora em Tempo Real -->
                    <div class="mb-8 w-full flex justify-center">
                        <div class="bg-black rounded-lg p-8 w-full max-w-6xl">
                            <div id="current-date" class="text-xl sm:text-2xl font-bold text-white mb-2 text-center">
                            </div>
                            <div id="current-time"
                                class="text-3xl sm:text-4xl lg:text-5xl font-bold text-green-400 mb-2 text-center">
                            </div>
                            <div id="current-day" class="text-base sm:text-lg text-gray-300 text-center"></div>
                        </div>
                    </div>

                    <!-- Mensagens de Sucesso/Erro -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulário de Registro -->
                    <form method="POST" action="{{ route('time-clock.register.store') }}" id="register-form">
                        @csrf

                        <!-- Botão Registrar Ponto -->
                        <button type="submit" id="register-btn"
                            class="mx-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-12 rounded-lg text-lg shadow-lg transform transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 flex items-center justify-center space-x-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Registrar Ponto</span>
                        </button>
                    </form>

                    <!-- Informações do Funcionário -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            <strong>Funcionário:</strong> {{ auth()->user()->name }}
                        </p>
                        @if (auth()->user()->employee)
                            <p class="text-sm text-gray-600">
                                <strong>Matrícula:</strong> {{ auth()->user()->employee->registration_number }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong>Empresa:</strong> {{ auth()->user()->employee->company->name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para atualizar data e hora
        function updateDateTime() {
            const now = new Date();

            // Configurações de localização para português brasileiro
            const dateOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'America/Sao_Paulo'
            };

            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'America/Sao_Paulo'
            };

            const dayOptions = {
                weekday: 'long',
                timeZone: 'America/Sao_Paulo'
            };

            // Atualizar elementos
            document.getElementById('current-date').textContent =
                now.toLocaleDateString('pt-BR', dateOptions);

            document.getElementById('current-time').textContent =
                now.toLocaleTimeString('pt-BR', timeOptions);

            document.getElementById('current-day').textContent =
                now.toLocaleDateString('pt-BR', dayOptions);
        }

        // Atualizar a cada segundo
        setInterval(updateDateTime, 1000);

        // Atualizar imediatamente ao carregar
        updateDateTime();

        // Feedback visual no botão
        document.getElementById('register-form').addEventListener('submit', function() {
            const btn = document.getElementById('register-btn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-6 h-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Registrando...
            `;
        });
    </script>
@endsection
