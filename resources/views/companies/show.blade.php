<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $company->name }}
            </h2>
            <div class="flex space-x-2">
                @can('gerenciar_empresas')
                    <a href="{{ route('companies.edit', $company) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações da Empresa -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Empresa</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Razão Social</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $company->name }}</p>
                                </div>

                                @if($company->trade_name)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nome Fantasia</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company->trade_name }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $company->cnpj }}</p>
                                </div>

                                @if($company->state_registration)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Inscrição Estadual</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company->state_registration }}</p>
                                    </div>
                                @endif

                                @if($company->municipal_registration)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Inscrição Municipal</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company->municipal_registration }}</p>
                                    </div>
                                @endif

                                @if($company->email)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company->email }}</p>
                                    </div>
                                @endif

                                @if($company->phone)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $company->phone }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>
                            
                            <div class="text-sm text-gray-900">
                                <p>{{ $company->address }}, {{ $company->number }}</p>
                                @if($company->complement)
                                    <p>{{ $company->complement }}</p>
                                @endif
                                <p>{{ $company->neighborhood }}</p>
                                <p>{{ $company->city }}/{{ $company->state }} - {{ $company->zip_code }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Configurações -->
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações do Sistema</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tolerância</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $company->tolerance_minutes }} minutos</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Justificativa Obrigatória</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $company->requires_justification ? 'Sim' : 'Não' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                            
                            @if($company->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Empresa Ativa
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Empresa Inativa
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Funcionários:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $company->employees->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Departamentos:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $company->departments->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Relógios de Ponto:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $company->timeClocks->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                            
                            <div class="space-y-2">
                                @can('gerenciar_funcionarios')
                                    <a href="{{ route('employees.index', ['company_id' => $company->id]) }}" 
                                       class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        Ver Funcionários
                                    </a>
                                @endcan

                                @can('gerenciar_departamentos')
                                    <a href="{{ route('departments.index', ['company_id' => $company->id]) }}" 
                                       class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Ver Departamentos
                                    </a>
                                @endcan

                                @can('gerenciar_relogios')
                                    <a href="{{ route('time-clocks.index', ['company_id' => $company->id]) }}" 
                                       class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ver Relógios de Ponto
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>