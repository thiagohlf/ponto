<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de horários de trabalho - Define os horários padrão dos funcionários
     * Conforme Art. 58 da CLT - jornada de trabalho
     */
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Identificação do horário
            $table->string('name'); // Nome do horário (ex: "Comercial", "Turno A")
            $table->text('description')->nullable();
            
            // Configuração semanal
            $table->integer('weekly_hours')->default(44); // Carga horária semanal (Art. 58 CLT)
            $table->integer('daily_hours')->default(8); // Carga horária diária
            
            // Horários por dia da semana (JSON para flexibilidade)
            $table->json('monday_schedule')->nullable(); // {"entry": "08:00", "exit": "17:00", "meal_start": "12:00", "meal_end": "13:00"}
            $table->json('tuesday_schedule')->nullable();
            $table->json('wednesday_schedule')->nullable();
            $table->json('thursday_schedule')->nullable();
            $table->json('friday_schedule')->nullable();
            $table->json('saturday_schedule')->nullable();
            $table->json('sunday_schedule')->nullable();
            
            // Configurações de intervalo (centralizadas aqui - removidas de employees)
            $table->boolean('has_meal_break')->default(true);
            $table->integer('meal_break_minutes')->default(60); // Em minutos
            $table->time('meal_break_start')->nullable(); // Início padrão do intervalo
            $table->time('meal_break_end')->nullable(); // Fim padrão do intervalo
            
            // Tolerâncias (centralizadas aqui - removidas de companies)
            $table->integer('entry_tolerance')->default(10); // Tolerância entrada (minutos)
            $table->integer('exit_tolerance')->default(10); // Tolerância saída (minutos)
            $table->integer('general_tolerance')->default(10); // Tolerância geral (substitui companies.tolerance_minutes)
            
            // Configurações especiais
            $table->boolean('flexible_schedule')->default(false); // Horário flexível
            $table->integer('flexible_minutes')->default(0); // Minutos de flexibilidade
            
            // Banco de horas
            $table->boolean('allows_overtime')->default(true); // Permite hora extra
            $table->integer('max_daily_overtime')->default(120); // Máximo de hora extra diária (minutos)
            $table->boolean('compensatory_time')->default(false); // Banco de horas
            
            // Auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Quem criou
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Última modificação
            
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index(['company_id', 'active']);
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};