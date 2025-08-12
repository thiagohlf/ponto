<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Overtime;
use App\Models\Employee;
use Carbon\Carbon;

class OvertimeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        if ($employees->count() === 0) {
            $this->command->warn('Nenhum funcionário encontrado. Execute SystemSeeder primeiro.');
            return;
        }

        $overtimes = [
            [
                'employee_id' => $employees->first()->id,
                'work_date' => Carbon::now()->subDays(3),
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'total_minutes' => 120,
                'overtime_type' => 'hora_extra_diaria',
                'hourly_rate' => 25.00,
                'overtime_multiplier' => 1.50,
                'calculated_amount' => 75.00,
                'night_shift_applicable' => false,
                'night_shift_minutes' => 0,
                'justification' => 'Finalização de projeto urgente',
                'compensatory_time' => false,
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->skip(1)->first()->id,
                'work_date' => Carbon::now()->subDays(1),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'total_minutes' => 480,
                'overtime_type' => 'trabalho_fim_semana',
                'hourly_rate' => 20.00,
                'overtime_multiplier' => 2.00,
                'calculated_amount' => 320.00,
                'night_shift_applicable' => false,
                'night_shift_minutes' => 0,
                'justification' => 'Trabalho em sábado por demanda especial',
                'compensatory_time' => false,
                'status' => 'pending',
            ],
            [
                'employee_id' => $employees->skip(2)->first()->id,
                'work_date' => Carbon::now()->subDays(7),
                'start_time' => '22:00:00',
                'end_time' => '02:00:00',
                'total_minutes' => 240,
                'overtime_type' => 'adicional_noturno',
                'hourly_rate' => 18.00,
                'overtime_multiplier' => 1.50,
                'calculated_amount' => 108.00,
                'night_shift_applicable' => true,
                'night_shift_minutes' => 240,
                'night_shift_percentage' => 20.00,
                'justification' => 'Plantão noturno de manutenção',
                'compensatory_time' => true,
                'compensation_deadline' => Carbon::now()->addDays(30),
                'compensated' => false,
                'status' => 'approved',
            ],
            [
                'employee_id' => $employees->skip(3)->first()->id,
                'work_date' => Carbon::now()->subDays(5),
                'start_time' => '17:00:00',
                'end_time' => '19:30:00',
                'total_minutes' => 150,
                'overtime_type' => 'banco_horas',
                'hourly_rate' => 22.00,
                'overtime_multiplier' => 1.00,
                'calculated_amount' => 55.00,
                'night_shift_applicable' => false,
                'night_shift_minutes' => 0,
                'justification' => 'Horas para banco de horas',
                'compensatory_time' => true,
                'compensation_deadline' => Carbon::now()->addDays(60),
                'compensated' => false,
                'status' => 'approved',
            ],
        ];

        foreach ($overtimes as $overtime) {
            Overtime::create($overtime);
        }

        $this->command->info('✅ Horas extras de exemplo criadas: ' . count($overtimes));
    }
}