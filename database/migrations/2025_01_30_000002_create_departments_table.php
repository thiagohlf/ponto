<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de departamentos - Organização interna da empresa
     * Para controle de custos e organização hierárquica
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            $table->string('name'); // Nome do departamento
            $table->string('code')->nullable(); // Código interno do departamento
            $table->text('description')->nullable(); // Descrição do departamento
            
            // Hierarquia de departamentos (auto-relacionamento)
            $table->foreignId('parent_department_id')->nullable()->constrained('departments')->onDelete('set null');
            
            // Centro de custo
            $table->string('cost_center')->nullable();
            
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices para performance
            $table->index(['company_id', 'active']);
            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};