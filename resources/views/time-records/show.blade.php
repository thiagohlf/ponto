<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registro de Ponto - NSR: {{ $timeRecord->nsr }}
            </h2>
            <div class="flex space-x-2">
                @can('gerenciar_registros_ponto')
                    <a href="{{ route('time-records.edit', $timeRecord) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </a>
                @endcan
                <a href="{{ route('time-records.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Status do Registro -->
            <div class="mb-6">
                @if($timeRecord->status === 'valid')
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Registro Válido</h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>Este registro foi validado e está em conformidade com a legislação trabalhista.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($timeRecord->status === 'pending_approval')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Pendente de Aprovação</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Este registro está aguardando aprovação de um supervisor.</p>
                                </div>
                                @can('aprovar_registros_ponto')
                                    <div class="mt-4 flex space-x-2">
                                        <form method="POST" action="{{ route('time-records.approve', $timeRecord) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm" 
                                                    onclick="return confirm('Aprovar este registro?')">
                                                Aprovar
                                            </button>
                                        </form>
                                        
                                        <button type="button" onclick="openRejectModal()" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Rejeitar
                                        </button>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Registro Inválido</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Este registro foi marcado como inválido.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações Principais -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Registro</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Funcionário</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->employee->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $timeRecord->employee->registration_number }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->record_date->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $timeRecord->record_date->format('l') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hora</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono text-lg">{{ $timeRecord->record_time }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipo de Marcação</label>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($timeRecord->record_type === 'entry') bg-green-100 text-green-800
                                        @elseif($timeRecord->record_type === 'exit') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        @switch($timeRecord->record_type)
                                            @case('entry') Entrada @break
                                            @case('exit') Saída @break
                                            @case('meal_start') Saída para Almoço @break
                                            @case('meal_end') Retorno do Almoço @break
                                            @case('break_start') Início de Pausa @break
                                            @case('break_end') Fim de Pausa @break
                                            @default {{ $timeRecord->record_type }}
                                        @endswitch
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Método de Identificação</label>
                                    <div class="mt-1 flex items-center">
                                        @switch($timeRecord->identification_method)
                                            @case('biometric')
                                                <svg class="h-4 w-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.625 2.655A9 9 0 0119 11a1 1 0 11-2 0 7 7 0 00-9.625-6.492 1 1 0 11-.75-1.853zM4.662 4.959A1 1 0 014.75 6.37 6.97 6.97 0 003 11a1 1 0 11-2 0 8.97 8.97 0 012.25-5.953 1 1 0 011.412-.088z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-900">Biometria</span>
                                                @break
                                            @case('rfid')
                                                <svg class="h-4 w-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-900">Cartão RFID</span>
                                                @break
                                            @case('pin')
                                                <span class="text-sm text-gray-900">PIN/Senha</span>
                                                @break
                                            @case('facial')
                                                <span class="text-sm text-gray-900">Reconhecimento Facial</span>
                                                @break
                                            @case('manual')
                                                <span class="text-sm text-orange-600 font-medium">Manual</span>
                                                @break
                                            @default
                                                <span class="text-sm text-gray-900">{{ $timeRecord->identification_method }}</span>
                                        @endswitch
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Relógio de Ponto</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->timeClock->name ?? 'N/A' }}</p>
                                    @if($timeRecord->timeClock)
                                        <p class="text-xs text-gray-500">{{ $timeRecord->timeClock->location }}</p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NSR</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $timeRecord->nsr }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    @if($timeRecord->status === 'valid')
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Válido
                                        </span>
                                    @elseif($timeRecord->status === 'pending_approval')
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pendente
                                        </span>
                                    @else
                                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inválido
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($timeRecord->observations)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700">Observações</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->observations }}</p>
                                </div>
                            @endif

                            @if($timeRecord->attachments && count($timeRecord->attachments) > 0)
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Documentos Anexados</label>
                                    <div class="space-y-2">
                                        @foreach($timeRecord->attachments as $attachment)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        @if(str_contains($attachment['mime_type'], 'pdf'))
                                                            <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                            </svg>
                                                        @elseif(str_contains($attachment['mime_type'], 'image'))
                                                            <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $attachment['original_name'] }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ number_format($attachment['size'] / 1024, 1) }} KB • 
                                                            Enviado em {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('d/m/Y H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="{{ route('time-records.attachment.download', [$timeRecord, $attachment['filename']]) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Baixar
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Localização -->
                    @if($timeRecord->latitude && $timeRecord->longitude)
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Localização</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Latitude</label>
                                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $timeRecord->latitude }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Longitude</label>
                                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $timeRecord->longitude }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="https://www.google.com/maps?q={{ $timeRecord->latitude }},{{ $timeRecord->longitude }}" 
                                       target="_blank" 
                                       class="inline-flex items-center px-3 py-2 border border-green-700 shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-green-800 hover:bg-green-900">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Ver no Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar com Auditoria -->
                <div class="space-y-6">
                    <!-- Auditoria -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Auditoria</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Criado em</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>

                                @if($timeRecord->updated_at != $timeRecord->created_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Última atualização</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif

                                @if($timeRecord->original_datetime)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Data/Hora Original</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->original_datetime->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                @endif

                                @if($timeRecord->changed_by)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Alterado por</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $timeRecord->changedBy->name }}</p>
                                        @if($timeRecord->changed_at)
                                            <p class="text-xs text-gray-500">{{ $timeRecord->changed_at->format('d/m/Y H:i:s') }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if($timeRecord->hash_verification)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Hash de Verificação</label>
                                        <p class="mt-1 text-xs text-gray-900 font-mono break-all">{{ substr($timeRecord->hash_verification, 0, 32) }}...</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Justificativas -->
                    @if($timeRecord->change_justification)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Justificativa</h3>
                                <p class="text-sm text-gray-900">{{ $timeRecord->change_justification }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Rejeição -->
    @can('aprovar_registros_ponto')
        <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Registro</h3>
                    <form method="POST" action="{{ route('time-records.reject', $timeRecord) }}">
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
            function openRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function closeRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
                document.getElementById('rejection_reason').value = '';
            }
        </script>
    @endcan
</x-app-layout>