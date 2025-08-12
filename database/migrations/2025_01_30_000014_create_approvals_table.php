<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de aprovações - Centraliza processo de aprovação para diferentes entidades
     * Remove duplicação de campos de aprovação espalhados em várias tabelas
     */
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento polimórfico
            $table->string('approvable_type'); // App\Models\Absence, App\Models\Overtime, etc.
            $table->unsignedBigInteger('approvable_id');
            
            // Dados da aprovação
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->enum('type', [
                'absence', 
                'overtime', 
                'schedule_change', 
                'time_adjustment', 
                'holiday_work',
                'medical_certificate',
                'other'
            ]);
            
            // Solicitante e aprovador
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('requested_at')->useCurrent();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Justificativas e observações
            $table->text('request_justification'); // Justificativa da solicitação
            $table->text('approval_notes')->nullable(); // Observações do aprovador
            $table->text('rejection_reason')->nullable(); // Motivo da rejeição
            
            // Dados adicionais
            $table->json('metadata')->nullable(); // Dados específicos do tipo de aprovação
            $table->integer('priority')->default(1); // Prioridade (1=baixa, 5=alta)
            $table->date('deadline')->nullable(); // Prazo para aprovação
            
            $table->timestamps();
            
            // Índices
            $table->index(['approvable_type', 'approvable_id']);
            $table->index(['status', 'type']);
            $table->index(['requested_by', 'status']);
            $table->index(['approved_by', 'approved_at']);
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};