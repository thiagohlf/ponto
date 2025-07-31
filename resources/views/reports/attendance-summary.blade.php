<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Resumo de Frequência
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.attendance-summary') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            <label for="company_id" class="block text-sm font-medium text-gray-700">Empresa</label>
                            <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas as empresas</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3 flex justify-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Gerar Relatório
                            </button>
                            <a href="{{ route('reports.attendance-summary') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if(request()->hasAny(['start_date', 'end_date']) && isset($employees) && isset($attendanceData))
                <!-- Resumo Geral -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Resumo Geral</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $employees->count() }}</div>
                                <div class="text-sm text-gray-500">Total de Funcionários</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ number_format(collect($attendanceData)->avg('attendance_rate'), 1) }}%
                                </div>
                                <div class="text-sm text-gray-500">Frequência Média</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ collect($attendanceData)->sum('late_arrivals') }}
                                </div>
                                <div class="text-sm text-gray-500">Total de Atrasos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">
                                    {{ collect($attendanceData)->sum('absent_days') }}
                                </div>
                                <div class="text-sm text-gray-500">Total de Faltas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório Detalhado -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        @if($employees->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Funcionário
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Departamento
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dias Úteis
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dias Presentes
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dias Ausentes
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Atrasos
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                % Frequência
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($employees as $employee)
                                            @php
                                                $data = $attendanceData[$employee->id] ?? [
                                                    'work_days' => 0,
                                                    'present_days' => 0,
                                                    'absent_days' => 0,
                                                    'late_arrivals' => 0,
                                                    'attendance_rate' => 0
                                                ];
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
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
                                                    {{ $employee->department->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                    {{ $data['work_days'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="text-sm font-medium text-green-600">
                                                        {{ $data['present_days'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="text-sm font-medium text-red-600">
                                                        {{ $data['absent_days'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="text-sm font-medium text-yellow-600">
                                                        {{ $data['late_arrivals'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center">
                                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                                            <div class="h-2 rounded-full 
                                                                @if($data['attendance_rate'] >= 95) bg-green-500
                                                                @elseif($data['attendance_rate'] >= 85) bg-yellow-500
                                                                @else bg-red-500 @endif" 
                                                                style="width: {{ $data['attendance_rate'] }}%"></div>
                                                        </div>
                                                        <span class="text-sm font-medium 
                                                            @if($data['attendance_rate'] >= 95) text-green-600
                                                            @elseif($data['attendance_rate'] >= 85) text-yellow-600
                                                            @else text-red-600 @endif">
                                                            {{ number_format($data['attendance_rate'], 1) }}%
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Legenda -->
                            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Legenda de Frequência:</h4>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-4 h-2 bg-green-500 rounded mr-2"></div>
                                        <span class="text-gray-700">Excelente (≥95%)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-2 bg-yellow-500 rounded mr-2"></div>
                                        <span class="text-gray-700">Boa (85-94%)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-2 bg-red-500 rounded mr-2"></div>
                                        <span class="text-gray-700">Atenção (<85%)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Informações do Relatório -->
                            <div class="mt-6 text-sm text-gray-500">
                                <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
                                <p>Período: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : '' }} até {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : '' }}</p>
                                @if(request('company_id'))
                                    <p>Empresa: {{ $companies->find(request('company_id'))->name ?? 'N/A' }}</p>
                                @endif
                                <p class="mt-2 text-xs">
                                    <strong>Observações:</strong><br>
                                    • Dias úteis: Considera apenas dias de trabalho (segunda a sexta)<br>
                                    • Atrasos: Entrada após 8:10 (considerando 10 min de tolerância)<br>
                                    • Ausências justificadas são contabilizadas separadamente
                                </p>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum funcionário encontrado</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Não foram encontrados funcionários para o período e filtros selecionados.
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Resumo de Frequência</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Selecione o período e os filtros desejados para gerar o relatório de frequência.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>