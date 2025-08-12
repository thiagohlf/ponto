<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de funcionários - Dados dos empregados
     * Conforme Art. 74 da CLT e Portaria 671/2021 do MTE
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Relação com users (name, email)
            $table->foreignId('work_schedule_id')->constrained()->onDelete('restrict'); // Horário padrão obrigatório

            // Dados pessoais únicos (não duplicados de users)
            $table->string('cpf', 14)->unique(); // CPF formatado (XXX.XXX.XXX-XX)
            $table->string('rg')->nullable(); // RG
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F', 'O'])->nullable(); // Masculino, Feminino, Outro

            // Dados trabalhistas
            $table->string('registration_number')->unique(); // Matrícula do funcionário
            $table->string('pis_pasep', 14)->nullable(); // PIS/PASEP formatado
            $table->date('admission_date'); // Data de admissão
            $table->date('dismissal_date')->nullable(); // Data de demissão
            $table->string('position'); // Cargo
            $table->decimal('salary', 10, 2)->nullable(); // Salário

            // Endereço em JSON (sem duplicar contatos que estão em users)
            $table->json('address_data')->nullable(); // {street, number, complement, neighborhood, city, state, zip_code}

            // Configurações específicas do ponto
            $table->boolean('exempt_time_control')->default(false); // Isento de controle (Art. 62 CLT)
            $table->string('photo_path')->nullable(); // Caminho da foto
            
            // Biometria para marcação web (se disponível)
            $table->text('fingerprint_template')->nullable(); // Template da digital para web
            
            // Nota: weekly_hours, has_meal_break, meal_break_minutes movidos para work_schedules

            $table->boolean('active')->default(true);
            $table->timestamps();

            // Índices para performance
            $table->index(['company_id', 'active']);
            $table->index(['department_id', 'active']);
            $table->index(['user_id', 'active']);
            $table->index(['work_schedule_id', 'active']);
            $table->index('admission_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
