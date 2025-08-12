<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\TimeRecord;
use App\Models\Employee;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $timeRecords = TimeRecord::all();
        $employees = Employee::all();
        
        if ($timeRecords->count() === 0) {
            $this->command->info('ℹ️ Nenhum registro de ponto encontrado. Localizações serão criadas quando houver registros.');
            return;
        }

        $locations = [];

        // Localizações para registros de ponto
        foreach ($timeRecords->take(10) as $record) { // Limitar a 10 para exemplo
            $locations[] = [
                'locatable_type' => TimeRecord::class,
                'locatable_id' => $record->id,
                'latitude' => -23.5505 + (rand(-100, 100) / 10000), // São Paulo com variação
                'longitude' => -46.6333 + (rand(-100, 100) / 10000),
                'address' => 'Rua das Empresas, ' . rand(100, 999),
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'BR',
                'accuracy' => rand(5, 50), // Precisão em metros
                'source' => 'gps',
                'recorded_at' => $record->full_datetime,
            ];
        }

        // Localizações para funcionários (endereço residencial)
        foreach ($employees->take(5) as $employee) { // Limitar a 5 para exemplo
            $locations[] = [
                'locatable_type' => Employee::class,
                'locatable_id' => $employee->id,
                'latitude' => -23.5505 + (rand(-500, 500) / 10000), // Variação maior para residências
                'longitude' => -46.6333 + (rand(-500, 500) / 10000),
                'address' => 'Rua Residencial, ' . rand(1, 500),
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'BR',
                'accuracy' => rand(10, 100),
                'source' => 'manual',
                'recorded_at' => now(),
            ];
        }

        foreach ($locations as $location) {
            Location::create($location);
        }

        $this->command->info('✅ Localizações de exemplo criadas: ' . count($locations));
    }
}