@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $department->name }}
        </h2>
        <div class="flex space-x-2">
            @can('gerenciar_departamentos')
                <a href="{{ route('departments.edit', $department) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
            @endcan
            <a href="{{ route('departments.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações do Departamento -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Departamento</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $department->name }}</p>
                                </div>

                                @if ($department->code)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Código</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $department->code }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Empresa</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $department->company->name }}</p>
                                </div>

                                @if ($department->parentDepartment)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Departamento Superior</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $department->parentDepartment->name }}</p>
                                    </div>
                                @endif

                                @if ($department->cost_center)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Centro de Custo</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $department->cost_center }}</p>
                                    </div>
                                @endif

                                @if ($department->description)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Descrição</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $department->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Subdepartamentos -->
                    @if ($department->childDepartments->count() > 0)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Subdepartamentos</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($department->childDepartments as $child)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900">{{ $child->name }}</h4>
                                                    @if ($child->code)
                                                        <p class="text-xs text-gray-500">{{ $child->code }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $child->employees->count() }}</p>
                                                    <p class="text-xs text-gray-500">funcionários</p>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('departments.show', $child) }}"
                                                    class="text-blue-600 hover:text-blue-900 text-xs">
                                                    Ver detalhes →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Funcionários -->
                    @if ($department->employees->count() > 0)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Funcionários do Departamento</h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Funcionário
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Cargo
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Ações
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($department->employees->take(10) as $employee)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-8 w-8">
                                                                <div
                                                                    class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                                    <span class="text-xs font-medium text-gray-700">
                                                                        {{ substr($employee->name, 0, 2) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $employee->name }}
                                                                </div>
                                                                <div class="text-sm text-gray-500">
                                                                    {{ $employee->registration_number }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $employee->position }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($employee->active && !$employee->dismissal_date)
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Ativo
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Inativo
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('employees.show', $employee) }}"
                                                            class="text-blue-600 hover:text-blue-900">
                                                            Ver
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($department->employees->count() > 10)
                                    <div class="mt-4">
                                        <a href="{{ route('employees.index', ['department_id' => $department->id]) }}"
                                            class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                            Ver todos os {{ $department->employees->count() }} funcionários →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>

                            @if ($department->active)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Departamento Ativo
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Departamento Inativo
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
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $department->employees->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Funcionários Ativos:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $department->employees->where('active', true)->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Subdepartamentos:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $department->childDepartments->count() }}</span>
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
                                    <a href="{{ route('employees.index', ['department_id' => $department->id]) }}"
                                        class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                            </path>
                                        </svg>
                                        Ver Funcionários
                                    </a>
                                @endcan

                                @can('gerenciar_departamentos')
                                    <a href="{{ route('departments.create', ['parent_department_id' => $department->id]) }}"
                                        class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Criar Subdepartamento
                                    </a>
                                @endcan

                                @can('visualizar_relatorios')
                                    <a href="{{ route('reports.attendance-summary', ['department_id' => $department->id]) }}"
                                        class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        Relatório de Frequência
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
