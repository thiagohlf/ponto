@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $employee->user->name }}
        </h2>
        <div class="flex space-x-2">
            @can('gerenciar_funcionarios')
                <a href="{{ route('employees.edit', $employee) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
            @endcan
            <a href="{{ route('employees.index') }}"
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
                <!-- Informações Pessoais -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->user->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CPF</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->cpf }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">RG</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->rg ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->birth_date ? $employee->birth_date->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->email ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Trabalhistas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Trabalhistas</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->registration_number }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->pis_pasep ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Admissão</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->admission_date->format('d/m/Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Demissão</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->dismissal_date ? $employee->dismissal_date->format('d/m/Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cargo</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->position }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Salário</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->salary ? 'R$ ' . number_format($employee->salary, 2, ',', '.') : 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Empresa</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->company->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Departamento</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Carga Horária Semanal</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->weekly_hours }}h</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Intervalo para
                                        Refeição</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->has_meal_break ? $employee->meal_break_minutes . ' minutos' : 'Não possui' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Status e Ações -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Situação:</span>
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
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Controle de Ponto:</span>
                                    @if ($employee->exempt_time_control)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Isento
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Obrigatório
                                        </span>
                                    @endif
                                </div>

                                @if ($employee->rfid_card)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Cartão RFID:</span>
                                        <span class="text-sm text-gray-900">{{ $employee->rfid_card }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>

                            <div class="space-y-2">
                                @can('gerenciar_usuarios')
                                    @if ($employee->user)
                                        <a href="{{ route('employees.user-permissions', $employee) }}"
                                            class="w-full flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                                </path>
                                            </svg>
                                            Gerenciar Perfis
                                        </a>
                                    @else
                                        <div
                                            class="w-full flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-md">
                                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Usuário não criado
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Registros -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Últimos Registros de Ponto</h3>

                    @if ($employee->timeRecords->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Hora</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Método</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($employee->timeRecords as $record)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->record_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->record_time }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @switch($record->identification_method)
                                                    @case('biometric')
                                                        Biometria
                                                    @break

                                                    @case('rfid')
                                                        Cartão RFID
                                                    @break

                                                    @case('pin')
                                                        PIN
                                                    @break

                                                    @case('facial')
                                                        Facial
                                                    @break

                                                    @case('manual')
                                                        Manual
                                                    @break

                                                    @default
                                                        {{ $record->identification_method }}
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($record->status === 'valid')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Válido
                                                    </span>
                                                @elseif($record->status === 'pending_approval')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pendente
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inválido
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('employees.time-records', $employee) }}"
                                class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                Ver todos os registros →
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Nenhum registro de ponto encontrado.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
