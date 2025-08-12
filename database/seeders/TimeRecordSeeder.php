<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeRecord;
use App\Models\Employee;
use Carbon\Carbon;

class TimeRecordSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        if ($employees->count() === 0) {
            $this->command->warn('Nenhum funcionário encontrado. Execute SystemSeeder primeiro.');
            return;
        }

        $records = [];
        $nsrCounter = 1;
        
        // Criar registros para os últimos 7 dias
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Pular fins de semana
            if ($date->isWeekend()) {
                continue;
            }
            
            foreach ($employees->take(3) as $employee) { // Apenas 3 funcionários para exemplo
                $entryTime = $date->copy()->setTime(8, rand(0, 15));
                $mealStartTime = $date->copy()->setTime(12, rand(0, 10));
                $mealEndTime = $date->copy()->setTime(13, rand(0, 10));
                $exitTime = $date->copy()->setTime(17, rand(0, 20));
                
                // Entrada manhã
                $records[] = [
                    'employee_id' => $employee->id,
                    'record_date' => $date->format('Y-m-d'),
                    'record_time' => $entryTime->format('H:i:s'),
                    'full_datetime' => $entryTime,
                    'record_type' => 'entry',
                    'identification_method' => 'web_login',
                    'nsr' => str_pad($nsrCounter++, 10, '0', STR_PAD_LEFT),
                    'hash_verification' => hash('sha256', 'entry_' . $employee->id . '_' . $entryTime->timestamp),
                    'ip_address' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'status' => 'valid',
                    'created_at' => $entryTime,
                    'updated_at' => $entryTime,
                ];
                
                // Saída almoço
                $records[] = [
                    'employee_id' => $employee->id,
                    'record_date' => $date->format('Y-m-d'),
                    'record_time' => $mealStartTime->format('H:i:s'),
                    'full_datetime' => $mealStartTime,
                    'record_type' => 'meal_start',
                    'identification_method' => 'web_login',
                    'nsr' => str_pad($nsrCounter++, 10, '0', STR_PAD_LEFT),
                    'hash_verification' => hash('sha256', 'meal_start_' . $employee->id . '_' . $mealStartTime->timestamp),
                    'ip_address' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'status' => 'valid',
                    'created_at' => $mealStartTime,
                    'updated_at' => $mealStartTime,
                ];
                
                // Volta almoço
                $records[] = [
                    'employee_id' => $employee->id,
                    'record_date' => $date->format('Y-m-d'),
                    'record_time' => $mealEndTime->format('H:i:s'),
                    'full_datetime' => $mealEndTime,
                    'record_type' => 'meal_end',
                    'identification_method' => 'web_login',
                    'nsr' => str_pad($nsrCounter++, 10, '0', STR_PAD_LEFT),
                    'hash_verification' => hash('sha256', 'meal_end_' . $employee->id . '_' . $mealEndTime->timestamp),
                    'ip_address' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'status' => 'valid',
                    'created_at' => $mealEndTime,
                    'updated_at' => $mealEndTime,
                ];
                
                // Saída tarde
                $records[] = [
                    'employee_id' => $employee->id,
                    'record_date' => $date->format('Y-m-d'),
                    'record_time' => $exitTime->format('H:i:s'),
                    'full_datetime' => $exitTime,
                    'record_type' => 'exit',
                    'identification_method' => 'web_login',
                    'nsr' => str_pad($nsrCounter++, 10, '0', STR_PAD_LEFT),
                    'hash_verification' => hash('sha256', 'exit_' . $employee->id . '_' . $exitTime->timestamp),
                    'ip_address' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'status' => 'valid',
                    'created_at' => $exitTime,
                    'updated_at' => $exitTime,
                ];
            }
        }
        
        // Adicionar alguns registros pendentes de aprovação
        $today = Carbon::now();
        if (!$today->isWeekend()) {
            $employee = $employees->first();
            $lateEntryTime = $today->copy()->setTime(8, 25);
            
            $records[] = [
                'employee_id' => $employee->id,
                'record_date' => $today->format('Y-m-d'),
                'record_time' => $lateEntryTime->format('H:i:s'),
                'full_datetime' => $lateEntryTime,
                'record_type' => 'entry',
                'identification_method' => 'manual',
                'nsr' => str_pad($nsrCounter++, 10, '0', STR_PAD_LEFT),
                'hash_verification' => hash('sha256', 'late_entry_' . $employee->id . '_' . $lateEntryTime->timestamp),
                'observations' => 'Atraso devido ao trânsito intenso',
                'status' => 'pending_approval',
                'created_at' => $lateEntryTime,
                'updated_at' => $lateEntryTime,
            ];
        }

        foreach ($records as $record) {
            TimeRecord::create($record);
        }

        $this->command->info('✅ Registros de ponto de exemplo criados: ' . count($records));
    }
}