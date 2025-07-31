<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Sistema de Ponto Eletrônico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total de Funcionários -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total de Funcionários</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalEmployees }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Funcionários Presentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Presentes Hoje</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $presentEmployees }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registros de Hoje -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Registros Hoje</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $todayRecords }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pendências -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pendências</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $pendingAbsences + $pendingOvertime }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Últimos Registros de Ponto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Últimos Registros de Ponto</h3>
                        <div class="space-y-3">
                            @forelse($recentTimeRecords as $record)
                                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $record->employee->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $record->record_date->format('d/m/Y') }} às {{ $record->record_time }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($record->record_type === 'entry') bg-green-100 text-green-800
                                            @elseif($record->record_type === 'exit') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            @switch($record->record_type)
                                                @case('entry')
                                                    Entrada
                                                @break

                                                @case('exit')
                                                    Saída
                                                @break

                                                @case('meal_start')
                                                    Saída Almoço
                                                @break

                                                @case('meal_end')
                                                    Retorno Almoço
                                                @break

                                                @default
                                                    {{ $record->record_type }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                                @empty
                                    <p class="text-gray-500 text-sm">Nenhum registro encontrado hoje.</p>
                                @endforelse
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('time-records.index') }}"
                                    class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                    Ver todos os registros →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status do Sistema -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status do Sistema</h3>

                            <!-- Relógios de Ponto -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Relógios de Ponto</span>
                                    <span class="text-sm text-gray-500">{{ $totalTimeClocks }} total</span>
                                </div>
                                @if ($offlineClocks > 0)
                                    <div class="mt-1 text-sm text-red-600">
                                        {{ $offlineClocks }} relógio(s) offline
                                    </div>
                                @else
                                    <div class="mt-1 text-sm text-green-600">
                                        Todos os relógios online
                                    </div>
                                @endif
                            </div>

                            <!-- Estatísticas da Semana -->
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Esta Semana</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total de Registros:</span>
                                        <span class="font-medium">{{ $weekStats['total_records'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Ausências:</span>
                                        <span class="font-medium">{{ $weekStats['total_absences'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Horas Extras:</span>
                                        <span
                                            class="font-medium">{{ number_format($weekStats['total_overtime_hours'], 1) }}h</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Atrasos:</span>
                                        <span class="font-medium">{{ $weekStats['late_arrivals'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Aniversariantes -->
                            @if ($birthdayEmployees->count() > 0)
                                <div class="border-t pt-4 mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Aniversariantes do Mês</h4>
                                    <div class="space-y-1">
                                        @foreach ($birthdayEmployees as $employee)
                                            <div class="text-sm text-gray-600">
                                                {{ $employee->name }} - {{ $employee->birth_date->format('d/m') }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Links Rápidos -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Acesso Rápido</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @can('gerenciar_funcionarios')
                                <a href="{{ route('employees.index') }}"
                                    class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">Funcionários</span>
                                </a>
                            @endcan

                            @can('visualizar_registros_ponto')
                                <a href="{{ route('time-records.index') }}"
                                    class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                    <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-900">Registros</span>
                                </a>
                            @endcan

                            @can('visualizar_relatorios')
                                <a href="{{ route('reports.index') }}"
                                    class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                    <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-purple-900">Relatórios</span>
                                </a>
                            @endcan

                            @can('gerenciar_empresas')
                                <a href="{{ route('companies.index') }}"
                                    class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-900">Empresas</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
