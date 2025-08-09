<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Registros de Ponto') }}
            </h2>
            @can('gerenciar_registros_ponto')
                <a href="{{ route('time-records.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Novo Registro
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('time-records.index') }}" class="grid grid-cols-1 {{ $employees->count() > 0 ? 'md:grid-cols-6' : 'md:grid-cols-5' }} gap-4">
                        @if($employees->count() > 0)
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
                        @endif

                        <div>
                            <label for="time_clock_id" class="block text-sm font-medium text-gray-700">Relógio</label>
                            <select name="time_clock_id" id="time_clock_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os relógios</option>
                                @foreach($timeClocks as $timeClock)
                                    <option value="{{ $timeClock->id }}" {{ request('time_clock_id') == $timeClock->id ? 'selected' : '' }}>
                                        {{ $timeClock->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Data Inicial</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Data Final</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="record_type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="record_type" id="record_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os tipos</option>
                                <option value="entry" {{ request('record_type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                                <option value="exit" {{ request('record_type') == 'exit' ? 'selected' : '' }}>Saída</option>
                                <option value="meal_start" {{ request('record_type') == 'meal_start' ? 'selected' : '' }}>Saída Almoço</option>
                                <option value="meal_end" {{ request('record_type') == 'meal_end' ? 'selected' : '' }}>Retorno Almoço</option>
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Válidos</option>
                                <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pendentes</option>
                                <option value="invalid" {{ request('status') == 'invalid' ? 'selected' : '' }}>Inválidos</option>
                            </select>
                        </div>

                        <div class="{{ $employees->count() > 0 ? 'md:col-span-6' : 'md:col-span-5' }} flex justify-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                            <a href="{{ route('time-records.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Registros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($timeRecords->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Funcionário
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data/Hora
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Método
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Relógio
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NSR
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
                                    @foreach($timeRecords as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-xs font-medium text-gray-700">
                                                                {{ substr($record->employee->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
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
                                                <div class="text-sm text-gray-900">{{ $record->record_date->format('d/m/Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $record->record_time }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($record->record_type === 'entry') bg-green-100 text-green-800
                                                    @elseif($record->record_type === 'exit') bg-red-100 text-red-800
                                                    @else bg-blue-100 text-blue-800 @endif">
                                                    @switch($record->record_type)
                                                        @case('entry') Entrada @break
                                                        @case('exit') Saída @break
                                                        @case('meal_start') Saída Almoço @break
                                                        @case('meal_end') Retorno Almoço @break
                                                        @default {{ $record->record_type }}
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @switch($record->identification_method)
                                                    @case('biometric') 
                                                        <span class="flex items-center">
                                                            <svg class="h-4 w-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M6.625 2.655A9 9 0 0119 11a1 1 0 11-2 0 7 7 0 00-9.625-6.492 1 1 0 11-.75-1.853zM4.662 4.959A1 1 0 014.75 6.37 6.97 6.97 0 003 11a1 1 0 11-2 0 8.97 8.97 0 012.25-5.953 1 1 0 011.412-.088z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Biometria
                                                        </span>
                                                        @break
                                                    @case('rfid') 
                                                        <span class="flex items-center">
                                                            <svg class="h-4 w-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                                            </svg>
                                                            Cartão RFID
                                                        </span>
                                                        @break
                                                    @case('pin') PIN @break
                                                    @case('facial') Facial @break
                                                    @case('manual') 
                                                        <span class="text-orange-600">Manual</span>
                                                        @break
                                                    @default {{ $record->identification_method }}
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $record->timeClock->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                                {{ $record->nsr }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->status === 'valid')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Válido
                                                    </span>
                                                @elseif($record->status === 'pending_approval')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pendente
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Inválido
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('time-records.show', $record) }}" class="text-blue-600 hover:text-blue-900">
                                                        Ver
                                                    </a>
                                                    
                                                    @can('aprovar_registros_ponto')
                                                        @if($record->status === 'pending_approval')
                                                            <form method="POST" action="{{ route('time-records.approve', $record) }}" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="text-green-600 hover:text-green-900" 
                                                                        onclick="return confirm('Aprovar este registro?')">
                                                                    Aprovar
                                                                </button>
                                                            </form>
                                                            
                                                            <button type="button" class="text-red-600 hover:text-red-900" 
                                                                    onclick="openRejectModal({{ $record->id }})">
                                                                Rejeitar
                                                            </button>
                                                        @endif
                                                    @endcan
                                                    
                                                    @can('gerenciar_registros_ponto')
                                                        <a href="{{ route('time-records.edit', $record) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            Editar
                                                        </a>
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
                            {{ $timeRecords->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum registro encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->hasAny(['employee_id', 'time_clock_id', 'date_from', 'date_to', 'record_type', 'status']))
                                    Tente ajustar os filtros de busca.
                                @else
                                    Os registros de ponto aparecerão aqui.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rejeição -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Registro</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Motivo da Rejeição</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Descreva o motivo da rejeição..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Rejeitar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(recordId) {
            document.getElementById('rejectForm').action = `/time-records/${recordId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }
    </script>
</x-app-layout>