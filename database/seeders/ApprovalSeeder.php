<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\User;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\MedicalCertificate;

class ApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $absences = Absence::all();
        $overtimes = Overtime::all();
        $certificates = MedicalCertificate::all();
        
        if ($users->count() < 2) {
            $this->command->warn('Poucos usuários encontrados. Execute SystemSeeder primeiro.');
            return;
        }

        $supervisor = $users->whereIn('email', ['admin@empresa.com.br', 'maria.silva@empresa.com.br'])->first();
        $employee = $users->where('email', 'ana.costa@empresa.com.br')->first();

        if (!$supervisor || !$employee) {
            $this->command->warn('Usuários específicos não encontrados.');
            return;
        }

        $approvals = [];

        // Aprovações para atestados médicos
        foreach ($certificates as $certificate) {
            $approvals[] = [
                'approvable_type' => MedicalCertificate::class,
                'approvable_id' => $certificate->id,
                'status' => 'approved',
                'type' => 'medical_certificate',
                'requested_by' => $employee->id,
                'requested_at' => $certificate->issue_date,
                'approved_by' => $supervisor->id,
                'approved_at' => $certificate->issue_date->addHours(2),
                'request_justification' => 'Solicitação de aprovação de atestado médico',
                'approval_notes' => 'Atestado válido, aprovado conforme documentação médica.',
                'priority' => 3,
                'deadline' => $certificate->issue_date->addDays(1),
            ];
        }

        // Aprovações para ausências
        foreach ($absences as $absence) {
            $status = rand(0, 2);
            $statusOptions = ['pending', 'approved', 'rejected'];
            
            $approval = [
                'approvable_type' => Absence::class,
                'approvable_id' => $absence->id,
                'status' => $statusOptions[$status],
                'type' => 'absence',
                'requested_by' => $employee->id,
                'requested_at' => $absence->start_date->subDays(1),
                'priority' => 2,
                'deadline' => $absence->start_date,
                'request_justification' => 'Solicitação de ausência por motivo pessoal',
            ];

            if ($status > 0) { // Aprovado ou rejeitado
                $approval['approved_by'] = $supervisor->id;
                $approval['approved_at'] = $absence->start_date->subHours(rand(1, 12));
                
                if ($status === 1) { // Aprovado
                    $approval['approval_notes'] = 'Ausência aprovada conforme justificativa apresentada.';
                } else { // Rejeitado
                    $approval['rejection_reason'] = 'Documentação insuficiente para aprovação da ausência.';
                }
            }

            $approvals[] = $approval;
        }

        // Aprovações para horas extras
        foreach ($overtimes as $overtime) {
            $status = rand(0, 2);
            $statusOptions = ['pending', 'approved', 'rejected'];
            
            $approval = [
                'approvable_type' => Overtime::class,
                'approvable_id' => $overtime->id,
                'status' => $statusOptions[$status],
                'type' => 'overtime',
                'requested_by' => $employee->id,
                'requested_at' => $overtime->work_date->subDays(1),
                'priority' => 1,
                'deadline' => $overtime->work_date->addDays(3),
                'request_justification' => 'Solicitação de aprovação de horas extras por demanda do projeto',
                'metadata' => [
                    'overtime_type' => $overtime->overtime_type,
                    'total_minutes' => $overtime->total_minutes,
                    'calculated_amount' => $overtime->calculated_amount,
                ],
            ];

            if ($status > 0) { // Aprovado ou rejeitado
                $approval['approved_by'] = $supervisor->id;
                $approval['approved_at'] = $overtime->work_date->addHours(rand(1, 24));
                
                if ($status === 1) { // Aprovado
                    $approval['approval_notes'] = 'Horas extras aprovadas conforme necessidade do projeto.';
                } else { // Rejeitado
                    $approval['rejection_reason'] = 'Horas extras não autorizadas previamente.';
                }
            }

            $approvals[] = $approval;
        }

        foreach ($approvals as $approval) {
            Approval::create($approval);
        }

        $this->command->info('✅ Aprovações de exemplo criadas: ' . count($approvals));
    }
}