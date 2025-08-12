<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de registros de ponto - Marcações dos funcionários
     * Conforme Art. 74 da CLT e Portaria 671/2021 do MTE
     * Deve manter integridade e inalterabilidade dos dados
     */
    public function up(): void
    {
        Schema::create('time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            // Nota: time_clock_id removido - marcação apenas web
            
            // Data e hora da marcação
            $table->date('record_date'); // Data da marcação
            $table->time('record_time'); // Hora da marcação
            $table->timestamp('full_datetime'); // Data e hora completa (para facilitar consultas)
            
            // Tipo de marcação
            $table->enum('record_type', [
                'entry', // Entrada
                'exit', // Saída
                'meal_start', // Início do intervalo
                'meal_end', // Fim do intervalo
                'break_start', // Início de pausa
                'break_end' // Fim de pausa
            ]);
            
            // Método de identificação usado (apenas web)
            $table->enum('identification_method', [
                'web_login', // Login web (usuário/senha)
                'web_biometric', // Biometria via web (se disponível)
                'manual' // Marcação manual por supervisor
            ])->default('web_login');
            
            // Dados de segurança e auditoria (Portaria 671/2021)
            $table->string('nsr')->unique(); // Número Sequencial de Registro
            $table->text('digital_signature')->nullable(); // Assinatura digital do registro
            $table->string('hash_verification')->nullable(); // Hash para verificação de integridade
            
            // Nota: Dados de localização movidos para locations table (polimórfica)
            
            // Status e observações
            $table->enum('status', ['valid', 'invalid', 'pending_approval'])->default('valid');
            $table->text('observations')->nullable(); // Observações sobre a marcação
            
            // Dados de alteração (centralizados em JSON)
            $table->json('change_data')->nullable(); // {original_datetime, change_justification, changed_by, changed_at}
            
            // Auditoria web (dados do navegador/dispositivo)
            $table->string('ip_address', 45)->nullable(); // IP do usuário
            $table->text('user_agent')->nullable(); // User agent do navegador
            $table->string('device_info')->nullable(); // Informações do dispositivo
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['employee_id', 'record_date']);
            $table->index(['record_date', 'record_time']);
            $table->index('full_datetime');
            $table->index('nsr');
            $table->index('status');
            $table->index(['identification_method', 'status']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_records');
    }
};