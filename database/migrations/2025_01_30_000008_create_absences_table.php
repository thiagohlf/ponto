<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de faltas e ausências - Controle de faltas justificadas e injustificadas
     * Conforme Art. 473 da CLT e legislação trabalhista
     */
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            
            // Período da ausência
            $table->date('start_date'); // Data de início
            $table->date('end_date'); // Data de fim
            $table->integer('total_days'); // Total de dias de ausência
            
            // Tipo de ausência
            $table->enum('absence_type', [
                'atestado_medico', // Atestado médico
                'ferias', // Férias
                'licenca_maternidade', // Licença maternidade
                'licenca_paternidade', // Licença paternidade
                'luto', // Luto (Art. 473, I CLT)
                'casamento', // Casamento (Art. 473, II CLT)
                'doacao_sangue', // Doação de sangue (Art. 473, IV CLT)
                'servico_militar', // Serviço militar
                'juri', // Júri (Art. 473, VII CLT)
                'testemunha', // Testemunha (Art. 473, VI CLT)
                'atividade_sindical', // Atividade sindical
                'licenca_estudos', // Licença para estudos
                'licenca_sem_vencimento', // Licença sem vencimento
                'falta_injustificada', // Falta injustificada
                'outros' // Outros motivos
            ]);
            
            // Detalhes da justificativa
            $table->text('justification')->nullable(); // Justificativa detalhada
            $table->foreignId('medical_certificate_id')->nullable()->constrained()->onDelete('set null'); // Relação com atestado médico
            
            // Nota: Campos médicos movidos para medical_certificates table
            
            // Status (aprovação centralizada na tabela approvals via polimorfismo)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Nota: Dados de aprovação movidos para approvals table (polimórfica)
            
            // Impacto financeiro
            $table->boolean('paid_absence')->default(true); // Ausência remunerada
            $table->decimal('discount_amount', 10, 2)->default(0); // Valor do desconto
            
            $table->timestamps();
            
            // Índices
            $table->index(['employee_id', 'start_date', 'end_date']);
            $table->index(['absence_type', 'status']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};