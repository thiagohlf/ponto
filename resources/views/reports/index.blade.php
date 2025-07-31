<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Relatórios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Introdução -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Central de Relatórios</h3>
                    <p class="text-gray-600">
                        Acesse relatórios detalhados sobre registros de ponto, frequência, ausências e horas extras.
                        Todos os relatórios podem ser filtrados por período, funcionário ou departamento.
                    </p>
                </div>
            </div>

            <!-- Grid de Relatórios -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Relatório de Registros de Ponto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Registros de Ponto</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Relatório detalhado de todos os registros de ponto com filtros por funcionário, período e tipo de marcação.
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Exportável para CSV</span>
                            <a href="{{ route('reports.time-records') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Frequência -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Resumo de Frequência</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Análise consolidada da frequência dos funcionários com cálculo de presença, faltas e atrasos.
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Com percentuais</span>
                            <a href="{{ route('reports.attendance-summary') }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Horas Extras -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Horas Extras</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Controle de horas extras realizadas com cálculos financeiros e status de aprovação.
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Com valores calculados</span>
                            <a href="{{ route('reports.overtime') }}" 
                               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Ausências -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Ausências</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Relatório de faltas, licenças e ausências com classificação por tipo e status de aprovação.
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Por tipo de ausência</span>
                            <a href="{{ route('reports.absences') }}" 
                               class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Acessar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card de Estatísticas Rápidas -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 overflow-hidden shadow-sm sm:rounded-lg text-white">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium">Estatísticas Rápidas</h3>
                            </div>
                        </div>
                        <p class="text-white text-sm mb-4 opacity-90">
                            Visualize estatísticas em tempo real do sistema de ponto eletrônico.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="opacity-90">Registros hoje:</span>
                                <span class="font-semibold">{{ \App\Models\TimeRecord::whereDate('record_date', today())->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="opacity-90">Funcionários ativos:</span>
                                <span class="font-semibold">{{ \App\Models\Employee::active()->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="opacity-90">Pendências:</span>
                                <span class="font-semibold">{{ \App\Models\Absence::pending()->count() + \App\Models\Overtime::pending()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card de Ajuda -->
                <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Precisa de Ajuda?</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Consulte nossa documentação ou entre em contato com o suporte técnico.
                        </p>
                        <div class="space-y-2">
                            <a href="#" class="block text-sm text-blue-600 hover:text-blue-500">
                                📖 Manual do usuário
                            </a>
                            <a href="#" class="block text-sm text-blue-600 hover:text-blue-500">
                                💬 Suporte técnico
                            </a>
                            <a href="#" class="block text-sm text-blue-600 hover:text-blue-500">
                                🎥 Vídeos tutoriais
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dicas de Uso -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Dicas para usar os relatórios</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Use filtros de data para analisar períodos específicos</li>
                                <li>Exporte os dados para Excel/CSV para análises mais detalhadas</li>
                                <li>Combine diferentes relatórios para obter insights completos</li>
                                <li>Verifique regularmente as pendências de aprovação</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>