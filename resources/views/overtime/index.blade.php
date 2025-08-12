@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Horas Extras
        </h2>
        @can('solicitar_horas_extras')
            <a href="{{ route('overtime.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Solicitar Hora Extra
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('overtime.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Funcionário</label>
                            <select name="employee_id" id="employee_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os funcionários</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="overtime_type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="overtime_type" id="overtime_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os tipos</option>
                                <option value="daily_overtime"
                                    {{ request('overtime_type') == 'daily_overtime' ? 'selected' : '' }}>Hora Extra Diária
                                </option>
                                <option value="weekend_work"
                                    {{ request('overtime_type') == 'weekend_work' ? 'selected' : '' }}>Trabalho em Fim de
                                    Semana</option>
                                <option value="holiday_work"
                                    {{ request('overtime_type') == 'holiday_work' ? 'selected' : '' }}>Trabalho em Feriado
                                </option>
                                <option value="night_shift"
                                    {{ request('overtime_type') == 'night_shift' ? 'selected' : '' }}>Adicional Noturno
                                </option>
                                <option value="compensatory"
                                    {{ request('overtime_type') == 'compensatory' ? 'selected' : '' }}>Banco de Horas
                                </option>
                                <option value="emergency" {{ request('overtime_type') == 'emergency' ? 'selected' : '' }}>
                                    Emergência</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitado
                                </option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pago</option>
                            </select>
                        </div>

                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-5 flex justify-end space-x-2">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('overtime.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumo -->
            @if ($overtime->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo do Período</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $overtime->count() }}</div>
                                <div class="text-sm text-gray-500">Total de Registros</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ number_format($overtime->sum('total_minutes') / 60, 1) }}h</div>
                                <div class="text-sm text-gray-500">Total de Horas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $overtime->where('status', 'pending')->count() }}</div>
                                <div class="text-sm text-gray-500">Pendentes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">R$
                                    {{ number_format($overtime->sum('calculated_amount'), 2, ',', '.') }}</div>
                                <div class="text-sm text-gray-500">Valor Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Lista de Horas Extras -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($overtime->count() > 0)
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
                                            Data/Período
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Horas
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Valor
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
                                    @foreach ($overtime as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-xs font-medium text-gray-700">
                                                                {{ substr($record->employee->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $record->employee->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $record->employee->registration_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $record->work_date->format('d/m/Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $record->start_time }} -
                                                    {{ $record->end_time }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if ($record->overtime_type === 'daily_overtime') bg-blue-100 text-blue-800
                                                    @elseif($record->overtime_type === 'weekend_work') bg-purple-100 text-purple-800
                                                    @elseif($record->overtime_type === 'holiday_work') bg-red-100 text-red-800
                                                    @elseif($record->overtime_type === 'night_shift') bg-indigo-100 text-indigo-800
                                                    @elseif($record->overtime_type === 'compensatory') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    @switch($record->overtime_type)
                                                        @case('daily_overtime')
                                                            Hora Extra Diária
                                                        @break

                                                        @case('weekend_work')
                                                            Fim de Semana
                                                        @break

                                                        @case('holiday_work')
                                                            Feriado
                                                        @break

                                                        @case('night_shift')
                                                            Adicional Noturno
                                                        @break

                                                        @case('compensatory')
                                                            Banco de Horas
                                                        @break

                                                        @case('emergency')
                                                            Emergência
                                                        @break

                                                        @default
                                                            {{ $record->overtime_type }}
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($record->total_minutes / 60, 1) }}h</div>
                                                @if ($record->night_shift_applicable && $record->night_shift_minutes > 0)
                                                    <div class="text-xs text-gray-500">
                                                        {{ number_format($record->night_shift_minutes / 60, 1) }}h noturno
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">R$
                                                    {{ number_format($record->calculated_amount, 2, ',', '.') }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ number_format($record->overtime_multiplier, 2) }}x</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($record->status === 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Aprovado
                                                    </span>
                                                @elseif($record->status === 'pending')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pendente
                                                    </span>
                                                @elseif($record->status === 'paid')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Pago
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Rejeitado
                                                    </span>
                                                @endif

                                                @if ($record->compensatory_time)
                                                    <div class="mt-1">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            Banco de Horas
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('overtime.show', $record) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </a>

                                                    @can('gerenciar_horas_extras')
                                                        @if ($record->status === 'pending')
                                                            <a href="{{ route('overtime.edit', $record) }}"
                                                                class="text-indigo-600 hover:text-indigo-900">
                                                                Editar
                                                            </a>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $overtime->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma hora extra encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if (request()->hasAny(['employee_id', 'overtime_type', 'status', 'start_date', 'end_date']))
                                    Tente ajustar os filtros de busca.
                                @else
                                    As horas extras aparecerão aqui.
                                @endif
                            </p>
                            @can('solicitar_horas_extras')
                                <div class="mt-6">
                                    <a href="{{ route('overtime.create') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Solicitar Hora Extra
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
