<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(auth()->user()->isSupervisor() || auth()->user()->isHR() || auth()->user()->isAdmin())
                    Novo Registro de Ponto
                @else
                    Solicitar Ajuste de Ponto
                @endif
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
                                <h3 class="text-sm font-medium text-yellow-800">
                                    @if(auth()->user()->isSupervisor() || auth()->user()->isHR() || auth()->user()->isAdmin())
                                        Atenção - Registro Manual
                                    @else
                                        Atenção - Solicitação de Ajuste
                                    @endif
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    @if(auth()->user()->isSupervisor() || auth()->user()->isHR() || auth()->user()->isAdmin())
                                        <p>Este registro será marcado como "manual" e ficará pendente de aprovação conforme a legislação trabalhista brasileira.</p>
                                    @else
                                        <p>Esta solicitação de ajuste de ponto será enviada para aprovação do seu supervisor conforme a legislação trabalhista brasileira.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('time-records.store') }}" class="space-y-6" enctype="multipart/form-data">
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
                                    <button type="button" id="getLocation" class="inline-flex items-center px-3 py-2 border border-green-700 shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Obter Localização Atual
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Justificativa e Anexos -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Justificativa e Documentos</h3>
                            
                            <div class="space-y-6">
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

                                <!-- Anexos -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Anexar Documentos</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="text-sm text-gray-600">
                                                <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Clique para anexar documentos</span>
                                                    <input id="attachments" name="attachments[]" type="file" class="sr-only" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                </label>
                                                <p class="pl-1">ou arraste e solte aqui</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PDF, JPG, PNG, DOC, DOCX até 5MB cada
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Lista de arquivos selecionados -->
                                    <div id="file-list" class="mt-4 space-y-2 hidden">
                                        <h4 class="text-sm font-medium text-gray-700">Arquivos selecionados:</h4>
                                        <div id="selected-files" class="space-y-1"></div>
                                    </div>
                                    
                                    @error('attachments.*')
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
                                @if(auth()->user()->isSupervisor() || auth()->user()->isHR() || auth()->user()->isAdmin())
                                    Criar Registro
                                @else
                                    Solicitar Ajuste
                                @endif
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

            // Gerenciamento de upload de arquivos
            const fileInput = document.getElementById('attachments');
            const fileList = document.getElementById('file-list');
            const selectedFiles = document.getElementById('selected-files');
            const dropZone = fileInput.closest('.border-dashed');

            // Função para formatar tamanho do arquivo
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Função para validar arquivo
            function validateFile(file) {
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (!allowedTypes.includes(file.type)) {
                    return 'Tipo de arquivo não permitido. Use: PDF, JPG, PNG, DOC, DOCX';
                }

                if (file.size > maxSize) {
                    return 'Arquivo muito grande. Máximo 5MB por arquivo.';
                }

                return null;
            }

            // Função para exibir arquivos selecionados
            function displaySelectedFiles(files) {
                selectedFiles.innerHTML = '';
                
                if (files.length === 0) {
                    fileList.classList.add('hidden');
                    return;
                }

                fileList.classList.remove('hidden');

                Array.from(files).forEach((file, index) => {
                    const error = validateFile(file);
                    
                    const fileItem = document.createElement('div');
                    fileItem.className = `flex items-center justify-between p-2 bg-gray-50 rounded border ${error ? 'border-red-300' : 'border-gray-200'}`;
                    
                    fileItem.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5 ${error ? 'text-red-500' : 'text-gray-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium ${error ? 'text-red-700' : 'text-gray-900'}">${file.name}</p>
                                <p class="text-xs ${error ? 'text-red-500' : 'text-gray-500'}">${error || formatFileSize(file.size)}</p>
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="removeFile(${index})">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    
                    selectedFiles.appendChild(fileItem);
                });
            }

            // Função para remover arquivo
            window.removeFile = function(index) {
                const dt = new DataTransfer();
                const files = fileInput.files;
                
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                
                fileInput.files = dt.files;
                displaySelectedFiles(fileInput.files);
            };

            // Event listener para mudança de arquivos
            fileInput.addEventListener('change', function() {
                displaySelectedFiles(this.files);
            });

            // Drag and drop functionality
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-indigo-400', 'bg-indigo-50');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
                
                const files = e.dataTransfer.files;
                fileInput.files = files;
                displaySelectedFiles(files);
            });
        });
    </script>
</x-app-layout>