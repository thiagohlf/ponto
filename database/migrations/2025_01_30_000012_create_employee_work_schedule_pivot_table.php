<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela pivot - Relacionamento entre funcionários e horários de trabalho
     * Permite que funcionários tenham horários diferentes em períodos específicos
     */
    public function up(): void
    {
        Schema::create('employee_work_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_schedule_id')->constrained()->onDelete('cascade');
            
            // Período de vigência do horário
            $table->date('start_date'); // Data de início da vigência
            $table->date('end_date')->nullable(); // Data de fim (null = indefinido)
            
            // Configurações específicas do funcionário
            $table->json('custom_schedule')->nullable(); // Horário personalizado (sobrescreve o padrão)
            $table->integer('custom_tolerance')->nullable(); // Tolerância específica (minutos)
            
            // Exceções e observações
            $table->text('notes')->nullable(); // Observações sobre este horário
            $table->boolean('temporary')->default(false); // Horário temporário
            $table->string('reason')->nullable(); // Motivo da mudança de horário
            
            // Aprovação
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices e constraints
            $table->index(['employee_id', 'start_date', 'end_date']);
            $table->index(['work_schedule_id', 'active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_work_schedule');
    }
};