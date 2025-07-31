<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relógios de Ponto
            </h2>
            @can('gerenciar_relogios')
                <a href="{{ route('time-clocks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Novo Relógio
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Geral -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Geral dos Relógios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $timeClocks->count() }}</div>
                            <div class="text-sm text-gray-500">Total de Relógios</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $timeClocks->where('status', 'online')->count() }}</div>
                            <div class="text-sm text-gray-500">Online</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $timeClocks->where('status', 'offline')->count() }}</div>
                            <div class="text-sm text-gray-500">Offline</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $timeClocks->where('status', 'maintenance')->count() }}</div>
                            <div class="text-sm text-gray-500">Manutenção</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Relógios -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($timeClocks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($timeClocks as $timeClock)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6">
                                        <!-- Header com Status -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    @if($timeClock->status === 'online')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                                            Online
                                                        </span>
                                                    @elseif($timeClock->status === 'offline')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
                                                            Offline
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                                            Manutenção
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informações do Relógio -->
                                        <div class="mb-4">
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $timeClock->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $timeClock->location }}</p>
                                        </div>

                                        <!-- Detalhes Técnicos -->
                                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="font-mono">{{ $timeClock->serial_number }}</span>
                                            </div>
                                            
                                            @if($timeClock->model)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                    </svg>
                                                    <span>{{ $timeClock->manufacturer }} {{ $timeClock->model }}</span>
                                                </div>
                                            @endif

                                            @if($timeClock->ip_address)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                                    </svg>
                                                    <span class="font-mono">{{ $timeClock->ip_address }}</span>
                                                </div>
                                            @endif

                                            @if($timeClock->last_sync)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    <span>Última sync: {{ $timeClock->last_sync->format('d/m/Y H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Certificação -->
                                        @if($timeClock->certification_number)
                                            <div class="border-t pt-4 mb-4">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                    </svg>
                                                    <span>Certificado INMETRO: {{ $timeClock->certification_number }}</span>
                                                </div>
                                                @if($timeClock->certification_date)
                                                    <div class="text-xs text-gray-500 ml-6">
                                                        Válido até: {{ $timeClock->certification_date->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Estatísticas -->
                                        <div class="border-t pt-4 mb-4">
                                            <div class="grid grid-cols-2 gap-4 text-center">
                                                <div>
                                                    <div class="text-lg font-bold text-blue-600">{{ $timeClock->employees_count ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">Funcionários</div>
                                                </div>
                                                <div>
                                                    <div class="text-lg font-bold text-green-600">{{ $timeClock->records_today ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">Registros Hoje</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ações -->
                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('time-clocks.show', $timeClock) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                Ver detalhes
                                            </a>
                                            
                                            @can('gerenciar_relogios')
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('time-clocks.edit', $timeClock) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        Editar
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $timeClocks->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum relógio de ponto cadastrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece cadastrando o primeiro relógio de ponto.</p>
                            @can('gerenciar_relogios')
                                <div class="mt-6">
                                    <a href="{{ route('time-clocks.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Novo Relógio
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>