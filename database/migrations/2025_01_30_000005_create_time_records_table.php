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
            $table->foreignId('time_clock_id')->nullable()->constrained()->onDelete('set null');
            
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
            
            // Método de identificação usado
            $table->enum('identification_method', [
                'biometric', // Biometria
                'rfid', // Cartão RFID
                'pin', // PIN/Senha
                'facial', // Reconhecimento facial
                'manual' // Marcação manual
            ]);
            
            // Dados de segurança e auditoria (Portaria 671/2021)
            $table->string('nsr')->unique(); // Número Sequencial de Registro
            $table->text('digital_signature')->nullable(); // Assinatura digital do registro
            $table->string('hash_verification')->nullable(); // Hash para verificação de integridade
            
            // Localização (se disponível)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Status e observações
            $table->enum('status', ['valid', 'invalid', 'pending_approval'])->default('valid');
            $table->text('observations')->nullable(); // Observações sobre a marcação
            
            // Dados de alteração (se houver)
            $table->timestamp('original_datetime')->nullable(); // Data/hora original (se foi alterada)
            $table->text('change_justification')->nullable(); // Justificativa da alteração
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null'); // Quem alterou
            $table->timestamp('changed_at')->nullable(); // Quando foi alterada
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['employee_id', 'record_date']);
            $table->index(['record_date', 'record_time']);
            $table->index(['time_clock_id', 'full_datetime']);
            $table->index('nsr');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_records');
    }
};