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
                'sick_leave', // Atestado médico
                'vacation', // Férias
                'maternity_leave', // Licença maternidade
                'paternity_leave', // Licença paternidade
                'bereavement', // Luto (Art. 473, I CLT)
                'marriage', // Casamento (Art. 473, II CLT)
                'blood_donation', // Doação de sangue (Art. 473, IV CLT)
                'military_service', // Serviço militar
                'jury_duty', // Júri (Art. 473, VII CLT)
                'witness_testimony', // Testemunha (Art. 473, VI CLT)
                'union_activity', // Atividade sindical
                'study_leave', // Licença para estudos
                'unpaid_leave', // Licença sem vencimento
                'unjustified', // Falta injustificada
                'other' // Outros motivos
            ]);
            
            // Detalhes da justificativa
            $table->text('justification')->nullable(); // Justificativa detalhada
            $table->string('document_number')->nullable(); // Número do documento (atestado, etc.)
            $table->string('document_path')->nullable(); // Caminho do arquivo do documento
            
            // Informações médicas (se aplicável)
            $table->string('doctor_name')->nullable(); // Nome do médico
            $table->string('doctor_crm')->nullable(); // CRM do médico
            $table->string('medical_code')->nullable(); // CID ou código médico
            
            // Status e aprovação
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
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