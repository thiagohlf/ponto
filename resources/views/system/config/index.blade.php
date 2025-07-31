<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações do Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('system.config.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Configurações de Registro -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
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
                                    <input type="checkbox" 
                                           name="registration_enabled" 
                                           value="1"
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    {{ $configs['registration_enabled'] ? 'Desativar' : 'Ativar' }} Registro
                                </a>
                            </div>
                        </div>

                        <!-- Configurações de Backup -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <svg class="inline w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
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
                                        <input type="checkbox" 
                                               name="backup_enabled" 
                                               value="1"
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
                                    <input type="number" 
                                           name="backup_retention_days" 
                                           id="backup_retention_days"
                                           value="{{ $configs['backup_retention_days'] }}"
                                           min="1" 
                                           max="365"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                                    <span class="ml-2 text-sm text-gray-500">dias</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de ação -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" 
                                    onclick="window.location.reload()"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
</x-app-layout>