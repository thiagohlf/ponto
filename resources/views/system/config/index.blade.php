@extends('layouts.app')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Configurações do Sistema') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('system.config.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Configurações de Registro -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                                Registro de Usuários
                            </h3>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">
                                        Permitir registro de novos usuários
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Quando desabilitado, apenas administradores podem criar novos usuários
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <input type="hidden" name="registration_enabled" value="0">
                                    <input type="checkbox" name="registration_enabled" value="1"
                                        {{ $configs['registration_enabled'] ? 'checked' : '' }}
                                        class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                                    <label class="ml-2 text-sm text-gray-700">
                                        {{ $configs['registration_enabled'] ? 'Ativado' : 'Desativado' }}
                                    </label>
                                </div>
                            </div>

                            <!-- Botão de alternância rápida -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('system.config.toggle-registration') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    {{ $configs['registration_enabled'] ? 'Desativar' : 'Ativar' }} Registro
                                </a>
                            </div>
                        </div>

                        <!-- Configurações de Backup -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                    </path>
                                </svg>
                                Configurações de Backup
                            </h3>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">
                                            Backup automático
                                        </label>
                                        <p class="text-sm text-gray-500">
                                            Realizar backup automático dos dados
                                        </p>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="hidden" name="backup_enabled" value="0">
                                        <input type="checkbox" name="backup_enabled" value="1"
                                            {{ $configs['backup_enabled'] ? 'checked' : '' }}
                                            class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                                        <label class="ml-2 text-sm text-gray-700">
                                            {{ $configs['backup_enabled'] ? 'Ativado' : 'Desativado' }}
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="backup_retention_days" class="block text-sm font-medium text-gray-700 mb-2">
                                        Dias de retenção do backup
                                    </label>
                                    <input type="number" name="backup_retention_days" id="backup_retention_days"
                                        value="{{ $configs['backup_retention_days'] }}" min="1" max="365"
                                        class="w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <span class="ml-2 text-sm text-gray-500">dias</span>
                                </div>

                                <!-- Botão de Backup Manual - Visível apenas para Administradores -->
                                @if (auth()->user()->isAdmin())
                                    <!-- Debug Info (remover em produção) -->
                                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-xs text-green-700">
                                            ✅ Usuário: {{ auth()->user()->name }} |
                                            ✅ É Admin: {{ auth()->user()->isAdmin() ? 'SIM' : 'NÃO' }} |
                                            ✅ Pode configurar:
                                            {{ auth()->user()->can('configurar_sistema') ? 'SIM' : 'NÃO' }}
                                        </p>
                                    </div>

                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div class="w-full">
                                                    <h4 class="text-lg font-semibold text-blue-900 mb-2">
                                                        <svg class="inline w-5 h-5 mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                                            </path>
                                                        </svg>
                                                        Backup Manual do Banco de Dados MySQL
                                                    </h4>
                                                    <p class="text-sm text-blue-700 mb-3">
                                                        Criar backup completo do banco de dados MySQL para download seguro
                                                    </p>

                                                    <!-- Informações do banco -->
                                                    <div class="bg-blue-100 p-3 rounded-md mb-4">
                                                        <div class="grid grid-cols-2 gap-4 text-xs text-blue-800">
                                                            <div>
                                                                <strong>Banco:</strong>
                                                                {{ config('database.connections.mysql.database') }}
                                                            </div>
                                                            <div>
                                                                <strong>Host:</strong>
                                                                {{ config('database.connections.mysql.host') }}:{{ config('database.connections.mysql.port') }}
                                                            </div>
                                                            <div>
                                                                <strong>Método:</strong> <span id="backup-method">PHP/PDO
                                                                    (mysqldump não disponível)</span>
                                                            </div>
                                                            <div>
                                                                <strong>Formato:</strong> SQL com estrutura e dados
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex space-x-3">
                                                        <form method="POST" action="{{ route('system.backup.create') }}"
                                                            class="inline" id="backup-form">
                                                            @csrf
                                                            <button type="submit" id="backup-button"
                                                                onclick="return startBackup()"
                                                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <svg class="w-5 h-5 mr-2" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                                    id="backup-icon">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                                                    </path>
                                                                </svg>
                                                                <span id="backup-text">🔄 Criar Backup Agora</span>
                                                            </button>
                                                        </form>

                                                        <button onclick="loadBackupList()"
                                                            class="inline-flex items-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                                </path>
                                                            </svg>
                                                            Atualizar Lista
                                                        </button>
                                                    </div>

                                                    <!-- Barra de progresso -->
                                                    <div id="backup-progress" class="hidden mt-4">
                                                        <div class="bg-blue-200 rounded-full h-2">
                                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                                style="width: 0%" id="progress-bar"></div>
                                                        </div>
                                                        <p class="text-xs text-blue-700 mt-1" id="progress-text">Iniciando
                                                            backup...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lista de Backups Existentes -->
                                    <div class="pt-6 border-t border-gray-200">
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h4 class="text-lg font-semibold text-gray-900 mb-3">
                                                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                Backups Disponíveis
                                            </h4>
                                            <div id="backup-list" class="space-y-2">
                                                <div class="text-sm text-gray-500 flex items-center">
                                                    <svg class="animate-spin w-4 h-4 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                    Carregando backups...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Debug para não-admin -->
                                    <div class="pt-4 border-t border-gray-200">
                                        <div class="bg-red-50 p-4 rounded-lg">
                                            <p class="text-sm text-red-700">
                                                ❌ Backup não disponível - Usuário: {{ auth()->user()->name }} não é
                                                administrador
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Botões de ação -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" onclick="window.location.reload()"
                                class="px-4 py-2 border border-green-700 rounded-md shadow-sm text-sm font-medium text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Salvar Configurações
                            </button>
                        </div>
                    </form>

                    <!-- Informações do Sistema -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informações do Sistema
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-medium text-blue-900">Versão do Laravel</h4>
                                <p class="text-blue-700">{{ app()->version() }}</p>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-medium text-green-900">Versão do PHP</h4>
                                <p class="text-green-700">{{ PHP_VERSION }}</p>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="font-medium text-purple-900">Ambiente</h4>
                                <p class="text-purple-700">{{ app()->environment() }}</p>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h4 class="font-medium text-yellow-900">Debug</h4>
                                <p class="text-yellow-700">{{ config('app.debug') ? 'Ativado' : 'Desativado' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->isAdmin())
        <script>
            // Carregar lista de backups ao carregar a página
            document.addEventListener('DOMContentLoaded', function() {
                loadBackupList();
            });

            function loadBackupList() {
                fetch('{{ route('system.backup.list') }}')
                    .then(response => response.json())
                    .then(data => {
                        const backupList = document.getElementById('backup-list');

                        if (data.length === 0) {
                            backupList.innerHTML = '<div class="text-sm text-gray-500">Nenhum backup encontrado.</div>';
                            return;
                        }

                        let html = '';
                        data.forEach(backup => {
                            const date = new Date(backup.created_at);
                            const formattedDate = date.toLocaleString('pt-BR');

                            html += `
                                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">${backup.name}</p>
                                            <p class="text-xs text-gray-500">${formattedDate} • ${backup.size}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ url('system/backup/download') }}/${backup.name}" 
                                           class="inline-flex items-center px-3 py-1 bg-green-100 hover:bg-green-200 text-green-800 text-xs font-medium rounded-md transition-colors duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            `;
                        });

                        backupList.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erro ao carregar backups:', error);
                        document.getElementById('backup-list').innerHTML =
                            '<div class="text-sm text-red-500">Erro ao carregar lista de backups.</div>';
                    });
            }

            // Função para iniciar backup com feedback visual
            function startBackup() {
                if (!confirm(
                        '🔄 Deseja criar um backup do banco de dados MySQL agora?\n\n⚠️ O processo pode levar alguns minutos dependendo do tamanho do banco.\n\n✅ O arquivo será baixado automaticamente após a criação.'
                        )) {
                    return false;
                }

                // Elementos da interface
                const button = document.getElementById('backup-button');
                const buttonText = document.getElementById('backup-text');
                const buttonIcon = document.getElementById('backup-icon');
                const progress = document.getElementById('backup-progress');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');

                // Desabilitar botão e mostrar progresso
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                buttonText.textContent = '⏳ Criando Backup...';
                buttonIcon.classList.add('animate-spin');
                progress.classList.remove('hidden');

                // Simular progresso
                let progressValue = 0;
                const progressInterval = setInterval(() => {
                    progressValue += Math.random() * 15;
                    if (progressValue > 90) progressValue = 90;

                    progressBar.style.width = progressValue + '%';

                    if (progressValue < 30) {
                        progressText.textContent = 'Conectando ao banco de dados...';
                    } else if (progressValue < 60) {
                        progressText.textContent = 'Exportando estrutura das tabelas...';
                    } else if (progressValue < 90) {
                        progressText.textContent = 'Exportando dados das tabelas...';
                    } else {
                        progressText.textContent = 'Finalizando backup...';
                    }
                }, 200);

                // Resetar interface após um tempo (backup real)
                setTimeout(() => {
                    clearInterval(progressInterval);
                    progressBar.style.width = '100%';
                    progressText.textContent = 'Backup concluído! Iniciando download...';

                    setTimeout(() => {
                        // Resetar interface
                        button.disabled = false;
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                        buttonText.textContent = '🔄 Criar Backup Agora';
                        buttonIcon.classList.remove('animate-spin');
                        progress.classList.add('hidden');
                        progressBar.style.width = '0%';

                        // Recarregar lista de backups
                        loadBackupList();
                    }, 2000);
                }, 3000);

                return true;
            }

            // Recarregar lista após criar backup
            const backupForm = document.querySelector('form[action="{{ route('system.backup.create') }}"]');
            if (backupForm) {
                backupForm.addEventListener('submit', function(e) {
                    // O startBackup() já cuida do feedback visual
                });
            }
        </script>
    @endif
@endsection
