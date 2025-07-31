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
            
            // Dados pessoais
            $table->string('name'); // Nome completo
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
            
            // Dados de contato
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 9)->nullable();
            
            // Configurações específicas do ponto
            $table->boolean('exempt_time_control')->default(false); // Isento de controle (Art. 62 CLT)
            $table->integer('weekly_hours')->default(44); // Carga horária semanal
            $table->boolean('has_meal_break')->default(true); // Tem intervalo para refeição
            $table->integer('meal_break_minutes')->default(60); // Duração do intervalo em minutos
            
            // Biometria e identificação
            $table->text('fingerprint_template')->nullable(); // Template da digital
            $table->string('rfid_card')->nullable(); // Cartão RFID
            $table->string('photo_path')->nullable(); // Caminho da foto
            
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices para performance
            $table->index(['company_id', 'active']);
            $table->index(['department_id', 'active']);
            $table->index('admission_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};