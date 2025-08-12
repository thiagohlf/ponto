<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absence;
use App\Models\Employee;
use Carbon\Carbon;

class AbsenceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        if ($employees->count() === 0) {
            $this->command->warn('Nenhum funcionário encontrado. Execute SystemSeeder primeiro.');
            return;
        }

        $absences = [
            [
                'employee_id' => $employees->first()->id,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->subDays(8),
                'total_days' => 3,
                'absence_type' => 'atestado_medico',
                'justification' => 'Atestado médico por gripe',
                'status' => 'approved',
                'paid_absence' => true,
                'discount_amount' => 0,
            ],
            [
                'employee_id' => $employees->skip(1)->first()->id,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->subDays(5),
                'total_days' => 1,
                'absence_type' => 'luto',
                'justification' => 'Falecimento de familiar próximo',
                'status' => 'approved',
                'paid_absence' => true,
                'discount_amount' => 0,
            ],
            [
                'employee_id' => $employees->skip(2)->first()->id,
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(7),
                'total_days' => 3,
                'absence_type' => 'ferias',
                'justification' => 'Férias programadas',
                'status' => 'pending',
                'paid_absence' => true,
                'discount_amount' => 0,
            ],
            [
                'employee_id' => $employees->skip(3)->first()->id,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->subDays(2),
                'total_days' => 1,
                'absence_type' => 'falta_injustificada',
                'justification' => 'Falta sem justificativa',
                'status' => 'approved',
                'paid_absence' => false,
                'discount_amount' => 150.00,
            ],
        ];

        foreach ($absences as $absence) {
            Absence::create($absence);
        }

        $this->command->info('✅ Ausências de exemplo criadas: ' . count($absences));
    }
}