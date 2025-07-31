<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nova Empresa
            </h2>
            <a href="{{ route('companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

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
                                    <label for="name" class="block text-sm font-medium text-gray-700">Razão Social *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="trade_name" class="block text-sm font-medium text-gray-700">Nome Fantasia</label>
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
                                    <label for="state_registration" class="block text-sm font-medium text-gray-700">Inscrição Estadual</label>
                                    <input type="text" name="state_registration" id="state_registration" value="{{ old('state_registration') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state_registration') border-red-300 @enderror">
                                    @error('state_registration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="municipal_registration" class="block text-sm font-medium text-gray-700">Inscrição Municipal</label>
                                    <input type="text" name="municipal_registration" id="municipal_registration" value="{{ old('municipal_registration') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('municipal_registration') border-red-300 @enderror">
                                    @error('municipal_registration')
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
                                           placeholder="(00) 0000-0000"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-300 @enderror">
                                    @error('phone')
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
                                    <label for="address" class="block text-sm font-medium text-gray-700">Endereço *</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-300 @enderror">
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="number" class="block text-sm font-medium text-gray-700">Número *</label>
                                    <input type="text" name="number" id="number" value="{{ old('number') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('number') border-red-300 @enderror">
                                    @error('number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="complement" class="block text-sm font-medium text-gray-700">Complemento</label>
                                    <input type="text" name="complement" id="complement" value="{{ old('complement') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('complement') border-red-300 @enderror">
                                    @error('complement')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="neighborhood" class="block text-sm font-medium text-gray-700">Bairro *</label>
                                    <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('neighborhood') border-red-300 @enderror">
                                    @error('neighborhood')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">Cidade *</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('city') border-red-300 @enderror">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">Estado *</label>
                                    <select name="state" id="state" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('state') border-red-300 @enderror">
                                        <option value="">Selecione...</option>
                                        <option value="AC" {{ old('state') == 'AC' ? 'selected' : '' }}>Acre</option>
                                        <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                        <option value="AP" {{ old('state') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                        <option value="AM" {{ old('state') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                        <option value="BA" {{ old('state') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                        <option value="CE" {{ old('state') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                        <option value="DF" {{ old('state') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                        <option value="ES" {{ old('state') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                        <option value="GO" {{ old('state') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                        <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                        <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                        <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                        <option value="MG" {{ old('state') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                        <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pará</option>
                                        <option value="PB" {{ old('state') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                        <option value="PR" {{ old('state') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                        <option value="PE" {{ old('state') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                        <option value="PI" {{ old('state') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                        <option value="RJ" {{ old('state') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                        <option value="RN" {{ old('state') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                        <option value="RS" {{ old('state') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                        <option value="RO" {{ old('state') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                        <option value="RR" {{ old('state') == 'RR' ? 'selected' : '' }}>Roraima</option>
                                        <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                        <option value="SP" {{ old('state') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                        <option value="SE" {{ old('state') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                        <option value="TO" {{ old('state') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                    </select>
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="zip_code" class="block text-sm font-medium text-gray-700">CEP *</label>
                                    <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code') }}" required
                                           placeholder="00000-000" maxlength="9"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('zip_code') border-red-300 @enderror">
                                    @error('zip_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configurações do Sistema -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações do Sistema de Ponto</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tolerance_minutes" class="block text-sm font-medium text-gray-700">Tolerância (minutos) *</label>
                                    <input type="number" name="tolerance_minutes" id="tolerance_minutes" value="{{ old('tolerance_minutes', 10) }}" required min="0" max="60"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tolerance_minutes') border-red-300 @enderror">
                                    <p class="mt-1 text-sm text-gray-500">Tolerância para entrada e saída conforme Art. 58, §1º da CLT</p>
                                    @error('tolerance_minutes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="requires_justification" id="requires_justification" value="1" {{ old('requires_justification', true) ? 'checked' : '' }}
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

        // Máscara para CEP
        document.getElementById('zip_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });

        // Máscara para telefone
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
    </script>
</x-app-layout>