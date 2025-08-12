@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Funcionário') }}
        </h2>
        <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                        @csrf

                        <!-- Informações Básicas -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo
                                        *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" required
                                        placeholder="000.000.000-00" maxlength="14"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('cpf') border-red-300 @enderror">
                                    @error('cpf')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rg" class="block text-sm font-medium text-gray-700">RG</label>
                                    <input type="text" name="rg" id="rg" value="{{ old('rg') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('rg') border-red-300 @enderror">
                                    @error('rg')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700">Data de
                                        Nascimento</label>
                                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('birth_date') border-red-300 @enderror">
                                    @error('birth_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gênero</label>
                                    <select name="gender" id="gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('gender') border-red-300 @enderror">
                                        <option value="">Selecione...</option>
                                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino
                                        </option>
                                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino
                                        </option>
                                        <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-300 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        placeholder="(00) 00000-0000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-300 @enderror">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações Trabalhistas -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Trabalhistas</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700">Empresa
                                        *</label>
                                    <select name="company_id" id="company_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('company_id') border-red-300 @enderror">
                                        <option value="">Selecione uma empresa...</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department_id"
                                        class="block text-sm font-medium text-gray-700">Departamento</label>
                                    <select name="department_id" id="department_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department_id') border-red-300 @enderror">
                                        <option value="">Selecione um departamento...</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="registration_number"
                                        class="block text-sm font-medium text-gray-700">Matrícula *</label>
                                    <input type="text" name="registration_number" id="registration_number"
                                        value="{{ old('registration_number') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('registration_number') border-red-300 @enderror">
                                    @error('registration_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="pis_pasep"
                                        class="block text-sm font-medium text-gray-700">PIS/PASEP</label>
                                    <input type="text" name="pis_pasep" id="pis_pasep"
                                        value="{{ old('pis_pasep') }}" placeholder="000.00000.00-0" maxlength="14"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('pis_pasep') border-red-300 @enderror">
                                    @error('pis_pasep')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="admission_date" class="block text-sm font-medium text-gray-700">Data de
                                        Admissão *</label>
                                    <input type="date" name="admission_date" id="admission_date"
                                        value="{{ old('admission_date') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('admission_date') border-red-300 @enderror">
                                    @error('admission_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700">Cargo *</label>
                                    <input type="text" name="position" id="position" value="{{ old('position') }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('position') border-red-300 @enderror">
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="salary" class="block text-sm font-medium text-gray-700">Salário</label>
                                    <input type="number" name="salary" id="salary" value="{{ old('salary') }}"
                                        step="0.01" min="0" placeholder="0.00"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('salary') border-red-300 @enderror">
                                    @error('salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configurações de Ponto -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações de Ponto</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="weekly_hours" class="block text-sm font-medium text-gray-700">Carga
                                        Horária Semanal *</label>
                                    <input type="number" name="weekly_hours" id="weekly_hours"
                                        value="{{ old('weekly_hours', 44) }}" required min="1" max="44"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('weekly_hours') border-red-300 @enderror">
                                    @error('weekly_hours')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="meal_break_minutes"
                                        class="block text-sm font-medium text-gray-700">Intervalo para Refeição (minutos)
                                        *</label>
                                    <input type="number" name="meal_break_minutes" id="meal_break_minutes"
                                        value="{{ old('meal_break_minutes', 60) }}" required min="30"
                                        max="120"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('meal_break_minutes') border-red-300 @enderror">
                                    @error('meal_break_minutes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="rfid_card" class="block text-sm font-medium text-gray-700">Cartão
                                        RFID</label>
                                    <input type="text" name="rfid_card" id="rfid_card"
                                        value="{{ old('rfid_card') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('rfid_card') border-red-300 @enderror">
                                    @error('rfid_card')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="exempt_time_control" id="exempt_time_control"
                                            value="1" {{ old('exempt_time_control') ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="exempt_time_control" class="ml-2 block text-sm text-gray-900">
                                            Isento de controle de ponto
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="has_meal_break" id="has_meal_break" value="1"
                                            {{ old('has_meal_break', true) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="has_meal_break" class="ml-2 block text-sm text-gray-900">
                                            Possui intervalo para refeição
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-300 @enderror">
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="number" class="block text-sm font-medium text-gray-700">Número</label>
                                    <input type="text" name="number" id="number" value="{{ old('number') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('number') border-red-300 @enderror">
                                    @error('number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="complement"
                                        class="block text-sm font-medium text-gray-700">Complemento</label>
                                    <input type="text" name="complement" id="complement"
                                        value="{{ old('complement') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('complement') border-red-300 @enderror">
                                    @error('complement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="neighborhood"
                                        class="block text-sm font-medium text-gray-700">Bairro</label>
                                    <input type="text" name="neighborhood" id="neighborhood"
                                        value="{{ old('neighborhood') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('neighborhood') border-red-300 @enderror">
                                    @error('neighborhood')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">Cidade</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('city') border-red-300 @enderror">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">Estado</label>
                                    <select name="state" id="state"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state') border-red-300 @enderror">
                                        <option value="">Selecione...</option>
                                        <option value="AC" {{ old('state') == 'AC' ? 'selected' : '' }}>Acre</option>
                                        <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alagoas
                                        </option>
                                        <option value="AP" {{ old('state') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                        <option value="AM" {{ old('state') == 'AM' ? 'selected' : '' }}>Amazonas
                                        </option>
                                        <option value="BA" {{ old('state') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                        <option value="CE" {{ old('state') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                        <option value="DF" {{ old('state') == 'DF' ? 'selected' : '' }}>Distrito
                                            Federal</option>
                                        <option value="ES" {{ old('state') == 'ES' ? 'selected' : '' }}>Espírito Santo
                                        </option>
                                        <option value="GO" {{ old('state') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                        <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Maranhão
                                        </option>
                                        <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Mato Grosso
                                        </option>
                                        <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mato Grosso do
                                            Sul</option>
                                        <option value="MG" {{ old('state') == 'MG' ? 'selected' : '' }}>Minas Gerais
                                        </option>
                                        <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pará</option>
                                        <option value="PB" {{ old('state') == 'PB' ? 'selected' : '' }}>Paraíba
                                        </option>
                                        <option value="PR" {{ old('state') == 'PR' ? 'selected' : '' }}>Paraná
                                        </option>
                                        <option value="PE" {{ old('state') == 'PE' ? 'selected' : '' }}>Pernambuco
                                        </option>
                                        <option value="PI" {{ old('state') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                        <option value="RJ" {{ old('state') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro
                                        </option>
                                        <option value="RN" {{ old('state') == 'RN' ? 'selected' : '' }}>Rio Grande do
                                            Norte</option>
                                        <option value="RS" {{ old('state') == 'RS' ? 'selected' : '' }}>Rio Grande do
                                            Sul</option>
                                        <option value="RO" {{ old('state') == 'RO' ? 'selected' : '' }}>Rondônia
                                        </option>
                                        <option value="RR" {{ old('state') == 'RR' ? 'selected' : '' }}>Roraima
                                        </option>
                                        <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>Santa Catarina
                                        </option>
                                        <option value="SP" {{ old('state') == 'SP' ? 'selected' : '' }}>São Paulo
                                        </option>
                                        <option value="SE" {{ old('state') == 'SE' ? 'selected' : '' }}>Sergipe
                                        </option>
                                        <option value="TO" {{ old('state') == 'TO' ? 'selected' : '' }}>Tocantins
                                        </option>
                                    </select>
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="zip_code" class="block text-sm font-medium text-gray-700">CEP</label>
                                    <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                                        placeholder="00000-000" maxlength="9"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('zip_code') border-red-300 @enderror">
                                    @error('zip_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('employees.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar Funcionário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Máscaras para campos
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        document.getElementById('pis_pasep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{5})(\d)/, '$1.$2');
            value = value.replace(/(\d{2})(\d{1})$/, '$1-$2');
            e.target.value = value;
        });

        document.getElementById('zip_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    </script>
@endsection
