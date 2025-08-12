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

            // Endereço completo em um campo JSON para flexibilidade
            $table->json('address_data'); // {street, number, complement, neighborhood, city, state, zip_code}
            
            // Contatos em JSON para múltiplos contatos
            $table->json('contact_data')->nullable(); // {phone, email, mobile, etc}

            // Configurações globais do sistema de ponto
            $table->boolean('requires_justification')->default(true); // Exige justificativa para alterações
            
            // Auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Quem criou
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Última modificação
            
            // Nota: tolerance_minutes movido para work_schedules (específico por horário)
            $table->boolean('active')->default(true);

            $table->timestamps();
            
            // Índices de auditoria
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
