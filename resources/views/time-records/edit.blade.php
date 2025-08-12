@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Registro de Ponto
        </h2>
        <a href="{{ route('time-records.show', $timeRecord) }}"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Atenção - Alteração de Registro
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Esta alteração será registrada e ficará pendente de aprovação conforme a
                                        legislação trabalhista brasileira.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('time-records.update', $timeRecord) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Informações do Registro Original -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Registro</h3>

                            <div class="bg-gray-50 p-4 rounded-md mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Dados Originais:</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Funcionário:</span>
                                        <p class="font-medium">{{ $timeRecord->employee->user->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Data Original:</span>
                                        <p class="font-medium">{{ $timeRecord->record_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Hora Original:</span>
                                        <p class="font-medium">{{ $timeRecord->record_time->format('H:i') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Tipo:</span>
                                        <p class="font-medium">
                                            @switch($timeRecord->record_type)
                                                @case('entry')
                                                    Entrada
                                                @break

                                                @case('exit')
                                                    Saída
                                                @break

                                                @case('meal_start')
                                                    Saída para Almoço
                                                @break

                                                @case('meal_end')
                                                    Retorno do Almoço
                                                @break

                                                @case('break_start')
                                                    Início de Pausa
                                                @break

                                                @case('break_end')
                                                    Fim de Pausa
                                                @break

                                                @default
                                                    {{ $timeRecord->record_type }}
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="record_date" class="block text-sm font-medium text-gray-700">Nova Data
                                        *</label>
                                    <input type="date" name="record_date" id="record_date"
                                        value="{{ old('record_date', $timeRecord->record_date->format('Y-m-d')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_date') border-red-300 @enderror">
                                    @error('record_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_time" class="block text-sm font-medium text-gray-700">Nova Hora
                                        *</label>
                                    <input type="time" name="record_time" id="record_time"
                                        value="{{ old('record_time', $timeRecord->record_time->format('H:i')) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_time') border-red-300 @enderror">
                                    @error('record_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_type" class="block text-sm font-medium text-gray-700">Tipo de
                                        Marcação *</label>
                                    <select name="record_type" id="record_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_type') border-red-300 @enderror">
                                        <option value="">Selecione o tipo...</option>
                                        <option value="entry"
                                            {{ old('record_type', $timeRecord->record_type) == 'entry' ? 'selected' : '' }}>
                                            Entrada</option>
                                        <option value="exit"
                                            {{ old('record_type', $timeRecord->record_type) == 'exit' ? 'selected' : '' }}>
                                            Saída</option>
                                        <option value="meal_start"
                                            {{ old('record_type', $timeRecord->record_type) == 'meal_start' ? 'selected' : '' }}>
                                            Saída para Almoço</option>
                                        <option value="meal_end"
                                            {{ old('record_type', $timeRecord->record_type) == 'meal_end' ? 'selected' : '' }}>
                                            Retorno do Almoço</option>
                                        <option value="break_start"
                                            {{ old('record_type', $timeRecord->record_type) == 'break_start' ? 'selected' : '' }}>
                                            Início de Pausa</option>
                                        <option value="break_end"
                                            {{ old('record_type', $timeRecord->record_type) == 'break_end' ? 'selected' : '' }}>
                                            Fim de Pausa</option>
                                    </select>
                                    @error('record_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                            </div>
                        </div>

                        <!-- Justificativa para Alteração -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Justificativa para Alteração</h3>

                            <div>
                                <label for="change_justification" class="block text-sm font-medium text-gray-700">Motivo
                                    da Alteração *</label>
                                <textarea name="change_justification" id="change_justification" rows="4" required
                                    placeholder="Descreva detalhadamente o motivo desta alteração no registro de ponto..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('change_justification') border-red-300 @enderror">{{ old('change_justification') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Esta justificativa é obrigatória para alterações
                                    de registros conforme a legislação trabalhista.</p>
                                @error('change_justification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Histórico de Alterações (se houver) -->
                        @if ($timeRecord->change_data)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Alterações</h3>

                                <div class="bg-gray-50 p-4 rounded-md">
                                    <div class="space-y-2 text-sm">
                                        @php
                                            $changeData = is_string($timeRecord->change_data)
                                                ? json_decode($timeRecord->change_data, true)
                                                : $timeRecord->change_data;
                                        @endphp

                                        @if (isset($changeData['original_datetime']))
                                            <p><span class="font-medium">Data/Hora Original:</span>
                                                {{ \Carbon\Carbon::parse($changeData['original_datetime'])->format('d/m/Y H:i') }}
                                            </p>
                                        @endif

                                        @if (isset($changeData['change_justification']))
                                            <p><span class="font-medium">Justificativa Anterior:</span>
                                                {{ $changeData['change_justification'] }}</p>
                                        @endif

                                        @if (isset($changeData['changed_at']))
                                            <p><span class="font-medium">Alterado em:</span>
                                                {{ \Carbon\Carbon::parse($changeData['changed_at'])->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('time-records.show', $timeRecord) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
