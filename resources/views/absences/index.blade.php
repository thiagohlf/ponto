<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ausências e Faltas
            </h2>
            @can('solicitar_ausencias')
                <a href="{{ route('absences.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Solicitar Ausência
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('absences.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700">Funcionário</label>
                            <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os funcionários</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="absence_type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="absence_type" id="absence_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os tipos</option>
                                <option value="sick_leave" {{ request('absence_type') == 'sick_leave' ? 'selected' : '' }}>Atestado Médico</option>
                                <option value="vacation" {{ request('absence_type') == 'vacation' ? 'selected' : '' }}>Férias</option>
                                <option value="maternity_leave" {{ request('absence_type') == 'maternity_leave' ? 'selected' : '' }}>Licença Maternidade</option>
                                <option value="paternity_leave" {{ request('absence_type') == 'paternity_leave' ? 'selected' : '' }}>Licença Paternidade</option>
                                <option value="bereavement" {{ request('absence_type') == 'bereavement' ? 'selected' : '' }}>Luto</option>
                                <option value="marriage" {{ request('absence_type') == 'marriage' ? 'selected' : '' }}>Casamento</option>
                                <option value="unjustified" {{ request('absence_type') == 'unjustified' ? 'selected' : '' }}>Falta Injustificada</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
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
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('absences.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Ausências -->
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
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($absences as $absence)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-xs font-medium text-gray-700">
                                                                {{ substr($absence->employee->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $absence->employee->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $absence->employee->registration_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $absence->start_date->format('d/m/Y') }}
                                                    @if($absence->start_date != $absence->end_date)
                                                        até {{ $absence->end_date->format('d/m/Y') }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($absence->absence_type === 'sick_leave') bg-red-100 text-red-800
                                                    @elseif($absence->absence_type === 'vacation') bg-green-100 text-green-800
                                                    @elseif($absence->absence_type === 'unjustified') bg-gray-100 text-gray-800
                                                    @else bg-blue-100 text-blue-800 @endif">
                                                    @switch($absence->absence_type)
                                                        @case('sick_leave') Atestado Médico @break
                                                        @case('vacation') Férias @break
                                                        @case('maternity_leave') Licença Maternidade @break
                                                        @case('paternity_leave') Licença Paternidade @break
                                                        @case('bereavement') Luto @break
                                                        @case('marriage') Casamento @break
                                                        @case('blood_donation') Doação de Sangue @break
                                                        @case('military_service') Serviço Militar @break
                                                        @case('jury_duty') Júri @break
                                                        @case('witness_testimony') Testemunha @break
                                                        @case('union_activity') Atividade Sindical @break
                                                        @case('study_leave') Licença para Estudos @break
                                                        @case('unpaid_leave') Licença sem Vencimento @break
                                                        @case('unjustified') Falta Injustificada @break
                                                        @default {{ $absence->absence_type }}
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $absence->total_days }} {{ $absence->total_days == 1 ? 'dia' : 'dias' }}
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('absences.show', $absence) }}" class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </a>
                                                    
                                                    @can('gerenciar_ausencias')
                                                        @if($absence->status === 'pending')
                                                            <a href="{{ route('absences.edit', $absence) }}" class="text-indigo-600 hover:text-indigo-900">
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
                            {{ $absences->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma ausência encontrada</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['employee_id', 'absence_type', 'status', 'start_date', 'end_date']))
                                    Tente ajustar os filtros de busca.
                                @else
                                    As ausências e faltas aparecerão aqui.
                                @endif
                            </p>
                            @can('solicitar_ausencias')
                                <div class="mt-6">
                                    <a href="{{ route('absences.create') }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Solicitar Ausência
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