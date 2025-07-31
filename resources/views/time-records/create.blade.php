<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Novo Registro de Ponto
            </h2>
            <a href="{{ route('time-records.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Atenção - Registro Manual</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Este registro será marcado como "manual" e ficará pendente de aprovação conforme a legislação trabalhista brasileira.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('time-records.store') }}" class="space-y-6">
                        @csrf

                        <!-- Informações do Funcionário -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Registro</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Funcionário *</label>
                                    <select name="employee_id" id="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('employee_id') border-red-300 @enderror">
                                        <option value="">Selecione um funcionário...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }} - {{ $employee->registration_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="time_clock_id" class="block text-sm font-medium text-gray-700">Relógio de Ponto</label>
                                    <select name="time_clock_id" id="time_clock_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('time_clock_id') border-red-300 @enderror">
                                        <option value="">Selecione um relógio...</option>
                                        @foreach($timeClocks as $timeClock)
                                            <option value="{{ $timeClock->id }}" {{ old('time_clock_id') == $timeClock->id ? 'selected' : '' }}>
                                                {{ $timeClock->name }} - {{ $timeClock->location }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('time_clock_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_date" class="block text-sm font-medium text-gray-700">Data *</label>
                                    <input type="date" name="record_date" id="record_date" value="{{ old('record_date', date('Y-m-d')) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_date') border-red-300 @enderror">
                                    @error('record_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_time" class="block text-sm font-medium text-gray-700">Hora *</label>
                                    <input type="time" name="record_time" id="record_time" value="{{ old('record_time', date('H:i')) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_time') border-red-300 @enderror">
                                    @error('record_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="record_type" class="block text-sm font-medium text-gray-700">Tipo de Marcação *</label>
                                    <select name="record_type" id="record_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('record_type') border-red-300 @enderror">
                                        <option value="">Selecione o tipo...</option>
                                        <option value="entry" {{ old('record_type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                                        <option value="exit" {{ old('record_type') == 'exit' ? 'selected' : '' }}>Saída</option>
                                        <option value="meal_start" {{ old('record_type') == 'meal_start' ? 'selected' : '' }}>Saída para Almoço</option>
                                        <option value="meal_end" {{ old('record_type') == 'meal_end' ? 'selected' : '' }}>Retorno do Almoço</option>
                                        <option value="break_start" {{ old('record_type') == 'break_start' ? 'selected' : '' }}>Início de Pausa</option>
                                        <option value="break_end" {{ old('record_type') == 'break_end' ? 'selected' : '' }}>Fim de Pausa</option>
                                    </select>
                                    @error('record_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="identification_method" class="block text-sm font-medium text-gray-700">Método de Identificação *</label>
                                    <select name="identification_method" id="identification_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('identification_method') border-red-300 @enderror">
                                        <option value="">Selecione o método...</option>
                                        <option value="biometric" {{ old('identification_method') == 'biometric' ? 'selected' : '' }}>Biometria</option>
                                        <option value="rfid" {{ old('identification_method') == 'rfid' ? 'selected' : '' }}>Cartão RFID</option>
                                        <option value="pin" {{ old('identification_method') == 'pin' ? 'selected' : '' }}>PIN/Senha</option>
                                        <option value="facial" {{ old('identification_method') == 'facial' ? 'selected' : '' }}>Reconhecimento Facial</option>
                                        <option value="manual" {{ old('identification_method', 'manual') == 'manual' ? 'selected' : '' }}>Manual</option>
                                    </select>
                                    @error('identification_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Localização (Opcional) -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Localização (Opcional)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <input type="number" name="latitude" id="latitude" value="{{ old('latitude') }}" step="0.00000001" min="-90" max="90"
                                           placeholder="-23.5505199"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('latitude') border-red-300 @enderror">
                                    @error('latitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <input type="number" name="longitude" id="longitude" value="{{ old('longitude') }}" step="0.00000001" min="-180" max="180"
                                           placeholder="-46.6333094"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('longitude') border-red-300 @enderror">
                                    @error('longitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <button type="button" id="getLocation" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Obter Localização Atual
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Observações e Justificativa -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Observações e Justificativa</h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="observations" class="block text-sm font-medium text-gray-700">Observações</label>
                                    <textarea name="observations" id="observations" rows="3" 
                                              placeholder="Observações sobre este registro..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('observations') border-red-300 @enderror">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="change_justification" class="block text-sm font-medium text-gray-700">Justificativa para Registro Manual *</label>
                                    <textarea name="change_justification" id="change_justification" rows="3" required
                                              placeholder="Descreva o motivo pelo qual este registro está sendo feito manualmente..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('change_justification') border-red-300 @enderror">{{ old('change_justification') }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Esta justificativa é obrigatória para registros manuais conforme a legislação trabalhista.</p>
                                    @error('change_justification')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('time-records.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Criar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para obter localização atual
        document.getElementById('getLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    alert('Erro ao obter localização: ' + error.message);
                });
            } else {
                alert('Geolocalização não é suportada por este navegador.');
            }
        });

        // Auto-preencher data e hora atuais
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);
            
            if (!document.getElementById('record_date').value) {
                document.getElementById('record_date').value = today;
            }
            if (!document.getElementById('record_time').value) {
                document.getElementById('record_time').value = currentTime;
            }
        });
    </script>
</x-app-layout>