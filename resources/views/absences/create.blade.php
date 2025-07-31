<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Solicitar Ausência
            </h2>
            <a href="{{ route('absences.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('absences.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Informações Básicas -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Ausência</h3>
                            
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
                                    <label for="absence_type" class="block text-sm font-medium text-gray-700">Tipo de Ausência *</label>
                                    <select name="absence_type" id="absence_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('absence_type') border-red-300 @enderror">
                                        <option value="">Selecione o tipo...</option>
                                        <option value="sick_leave" {{ old('absence_type') == 'sick_leave' ? 'selected' : '' }}>Atestado Médico</option>
                                        <option value="vacation" {{ old('absence_type') == 'vacation' ? 'selected' : '' }}>Férias</option>
                                        <option value="maternity_leave" {{ old('absence_type') == 'maternity_leave' ? 'selected' : '' }}>Licença Maternidade</option>
                                        <option value="paternity_leave" {{ old('absence_type') == 'paternity_leave' ? 'selected' : '' }}>Licença Paternidade</option>
                                        <option value="bereavement" {{ old('absence_type') == 'bereavement' ? 'selected' : '' }}>Luto (Art. 473, I CLT)</option>
                                        <option value="marriage" {{ old('absence_type') == 'marriage' ? 'selected' : '' }}>Casamento (Art. 473, II CLT)</option>
                                        <option value="blood_donation" {{ old('absence_type') == 'blood_donation' ? 'selected' : '' }}>Doação de Sangue (Art. 473, IV CLT)</option>
                                        <option value="military_service" {{ old('absence_type') == 'military_service' ? 'selected' : '' }}>Serviço Militar</option>
                                        <option value="jury_duty" {{ old('absence_type') == 'jury_duty' ? 'selected' : '' }}>Júri (Art. 473, VII CLT)</option>
                                        <option value="witness_testimony" {{ old('absence_type') == 'witness_testimony' ? 'selected' : '' }}>Testemunha (Art. 473, VI CLT)</option>
                                        <option value="union_activity" {{ old('absence_type') == 'union_activity' ? 'selected' : '' }}>Atividade Sindical</option>
                                        <option value="study_leave" {{ old('absence_type') == 'study_leave' ? 'selected' : '' }}>Licença para Estudos</option>
                                        <option value="unpaid_leave" {{ old('absence_type') == 'unpaid_leave' ? 'selected' : '' }}>Licença sem Vencimento</option>
                                        <option value="unjustified" {{ old('absence_type') == 'unjustified' ? 'selected' : '' }}>Falta Injustificada</option>
                                        <option value="other" {{ old('absence_type') == 'other' ? 'selected' : '' }}>Outros Motivos</option>
                                    </select>
                                    @error('absence_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início *</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('start_date') border-red-300 @enderror">
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Data de Fim *</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('end_date') border-red-300 @enderror">
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="total_days" class="block text-sm font-medium text-gray-700">Total de Dias</label>
                                    <input type="number" name="total_days" id="total_days" value="{{ old('total_days', 1) }}" min="1" readonly
                                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="mt-1 text-sm text-gray-500">Calculado automaticamente</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="justification" class="block text-sm font-medium text-gray-700">Justificativa *</label>
                                    <textarea name="justification" id="justification" rows="3" required
                                              placeholder="Descreva o motivo da ausência..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('justification') border-red-300 @enderror">{{ old('justification') }}</textarea>
                                    @error('justification')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Documentação -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Documentação Comprobatória</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="document_number" class="block text-sm font-medium text-gray-700">Número do Documento</label>
                                    <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}"
                                           placeholder="Ex: Número do atestado médico"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('document_number') border-red-300 @enderror">
                                    @error('document_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="document_path" class="block text-sm font-medium text-gray-700">Anexar Documento</label>
                                    <input type="file" name="document_path" id="document_path" accept=".pdf,.jpg,.jpeg,.png"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-sm text-gray-500">PDF, JPG, JPEG ou PNG até 5MB</p>
                                    @error('document_path')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="document_date" class="block text-sm font-medium text-gray-700">Data do Documento</label>
                                    <input type="date" name="document_date" id="document_date" value="{{ old('document_date') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('document_date') border-red-300 @enderror">
                                    @error('document_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações Médicas (se aplicável) -->
                        <div class="border-b border-gray-200 pb-6" id="medical_info" style="display: none;">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Médicas</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="doctor_name" class="block text-sm font-medium text-gray-700">Nome do Médico</label>
                                    <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('doctor_name') border-red-300 @enderror">
                                    @error('doctor_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="doctor_crm" class="block text-sm font-medium text-gray-700">CRM do Médico</label>
                                    <input type="text" name="doctor_crm" id="doctor_crm" value="{{ old('doctor_crm') }}"
                                           placeholder="Ex: CRM/SP 123456"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('doctor_crm') border-red-300 @enderror">
                                    @error('doctor_crm')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="medical_code" class="block text-sm font-medium text-gray-700">Código CID</label>
                                    <input type="text" name="medical_code" id="medical_code" value="{{ old('medical_code') }}"
                                           placeholder="Ex: M54.5"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('medical_code') border-red-300 @enderror">
                                    @error('medical_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configurações Financeiras -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="paid_absence" id="paid_absence" value="1" {{ old('paid_absence', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="paid_absence" class="ml-2 block text-sm text-gray-900">
                                        Ausência remunerada
                                    </label>
                                </div>

                                <div>
                                    <label for="discount_amount" class="block text-sm font-medium text-gray-700">Valor do Desconto (R$)</label>
                                    <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('discount_amount') border-red-300 @enderror">
                                    <p class="mt-1 text-sm text-gray-500">Deixe 0 se não houver desconto</p>
                                    @error('discount_amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('absences.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Solicitar Ausência
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calcular total de dias automaticamente
        function calculateDays() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('total_days').value = diffDays;
            }
        }

        document.getElementById('start_date').addEventListener('change', calculateDays);
        document.getElementById('end_date').addEventListener('change', calculateDays);

        // Mostrar/ocultar informações médicas
        document.getElementById('absence_type').addEventListener('change', function() {
            const medicalInfo = document.getElementById('medical_info');
            if (this.value === 'sick_leave') {
                medicalInfo.style.display = 'block';
            } else {
                medicalInfo.style.display = 'none';
            }
        });

        // Configurar ausência remunerada baseada no tipo
        document.getElementById('absence_type').addEventListener('change', function() {
            const paidCheckbox = document.getElementById('paid_absence');
            const discountField = document.getElementById('discount_amount');
            
            // Tipos que geralmente são remunerados
            const paidTypes = ['sick_leave', 'vacation', 'maternity_leave', 'paternity_leave', 'bereavement', 'marriage', 'blood_donation', 'jury_duty', 'witness_testimony'];
            
            if (paidTypes.includes(this.value)) {
                paidCheckbox.checked = true;
                discountField.value = 0;
            } else if (this.value === 'unjustified') {
                paidCheckbox.checked = false;
            }
        });
    </script>
</x-app-layout>