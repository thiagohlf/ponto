<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalCertificate;
use App\Models\Employee;
use Carbon\Carbon;

class MedicalCertificateSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        
        if ($employees->count() === 0) {
            $this->command->warn('Nenhum funcionário encontrado. Execute SystemSeeder primeiro.');
            return;
        }

        // Criar alguns atestados médicos de exemplo
        $certificates = [
            [
                'employee_id' => $employees->first()->id,
                'document_number' => 'ATM-2025-001',
                'document_type' => 'atestado_medico',
                'doctor_name' => 'Dr. João Silva',
                'doctor_crm' => '12345-SP',
                'medical_code' => 'Z76.1',
                'medical_description' => 'Repouso por gripe',
                'issue_date' => Carbon::now()->subDays(5),
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(2),
                'total_days' => 5,
                'status' => 'active',
            ],
            [
                'employee_id' => $employees->skip(1)->first()->id,
                'document_number' => 'ATM-2025-002',
                'document_type' => 'atestado_medico',
                'doctor_name' => 'Dra. Maria Santos',
                'doctor_crm' => '67890-SP',
                'medical_code' => 'M79.3',
                'medical_description' => 'Lesão muscular - repouso',
                'issue_date' => Carbon::now()->subDays(10),
                'start_date' => Carbon::now()->subDays(8),
                'end_date' => Carbon::now()->subDays(3),
                'total_days' => 5,
                'status' => 'expired',
            ],
            [
                'employee_id' => $employees->skip(2)->first()->id,
                'document_number' => 'LAU-2025-001',
                'document_type' => 'laudo_medico',
                'doctor_name' => 'Dr. Carlos Oliveira',
                'doctor_crm' => '11111-RJ',
                'medical_code' => 'H10.9',
                'medical_description' => 'Conjuntivite - tratamento',
                'issue_date' => Carbon::now()->subDays(2),
                'start_date' => Carbon::now()->subDays(1),
                'end_date' => Carbon::now()->addDays(3),
                'total_days' => 4,
                'status' => 'active',
            ],
        ];

        foreach ($certificates as $cert) {
            MedicalCertificate::create($cert);
        }

        $this->command->info('✅ Atestados médicos de exemplo criados: ' . count($certificates));
    }
}