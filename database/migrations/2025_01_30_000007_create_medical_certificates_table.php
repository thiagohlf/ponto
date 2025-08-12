<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de atestados médicos - Centraliza informações médicas
     * Remove duplicação de campos médicos de absences
     */
    public function up(): void
    {
        Schema::create('medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Dados do documento
            $table->string('document_number')->unique(); // Número do documento (atestado, etc.)
            $table->string('document_path')->nullable(); // Caminho do arquivo do documento
            $table->enum('document_type', [
                'atestado_medico',
                'laudo_medico', 
                'receita_medica',
                'exame_medico',
                'outros'
            ]);
            
            // Informações médicas
            $table->string('doctor_name'); // Nome do médico
            $table->string('doctor_crm'); // CRM do médico
            $table->string('medical_code')->nullable(); // CID ou código médico
            $table->text('medical_description')->nullable(); // Descrição médica
            
            // Período de validade
            $table->date('issue_date'); // Data de emissão
            $table->date('start_date'); // Data de início da validade
            $table->date('end_date'); // Data de fim da validade
            $table->integer('total_days'); // Total de dias
            
            // Status (aprovação centralizada na tabela approvals via polimorfismo)
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            
            // Nota: Dados de aprovação movidos para approvals table (polimórfica)
            
            $table->timestamps();
            
            // Índices
            $table->index(['employee_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_certificates');
    }
};