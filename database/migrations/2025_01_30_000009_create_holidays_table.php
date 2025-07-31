<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de feriados - Controle de feriados nacionais, estaduais e municipais
     * Conforme Lei 662/1949 (feriados nacionais) e legislações locais
     */
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade'); // null = feriado nacional
            
            // Dados do feriado
            $table->string('name'); // Nome do feriado
            $table->date('date'); // Data do feriado
            $table->integer('year'); // Ano (para facilitar consultas)
            
            // Tipo de feriado
            $table->enum('type', [
                'national', // Nacional (Lei 662/1949)
                'state', // Estadual
                'municipal', // Municipal
                'company' // Específico da empresa
            ]);
            
            // Localização (para feriados regionais)
            $table->string('state', 2)->nullable(); // UF (para feriados estaduais)
            $table->string('city')->nullable(); // Cidade (para feriados municipais)
            
            // Características do feriado
            $table->boolean('is_fixed')->default(true); // Data fixa ou móvel (ex: Páscoa)
            $table->boolean('is_recurring')->default(true); // Se repete anualmente
            $table->text('description')->nullable(); // Descrição do feriado
            
            // Configurações trabalhistas
            $table->boolean('mandatory_rest')->default(true); // Descanso obrigatório
            $table->boolean('allows_work')->default(false); // Permite trabalho
            $table->decimal('work_multiplier', 3, 2)->default(2.00); // Multiplicador se trabalhar (100% extra)
            
            // Feriados móveis (cálculo automático)
            $table->string('calculation_rule')->nullable(); // Regra para calcular data (ex: Páscoa + X dias)
            $table->integer('days_offset')->nullable(); // Offset em dias para cálculo
            
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index(['date', 'type']);
            $table->index(['year', 'type']);
            $table->index(['company_id', 'date']);
            $table->index(['state', 'city', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};