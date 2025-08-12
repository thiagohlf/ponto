@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Relatório de Registros de Ponto
        </h2>
        <div class="flex space-x-2">
            @can('exportar_relatorios')
                <a href="{{ route('reports.export.time-records', request()->query()) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Exportar CSV
                </a>

                @if (request()->hasAny(['start_date', 'end_date']))
                    @if (request('employee_id'))
                        <!-- PDF Individual -->
                        <a href="{{ route('reports.pdf.timesheet', request()->query()) }}"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            📄 Gerar PDF Individual
                        </a>
                    @else
                        <!-- PDF de Todos -->
                        <a href="{{ route('reports.pdf.all-timesheets', request()->query()) }}"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            📁 Gerar PDFs de Todos
                        </a>
                    @endif
                @endif
            @endcan
            <a href="{{ route('reports.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.time-records') }}"
                        class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial *</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ request('start_date', date('Y-m-01')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final *</label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ request('end_date', date('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Funcionário</label>
                            <select name="employee_id" id="employee_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os funcionários</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="company_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                            <select name="company_id" id="company_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas as empresas</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex justify-end space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Gerar Relatório
                            </button>
                            <a href="{{ route('reports.time-records') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if (request()->hasAny(['start_date', 'end_date']))
                <!-- Resumo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo do Período</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $timeRecords->count() }}</div>
                                <div class="text-sm text-gray-500">Total de Registros</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $timeRecords->where('status', 'valid')->count() }}</div>
                                <div class="text-sm text-gray-500">Registros Válidos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $timeRecords->where('status', 'pending_approval')->count() }}</div>
                                <div class="text-sm text-gray-500">Pendentes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $timeRecords->where('identification_method', 'manual')->count() }}</div>
                                <div class="text-sm text-gray-500">Registros Manuais</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        @if ($timeRecords->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Data/Hora
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Funcionário
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipo
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Método
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Relógio
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                NSR
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            @can('exportar_relatorios')
                                                @if (!request('employee_id'))
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Ações
                                                    </th>
                                                @endif
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $processedEmployees = [];
                                        @endphp
                                        @foreach ($timeRecords as $record)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $record->record_date->format('d/m/Y') }}</div>
                                                    <div class="text-sm text-gray-500">{{ $record->record_time }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $record->employee->user->name }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $record->employee->registration_number }}</div>
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
                                                            <span class="flex items-center text-green-600">
                                                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M6.625 2.655A9 9 0 0119 11a1 1 0 11-2 0 7 7 0 00-9.625-6.492 1 1 0 11-.75-1.853zM4.662 4.959A1 1 0 014.75 6.37 6.97 6.97 0 003 11a1 1 0 11-2 0 8.97 8.97 0 012.25-5.953 1 1 0 011.412-.088z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                                Biometria
                                                            </span>
                                                        @break

                                                        @case('rfid')
                                                            <span class="flex items-center text-blue-600">
                                                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path
                                                                        d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z">
                                                                    </path>
                                                                </svg>
                                                                RFID
                                                            </span>
                                                        @break

                                                        @case('manual')
                                                            <span class="text-orange-600 font-medium">Manual</span>
                                                        @break

                                                        @default
                                                            {{ $record->identification_method }}
                                                    @endswitch
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    Sistema Web
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                                    {{ $record->nsr }}
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
                                                @can('exportar_relatorios')
                                                    @if (!request('employee_id'))
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            @if (!in_array($record->employee->id, $processedEmployees))
                                                                @php $processedEmployees[] = $record->employee->id; @endphp
                                                                <a href="{{ route('reports.pdf.timesheet', array_merge(request()->query(), ['employee_id' => $record->employee->id])) }}"
                                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                                    📄 PDF
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endif
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (!request('employee_id') && $timeRecords->count() > 0)
                                @can('exportar_relatorios')
                                    <!-- Seção de PDFs Individuais -->
                                    <div class="mt-8 border-t pt-6">
                                        <h4 class="text-lg font-medium text-gray-900 mb-4">📄 Gerar PDF Individual por
                                            Funcionário</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @php
                                                $uniqueEmployees = $timeRecords
                                                    ->groupBy('employee_id')
                                                    ->map(function ($records) {
                                                        return $records->first()->employee;
                                                    });
                                            @endphp
                                            @foreach ($uniqueEmployees as $employee)
                                                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $employee->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $employee->registration_number }}</div>
                                                        <div class="text-xs text-gray-400">
                                                            {{ $timeRecords->where('employee_id', $employee->id)->count() }}
                                                            registros
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('reports.pdf.timesheet', array_merge(request()->query(), ['employee_id' => $employee->id])) }}"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        📄 Gerar PDF
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endcan
                            @endif

                            <!-- Informações do Relatório -->
                            <div class="mt-6 text-sm text-gray-500">
                                <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
                                <p>Período:
                                    {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '' }}
                                    até
                                    {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '' }}
                                </p>
                                @if (request('employee_id'))
                                    <p>Funcionário: {{ $employees->find(request('employee_id'))->name ?? 'N/A' }}</p>
                                @endif
                                @if (request('company_id'))
                                    <p>Empresa: {{ $companies->find(request('company_id'))->name ?? 'N/A' }}</p>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum registro encontrado</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Não foram encontrados registros de ponto para o período e filtros selecionados.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Relatório de Registros de Ponto</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Selecione o período e os filtros desejados para gerar o relatório.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
