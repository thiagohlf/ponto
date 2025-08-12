@extends('layouts.app')

@section('header')

    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Relatório de Ausências
        </h2>
        <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar
        </a>
    </div>

@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.absences') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial *</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date', date('Y-m-01')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final *</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date', date('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Funcionário</label>
                            <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os funcionários</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="absence_type" class="block text-sm font-medium text-gray-700">Tipo de Ausência</label>
                            <select name="absence_type" id="absence_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os tipos</option>
                                <option value="sick_leave" {{ request('absence_type') == 'sick_leave' ? 'selected' : '' }}>Atestado Médico</option>
                                <option value="vacation" {{ request('absence_type') == 'vacation' ? 'selected' : '' }}>Férias</option>
                                <option value="personal" {{ request('absence_type') == 'personal' ? 'selected' : '' }}>Pessoal</option>
                                <option value="maternity" {{ request('absence_type') == 'maternity' ? 'selected' : '' }}>Licença Maternidade</option>
                                <option value="paternity" {{ request('absence_type') == 'paternity' ? 'selected' : '' }}>Licença Paternidade</option>
                                <option value="other" {{ request('absence_type') == 'other' ? 'selected' : '' }}>Outros</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                            </select>
                        </div>

                        <div class="md:col-span-5 flex justify-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Gerar Relatório
                            </button>
                            <a href="{{ route('reports.absences') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if(request()->hasAny(['start_date', 'end_date']))
                <!-- Resumo -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo do Período</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $absences->count() }}</div>
                                <div class="text-sm text-gray-500">Total de Ausências</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $totalDays }}</div>
                                <div class="text-sm text-gray-500">Total de Dias</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        @if($absences->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Funcionário
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Período
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipo
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dias
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Motivo
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($absences as $absence)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $absence->employee->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $absence->employee->registration_number }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $absence->start_date->format('d/m/Y') }} 
                                                    @if($absence->end_date && $absence->end_date != $absence->start_date)
                                                        - {{ $absence->end_date->format('d/m/Y') }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($absence->absence_type === 'sick_leave') bg-red-100 text-red-800
                                                        @elseif($absence->absence_type === 'vacation') bg-green-100 text-green-800
                                                        @elseif($absence->absence_type === 'personal') bg-blue-100 text-blue-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        @switch($absence->absence_type)
                                                            @case('sick_leave') Atestado Médico @break
                                                            @case('vacation') Férias @break
                                                            @case('personal') Pessoal @break
                                                            @case('maternity') Licença Maternidade @break
                                                            @case('paternity') Licença Paternidade @break
                                                            @default {{ ucfirst($absence->absence_type) }}
                                                        @endswitch
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $absence->total_days }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                    {{ $absence->reason ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($absence->status === 'approved')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Aprovado
                                                        </span>
                                                    @elseif($absence->status === 'pending')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Pendente
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Rejeitado
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Informações do Relatório -->
                            <div class="mt-6 text-sm text-gray-500">
                                <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
                                <p>Período: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '' }} até {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '' }}</p>
                                @if(request('employee_id'))
                                    <p>Funcionário: {{ $employees->find(request('employee_id'))->name ?? 'N/A' }}</p>
                                @endif
                                @if(request('absence_type'))
                                    <p>Tipo: {{ ucfirst(str_replace('_', ' ', request('absence_type'))) }}</p>
                                @endif
                                @if(request('status'))
                                    <p>Status: {{ ucfirst(request('status')) }}</p>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma ausência encontrada</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Não foram encontradas ausências para o período e filtros selecionados.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Relatório de Ausências</h3>
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