@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Gerenciar Perfis e Permissões</h2>
                            <p class="text-gray-600">Funcionário: <strong>{{ $employee->name }}</strong></p>
                            <p class="text-gray-600">Email: <strong>{{ $user->email }}</strong></p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('employees.show', $employee) }}"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Ver Funcionário
                            </a>
                            <a href="{{ route('employees.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Voltar
                            </a>
                        </div>
                    </div>

                    <!-- Mensagens de sucesso/erro -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Formulário para Perfis do Usuário -->
                    <form method="POST" action="{{ route('employees.user-permissions.update', $employee) }}">
                        @csrf
                        @method('PUT')

                        <div class="bg-blue-50 p-6 rounded-lg mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Perfis do Usuário</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Selecione os perfis que este usuário deve ter.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($roles as $role)
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            id="role_{{ $role->id }}"
                                            {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1">
                                        <div class="ml-3 flex-1">
                                            <label for="role_{{ $role->id }}"
                                                class="block text-sm font-medium text-gray-900 cursor-pointer">
                                                {{ $role->name }}
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $role->permissions->count() }} permissões incluídas
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="mt-6">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Atualizar Perfis do Usuário
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Gerenciamento de Permissões dos Perfis -->
                    <div class="border-t pt-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Gerenciar Permissões dos Perfis</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Configure as permissões de cada perfil do sistema.
                        </p>

                        @php
                            $allPermissions = \Spatie\Permission\Models\Permission::all()->groupBy(function (
                                $permission,
                            ) {
                                return explode('_', $permission->name)[0];
                            });
                        @endphp

                        <div class="space-y-8">
                            @foreach ($roles as $role)
                                <div class="border border-gray-200 rounded-lg">
                                    <div class="bg-gray-50 px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <h4 class="text-lg font-medium text-gray-900">{{ $role->name }}</h4>
                                                <span
                                                    class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $role->permissions->count() }} permissões
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <form method="POST"
                                            action="{{ route('employees.user-permissions.update', $employee) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="manage_role" value="{{ $role->id }}">

                                            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                                                @foreach ($allPermissions as $group => $permissions)
                                                    <div class="bg-white border border-gray-100 rounded-lg p-4">
                                                        <h5
                                                            class="text-sm font-semibold text-gray-700 uppercase mb-3 border-b pb-2">
                                                            {{ ucfirst($group) }}
                                                        </h5>
                                                        <div class="space-y-2">
                                                            @foreach ($permissions as $permission)
                                                                <div class="flex items-center">
                                                                    <input type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->name }}"
                                                                        id="role_{{ $role->id }}_perm_{{ $permission->id }}"
                                                                        {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}
                                                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                                    <label
                                                                        for="role_{{ $role->id }}_perm_{{ $permission->id }}"
                                                                        class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                                                        {{ str_replace('_', ' ', $permission->name) }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="flex justify-end space-x-3 border-t pt-4">
                                                <button type="submit"
                                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    Atribuir Permissões ao Perfil {{ $role->name }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissões Efetivas Atuais -->
                    <div class="bg-gray-50 p-6 rounded-lg mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Permissões Efetivas do Usuário</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Estas são todas as permissões que o usuário possui através dos perfis atribuídos.
                        </p>

                        @php
                            $userPermissions = $user->getAllPermissions();
                        @endphp

                        @if ($userPermissions->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach ($userPermissions as $permission)
                                    <div class="p-2 bg-white rounded-md border text-xs text-center">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                Total: {{ $userPermissions->count() }} permissões ativas
                            </p>
                        @else
                            <p class="text-sm text-gray-500">Nenhuma permissão encontrada.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
