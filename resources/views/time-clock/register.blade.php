<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registro de Ponto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Informações do Funcionário -->
                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $employee->name }}</h3>
                        <p class="text-gray-600">{{ $employee->position }} - {{ $employee->department->name }}</p>
                        <p class="text-sm text-gray-500">Matrícula: {{ $employee->registration_number }}</p>
                    </div>

                    <!-- Relógio Digital -->
                    <div class="text-center mb-8">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-lg p-8 shadow-lg">
                            <div id="current-time" class="text-6xl font-mono text-green-400 mb-2">
                                --:--:--
                            </div>
                            <div id="current-date" class="text-xl text-gray-300">
                                --/--/----
                            </div>
                        </div>
                    </div>

                    <!-- Próximo Registro -->
                    <div class="text-center mb-8">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-blue-800 mb-2">Próximo Registro</h4>
                            <p id="next-record-type" class="text-2xl font-bold text-blue-600">
                                {{ $nextRecordType === 'entry' ? 'Entrada' : 
                                   ($nextRecordType === 'meal_start' ? 'Início do Almoço' : 
                                   ($nextRecordType === 'meal_end' ? 'Fim do Almoço' : 'Saída')) }}
                            </p>
                        </div>
                    </div>

                    <!-- Botão de Registro -->
                    <div class="text-center mb-8">
                        <button id="register-btn" 
                                class="font-bold rounded-lg text-xl shadow-lg transform transition-all duration-200 hover:scale-105"
                                style="background-color: #16a34a; color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; white-space: nowrap; padding: 12px 37px;"
                                onmouseover="this.style.backgroundColor='#15803d'"
                                onmouseout="this.style.backgroundColor='#16a34a'">
                            <svg style="width: 24px; height: 24px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Registrar Ponto
                        </button>
                    </div>

                    <!-- Mensagens -->
                    <div id="message-container" class="mb-6 hidden">
                        <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded hidden">
                            <strong>Sucesso!</strong> <span id="success-text"></span>
                        </div>
                        <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
                            <strong>Erro!</strong> <span id="error-text"></span>
                        </div>
                    </div>

                    <!-- Registros do Dia -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Registros de Hoje</h4>
                        
                        <!-- Cabeçalho da tabela -->
                        <div class="grid grid-cols-4 gap-4 items-center bg-gray-200 p-3 rounded-t border-b font-semibold text-gray-700">
                            <div>Tipo</div>
                            <div class="text-center">Status</div>
                            <div class="text-center">Hora</div>
                            <div class="text-center">Data</div>
                        </div>
                        
                        <div id="today-records" class="space-y-0">
                            <!-- Registros serão carregados via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Atualizar relógio em tempo real
        function updateClock() {
            const now = new Date();
            
            // Formatar hora
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            
            // Formatar data
            const day = now.getDate().toString().padStart(2, '0');
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const year = now.getFullYear();
            const dateString = `${day}/${month}/${year}`;
            
            document.getElementById('current-time').textContent = timeString;
            document.getElementById('current-date').textContent = dateString;
        }

        // Carregar registros do dia
        function loadTodayRecords() {
            fetch('{{ route("time-clock.today-records") }}')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('today-records');
                    
                    if (data.records && data.records.length > 0) {
                        container.innerHTML = data.records.map(record => {
                            const dateOnly = record.datetime.split(' ')[0]; // Extrair apenas a data (dd/mm/aaaa)
                            return `
                            <div class="grid grid-cols-4 gap-4 items-center bg-white p-3 border-b border-gray-200">
                                <div>
                                    <span class="font-semibold text-blue-600">${record.type}</span>
                                </div>
                                <div class="text-center">
                                    <span class="text-sm px-2 py-1 rounded ${record.status === 'Válido' ? 'bg-green-100 text-green-700' : record.status === 'Pendente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'}">${record.status}</span>
                                </div>
                                <div class="text-center">
                                    <div class="font-mono text-lg font-bold">${record.time_only}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm text-gray-600">${dateOnly}</div>
                                </div>
                            </div>
                        `;
                        }).join('');
                    } else {
                        container.innerHTML = '<p class="text-gray-500 text-center">Nenhum registro hoje</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar registros:', error);
                });
        }

        // Registrar ponto
        function registerPoint() {
            const button = document.getElementById('register-btn');
            const originalText = button.innerHTML;
            
            // Desabilitar botão e mostrar loading
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin inline w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Registrando...';
            
            fetch('{{ route("time-clock.register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    
                    // Atualizar próximo tipo de registro
                    document.getElementById('next-record-type').textContent = data.record.next_type;
                    
                    // Recarregar registros do dia
                    loadTodayRecords();
                } else {
                    showMessage('error', data.error || 'Erro ao registrar ponto');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('error', 'Erro de conexão. Tente novamente.');
            })
            .finally(() => {
                // Reabilitar botão
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        // Mostrar mensagens
        function showMessage(type, text) {
            const container = document.getElementById('message-container');
            const successMsg = document.getElementById('success-message');
            const errorMsg = document.getElementById('error-message');
            
            // Esconder todas as mensagens
            successMsg.classList.add('hidden');
            errorMsg.classList.add('hidden');
            
            // Mostrar mensagem apropriada
            if (type === 'success') {
                document.getElementById('success-text').textContent = text;
                successMsg.classList.remove('hidden');
            } else {
                document.getElementById('error-text').textContent = text;
                errorMsg.classList.remove('hidden');
            }
            
            container.classList.remove('hidden');
            
            // Esconder mensagem após 5 segundos
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar relógio imediatamente e depois a cada segundo
            updateClock();
            setInterval(updateClock, 1000);
            
            // Carregar registros do dia
            loadTodayRecords();
            
            // Adicionar evento ao botão
            document.getElementById('register-btn').addEventListener('click', registerPoint);
        });
    </script>
</x-app-layout>