<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela pivot - Relacionamento entre funcionários e feriados
     * Para casos especiais onde funcionário trabalha em feriado ou tem feriado específico
     */
    public function up(): void
    {
        Schema::create('employee_holiday', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('holiday_id')->constrained()->onDelete('cascade');
            
            // Status do funcionário neste feriado
            $table->enum('status', [
                'off', // Folga normal
                'working', // Trabalhando no feriado
                'compensating', // Compensando em outro dia
                'vacation', // Em férias
                'absent' // Ausente
            ])->default('off');
            
            // Se trabalhou no feriado
            $table->time('work_start_time')->nullable(); // Hora de início do trabalho
            $table->time('work_end_time')->nullable(); // Hora de fim do trabalho
            $table->integer('worked_minutes')->default(0); // Minutos trabalhados
            
            // Cálculos financeiros
            $table->decimal('base_salary_day', 10, 2)->nullable(); // Valor do dia normal
            $table->decimal('holiday_multiplier', 3, 2)->default(2.00); // Multiplicador do feriado
            $table->decimal('calculated_amount', 10, 2)->default(0); // Valor calculado
            
            // Compensação
            $table->date('compensation_date')->nullable(); // Data da compensação
            $table->boolean('compensated')->default(false); // Já foi compensado
            $table->text('compensation_notes')->nullable(); // Observações da compensação
            
            // Justificativas (aprovações centralizadas na tabela approvals via polimorfismo)
            $table->text('work_justification')->nullable(); // Justificativa para trabalhar no feriado
            
            // Nota: Dados de aprovação movidos para approvals table (polimórfica)
            
            // Status de pagamento
            $table->enum('payment_status', ['pending', 'calculated', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            
            $table->timestamps();
            
            // Índices e constraints
            $table->unique(['employee_id', 'holiday_id']);
            $table->index(['employee_id', 'status']);
            $table->index(['holiday_id', 'status']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_holiday');
    }
};