<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela pivot - Relacionamento entre funcionários e relógios de ponto
     * Define quais funcionários podem usar quais relógios de ponto
     */
    public function up(): void
    {
        Schema::create('employee_time_clock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('time_clock_id')->constrained()->onDelete('cascade');
            
            // Configurações específicas do funcionário para este relógio
            $table->boolean('can_register')->default(true); // Pode registrar ponto
            $table->boolean('requires_supervisor_approval')->default(false); // Requer aprovação
            
            // Dados de cadastro biométrico específicos para este relógio
            $table->text('biometric_template')->nullable(); // Template específico para este equipamento
            $table->string('rfid_card_number')->nullable(); // Número do cartão para este relógio
            $table->string('pin_code')->nullable(); // PIN específico
            
            // Controle de acesso por horário
            $table->time('access_start_time')->nullable(); // Horário mínimo para marcação
            $table->time('access_end_time')->nullable(); // Horário máximo para marcação
            
            // Auditoria
            $table->timestamp('registered_at')->useCurrent(); // Quando foi cadastrado
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('active')->default(true);
            
            $table->timestamps();
            
            // Índices e constraints
            $table->unique(['employee_id', 'time_clock_id']);
            $table->index(['employee_id', 'active']);
            $table->index(['time_clock_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_time_clock');
    }
};