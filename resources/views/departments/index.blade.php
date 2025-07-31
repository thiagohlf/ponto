<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Departamentos
            </h2>
            @can('gerenciar_departamentos')
                <a href="{{ route('departments.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Novo Departamento
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($departments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($departments as $department)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    @if($department->active)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Ativo
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Inativo
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $department->name }}</h3>
                                            @if($department->code)
                                                <p class="text-sm text-gray-600">Código: {{ $department->code }}</p>
                                            @endif
                                        </div>

                                        @if($department->description)
                                            <div class="mb-4">
                                                <p class="text-sm text-gray-600">{{ Str::limit($department->description, 100) }}</p>
                                            </div>
                                        @endif

                                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <span>{{ $department->company->name }}</span>
                                            </div>

                                            @if($department->cost_center)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>Centro de Custo: {{ $department->cost_center }}</span>
                                                </div>
                                            @endif

                                            @if($department->parentDepartment)
                                                <div class="flex items-center">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                                    </svg>
                                                    <span>Subordinado a: {{ $department->parentDepartment->name }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Estatísticas -->
                                        <div class="border-t pt-4 mb-4">
                                            <div class="grid grid-cols-2 gap-4 text-center">
                                                <div>
                                                    <div class="text-2xl font-bold text-blue-600">{{ $department->employees_count ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">Funcionários</div>
                                                </div>
                                                <div>
                                                    <div class="text-2xl font-bold text-green-600">{{ $department->childDepartments_count ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">Subdepartamentos</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ações -->
                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('departments.show', $department) }}" 
                                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                Ver detalhes
                                            </a>
                                            
                                            @can('gerenciar_departamentos')
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('departments.edit', $department) }}" 
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
                            {{ $departments->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum departamento cadastrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece cadastrando o primeiro departamento.</p>
                            @can('gerenciar_departamentos')
                                <div class="mt-6">
                                    <a href="{{ route('departments.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Novo Departamento
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