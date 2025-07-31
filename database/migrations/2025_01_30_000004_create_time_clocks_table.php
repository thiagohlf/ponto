<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de relógios de ponto - Equipamentos de marcação
     * Conforme Portaria 671/2021 do MTE - identificação dos equipamentos
     */
    public function up(): void
    {
        Schema::create('time_clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Identificação do equipamento
            $table->string('name'); // Nome/identificação do relógio
            $table->string('serial_number')->unique(); // Número de série
            $table->string('model')->nullable(); // Modelo do equipamento
            $table->string('manufacturer')->nullable(); // Fabricante
            
            // Localização
            $table->string('location'); // Local onde está instalado
            $table->string('ip_address')->nullable(); // IP do equipamento (se conectado em rede)
            $table->string('mac_address')->nullable(); // MAC Address
            
            // Configurações técnicas
            $table->enum('connection_type', ['ethernet', 'wifi', 'serial', 'usb'])->default('ethernet');
            $table->json('settings')->nullable(); // Configurações específicas do equipamento
            
            // Certificação e conformidade (Portaria 671/2021)
            $table->string('certification_number')->nullable(); // Número da certificação INMETRO
            $table->date('certification_date')->nullable(); // Data da certificação
            $table->date('last_calibration')->nullable(); // Última calibração
            $table->date('next_calibration')->nullable(); // Próxima calibração
            
            // Status operacional
            $table->boolean('active')->default(true);
            $table->timestamp('last_sync')->nullable(); // Última sincronização
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            
            $table->timestamps();
            
            // Índices
            $table->index(['company_id', 'active']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_clocks');
    }
};