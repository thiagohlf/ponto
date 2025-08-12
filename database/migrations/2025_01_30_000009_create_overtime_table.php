<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de horas extras - Controle de horas extras e banco de horas
     * Conforme Art. 59 da CLT e legislação sobre horas extras
     */
    public function up(): void
    {
        Schema::create('overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Data e período
            $table->date('work_date'); // Data do trabalho
            $table->time('start_time'); // Hora de início da extra
            $table->time('end_time'); // Hora de fim da extra
            $table->integer('total_minutes'); // Total de minutos extras
            
            // Tipo de hora extra
            $table->enum('overtime_type', [
                'hora_extra_diaria', // Hora extra diária (até 2h - Art. 59 CLT)
                'trabalho_fim_semana', // Trabalho em fim de semana
                'trabalho_feriado', // Trabalho em feriado
                'adicional_noturno', // Adicional noturno (Art. 73 CLT)
                'banco_horas', // Banco de horas
                'emergencia' // Situação de emergência
            ]);
            
            // Cálculos financeiros
            $table->decimal('hourly_rate', 8, 2); // Valor da hora normal
            $table->decimal('overtime_multiplier', 3, 2)->default(1.50); // Multiplicador (50%, 100%, etc.)
            $table->decimal('calculated_amount', 10, 2); // Valor calculado
            
            // Adicional noturno (Art. 73 CLT - 22h às 5h)
            $table->boolean('night_shift_applicable')->default(false);
            $table->integer('night_shift_minutes')->default(0); // Minutos no período noturno
            $table->decimal('night_shift_percentage', 5, 2)->default(20.00); // 20% adicional noturno
            
            // Justificativa (autorização centralizada na tabela approvals via polimorfismo)
            $table->text('justification'); // Motivo da hora extra
            
            // Nota: Dados de autorização movidos para approvals table (polimórfica)
            
            // Banco de horas (se aplicável)
            $table->boolean('compensatory_time')->default(false); // Vai para banco de horas
            $table->date('compensation_deadline')->nullable(); // Prazo para compensar
            $table->boolean('compensated')->default(false); // Já foi compensada
            $table->date('compensated_date')->nullable(); // Data da compensação
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['employee_id', 'work_date']);
            $table->index(['overtime_type', 'status']);
            $table->index('work_date');
            $table->index(['compensatory_time', 'compensated']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime');
    }
};