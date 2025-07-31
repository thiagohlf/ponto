<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de empresas - Armazena dados das empresas que utilizam o sistema
     * Conforme Art. 74 da CLT - identificação do empregador
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Dados básicos da empresa
            $table->string('name'); // Razão social
            $table->string('trade_name')->nullable(); // Nome fantasia
            $table->string('cnpj', 18)->unique(); // CNPJ formatado (XX.XXX.XXX/XXXX-XX)
            $table->string('state_registration')->nullable(); // Inscrição estadual
            $table->string('municipal_registration')->nullable(); // Inscrição municipal

            // Endereço
            $table->string('address');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 9); // CEP formatado (XXXXX-XXX)

            // Contato
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Configurações do sistema de ponto
            $table->integer('tolerance_minutes')->default(10); // Tolerância em minutos (Art. 58, §1º CLT)
            $table->boolean('requires_justification')->default(true); // Exige justificativa para alterações
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
