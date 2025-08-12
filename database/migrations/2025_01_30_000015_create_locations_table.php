<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de localizações - Centraliza dados de localização geográfica
     * Remove duplicação de latitude/longitude de time_records
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento polimórfico
            $table->string('locatable_type'); // App\Models\TimeRecord, App\Models\Employee, etc.
            $table->unsignedBigInteger('locatable_id');
            
            // Dados de localização
            $table->decimal('latitude', 10, 8); // Latitude
            $table->decimal('longitude', 11, 8); // Longitude
            $table->string('address')->nullable(); // Endereço aproximado
            $table->string('city')->nullable(); // Cidade
            $table->string('state', 2)->nullable(); // Estado
            $table->string('country', 2)->default('BR'); // País
            
            // Precisão e confiabilidade
            $table->decimal('accuracy', 8, 2)->nullable(); // Precisão em metros
            $table->enum('source', ['gps', 'network', 'manual', 'ip'])->default('gps'); // Fonte da localização
            $table->timestamp('recorded_at')->useCurrent(); // Quando foi registrada
            
            $table->timestamps();
            
            // Índices
            $table->index(['locatable_type', 'locatable_id']);
            $table->index(['latitude', 'longitude']);
            $table->index(['city', 'state']);
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};