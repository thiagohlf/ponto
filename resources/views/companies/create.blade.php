@extends('layouts.app')
@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova Empresa
        </h2>
        <a href="{{ route('companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('companies.store') }}" class="space-y-6">
                        @csrf

                        <!-- Informações Básicas -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Empresa</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Razão Social
                                        *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="trade_name" class="block text-sm font-medium text-gray-700">Nome
                                        Fantasia</label>
                                    <input type="text" name="trade_name" id="trade_name" value="{{ old('trade_name') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('trade_name') border-red-300 @enderror">
                                    @error('trade_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ *</label>
                                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}" required
                                        placeholder="00.000.000/0000-00" maxlength="18"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('cnpj') border-red-300 @enderror">
                                    @error('cnpj')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="state_registration"
                                        class="block text-sm font-medium text-gray-700">Inscrição Estadual</label>
                                    <input type="text" name="state_registration" id="state_registration"
                                        value="{{ old('state_registration') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state_registration') border-red-300 @enderror">
                                    @error('state_registration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="municipal_registration"
                                        class="block text-sm font-medium text-gray-700">Inscrição Municipal</label>
                                    <input type="text" name="municipal_registration" id="municipal_registration"
                                        value="{{ old('municipal_registration') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('municipal_registration') border-red-300 @enderror">
                                    @error('municipal_registration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="contact_email" id="contact_email"
                                        value="{{ old('contact_email') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_email') border-red-300 @enderror">
                                    @error('contact_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_phone"
                                        class="block text-sm font-medium text-gray-700">Telefone</label>
                                    <input type="text" name="contact_phone" id="contact_phone"
                                        value="{{ old('contact_phone') }}" placeholder="(00) 0000-0000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('contact_phone') border-red-300 @enderror">
                                    @error('contact_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="address_street" class="block text-sm font-medium text-gray-700">Endereço
                                        *</label>
                                    <input type="text" name="address_street" id="address_street"
                                        value="{{ old('address_street') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_street') border-red-300 @enderror">
                                    @error('address_street')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_number" class="block text-sm font-medium text-gray-700">Número
                                        *</label>
                                    <input type="text" name="address_number" id="address_number"
                                        value="{{ old('address_number') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_number') border-red-300 @enderror">
                                    @error('address_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_complement"
                                        class="block text-sm font-medium text-gray-700">Complemento</label>
                                    <input type="text" name="address_complement" id="address_complement"
                                        value="{{ old('address_complement') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_complement') border-red-300 @enderror">
                                    @error('address_complement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_neighborhood"
                                        class="block text-sm font-medium text-gray-700">Bairro *</label>
                                    <input type="text" name="address_neighborhood" id="address_neighborhood"
                                        value="{{ old('address_neighborhood') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_neighborhood') border-red-300 @enderror">
                                    @error('address_neighborhood')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_city" class="block text-sm font-medium text-gray-700">Cidade
                                        *</label>
                                    <input type="text" name="address_city" id="address_city"
                                        value="{{ old('address_city') }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_city') border-red-300 @enderror">
                                    @error('address_city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_state" class="block text-sm font-medium text-gray-700">Estado
                                        *</label>
                                    <select name="address_state" id="address_state" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_state') border-red-300 @enderror">
                                        <option value="">Selecione...</option>
                                        <option value="AC" {{ old('address_state') == 'AC' ? 'selected' : '' }}>Acre
                                        </option>
                                        <option value="AL" {{ old('address_state') == 'AL' ? 'selected' : '' }}>
                                            Alagoas</option>
                                        <option value="AP" {{ old('address_state') == 'AP' ? 'selected' : '' }}>Amapá
                                        </option>
                                        <option value="AM" {{ old('address_state') == 'AM' ? 'selected' : '' }}>
                                            Amazonas</option>
                                        <option value="BA" {{ old('address_state') == 'BA' ? 'selected' : '' }}>Bahia
                                        </option>
                                        <option value="CE" {{ old('address_state') == 'CE' ? 'selected' : '' }}>Ceará
                                        </option>
                                        <option value="DF" {{ old('address_state') == 'DF' ? 'selected' : '' }}>
                                            Distrito Federal</option>
                                        <option value="ES" {{ old('address_state') == 'ES' ? 'selected' : '' }}>
                                            Espírito Santo</option>
                                        <option value="GO" {{ old('address_state') == 'GO' ? 'selected' : '' }}>Goiás
                                        </option>
                                        <option value="MA" {{ old('address_state') == 'MA' ? 'selected' : '' }}>
                                            Maranhão</option>
                                        <option value="MT" {{ old('address_state') == 'MT' ? 'selected' : '' }}>Mato
                                            Grosso</option>
                                        <option value="MS" {{ old('address_state') == 'MS' ? 'selected' : '' }}>Mato
                                            Grosso do Sul</option>
                                        <option value="MG" {{ old('address_state') == 'MG' ? 'selected' : '' }}>Minas
                                            Gerais</option>
                                        <option value="PA" {{ old('address_state') == 'PA' ? 'selected' : '' }}>Pará
                                        </option>
                                        <option value="PB" {{ old('address_state') == 'PB' ? 'selected' : '' }}>
                                            Paraíba</option>
                                        <option value="PR" {{ old('address_state') == 'PR' ? 'selected' : '' }}>Paraná
                                        </option>
                                        <option value="PE" {{ old('address_state') == 'PE' ? 'selected' : '' }}>
                                            Pernambuco</option>
                                        <option value="PI" {{ old('address_state') == 'PI' ? 'selected' : '' }}>Piauí
                                        </option>
                                        <option value="RJ" {{ old('address_state') == 'RJ' ? 'selected' : '' }}>Rio de
                                            Janeiro</option>
                                        <option value="RN" {{ old('address_state') == 'RN' ? 'selected' : '' }}>Rio
                                            Grande do Norte</option>
                                        <option value="RS" {{ old('address_state') == 'RS' ? 'selected' : '' }}>Rio
                                            Grande do Sul</option>
                                        <option value="RO" {{ old('address_state') == 'RO' ? 'selected' : '' }}>
                                            Rondônia</option>
                                        <option value="RR" {{ old('address_state') == 'RR' ? 'selected' : '' }}>
                                            Roraima</option>
                                        <option value="SC" {{ old('address_state') == 'SC' ? 'selected' : '' }}>Santa
                                            Catarina</option>
                                        <option value="SP" {{ old('address_state') == 'SP' ? 'selected' : '' }}>São
                                            Paulo</option>
                                        <option value="SE" {{ old('address_state') == 'SE' ? 'selected' : '' }}>
                                            Sergipe</option>
                                        <option value="TO" {{ old('address_state') == 'TO' ? 'selected' : '' }}>
                                            Tocantins</option>
                                    </select>
                                    @error('address_state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_zip_code" class="block text-sm font-medium text-gray-700">CEP
                                        *</label>
                                    <input type="text" name="address_zip_code" id="address_zip_code"
                                        value="{{ old('address_zip_code') }}" required placeholder="00000-000"
                                        maxlength="9"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address_zip_code') border-red-300 @enderror">
                                    @error('address_zip_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configurações do Sistema -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações do Sistema de Ponto</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-600">
                                        <strong>Nota:</strong> As configurações de tolerância agora são definidas nos
                                        horários de trabalho para maior flexibilidade.
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="requires_justification" id="requires_justification"
                                        value="1" {{ old('requires_justification', true) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="requires_justification" class="ml-2 block text-sm text-gray-900">
                                        Exigir justificativa para alterações de ponto
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('companies.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar Empresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Máscara para CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });



        // Máscara para telefone
        document.getElementById('contact_phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Máscara para CEP
        document.getElementById('address_zip_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    </script>
@endsection
