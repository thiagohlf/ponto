<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;

use App\Models\WorkSchedule;
use App\Models\Holiday;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário administrador primeiro (para auditoria)
        $adminUser = User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@empresa.com.br',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
        ]);
        $adminUser->assignRole('Administrador');

        // Criar empresa padrão
        $company = Company::create([
            'name' => 'Empresa Exemplo Ltda',
            'trade_name' => 'Exemplo Corp',
            'cnpj' => '12.345.678/0001-90',
            'state_registration' => '123456789',
            'municipal_registration' => '987654321',
            'address_data' => [
                'street' => 'Rua das Empresas',
                'number' => '123',
                'complement' => 'Sala 101',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01234-567'
            ],
            'contact_data' => [
                'phone' => '(11) 1234-5678',
                'email' => 'contato@empresa.com.br',
                'mobile' => '(11) 99999-9999'
            ],
            'requires_justification' => true,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
            'active' => true,
        ]);

        // Criar departamentos
        $departments = [
            ['name' => 'Recursos Humanos', 'description' => 'Departamento de gestão de pessoas'],
            ['name' => 'Tecnologia da Informação', 'description' => 'Departamento de TI'],
            ['name' => 'Financeiro', 'description' => 'Departamento financeiro'],
            ['name' => 'Operações', 'description' => 'Departamento operacional'],
            ['name' => 'Vendas', 'description' => 'Departamento comercial'],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'company_id' => $company->id,
                'name' => $dept['name'],
                'code' => strtoupper(substr($dept['name'], 0, 3)),
                'description' => $dept['description'],
                'active' => true,
            ]);
        }

        // Criar horário de trabalho padrão primeiro
        $workSchedule = WorkSchedule::create([
            'company_id' => $company->id,
            'name' => 'Horário Comercial',
            'description' => 'Segunda a Sexta, 8h às 18h com 1h de almoço',
            'weekly_hours' => 44,
            'daily_hours' => 8,
            'monday_schedule' => json_encode(['entry' => '08:00', 'exit' => '17:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'tuesday_schedule' => json_encode(['entry' => '08:00', 'exit' => '17:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'wednesday_schedule' => json_encode(['entry' => '08:00', 'exit' => '17:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'thursday_schedule' => json_encode(['entry' => '08:00', 'exit' => '17:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'friday_schedule' => json_encode(['entry' => '08:00', 'exit' => '17:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'has_meal_break' => true,
            'meal_break_duration' => 60,
            'meal_break_start' => '12:00:00',
            'meal_break_end' => '13:00:00',
            'entry_tolerance' => 10,
            'exit_tolerance' => 10,
            'general_tolerance' => 10,
            'allows_overtime' => true,
            'max_daily_overtime' => 120,
            'compensatory_time' => false,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
            'active' => true,
        ]);

        // Criar usuários e funcionários
        $users = [
            [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@empresa.com.br',
                'role' => 'RH',
                'department' => 'Recursos Humanos',
                'position' => 'Gerente de RH',
                'employee_id' => '0002',
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao.santos@empresa.com.br',
                'role' => 'Supervisor',
                'department' => 'Operações',
                'position' => 'Supervisor de Operações',
                'employee_id' => '0003',
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@empresa.com.br',
                'role' => 'Funcionário',
                'department' => 'Vendas',
                'position' => 'Vendedora',
                'employee_id' => '0004',
            ],
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos.oliveira@empresa.com.br',
                'role' => 'Técnico',
                'department' => 'Tecnologia da Informação',
                'position' => 'Técnico em TI',
                'employee_id' => '0005',
            ],
        ];

        // Criar funcionário para o administrador
        $department = Department::where('name', 'Tecnologia da Informação')->first();
        Employee::create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'user_id' => $adminUser->id,
            'work_schedule_id' => $workSchedule->id,
            'cpf' => '000.000.000-01',
            'rg' => '12.345.678-9',
            'birth_date' => '1985-01-15',
            'gender' => 'M',
            'registration_number' => '0001',
            'pis_pasep' => '123.45678.90-1',
            'admission_date' => Carbon::now()->subMonths(12),
            'position' => 'Administrador do Sistema',
            'salary' => 8000.00,
            'address_data' => [
                'street' => 'Rua dos Administradores',
                'number' => '100',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01000-000'
            ],
            'exempt_time_control' => false,
            'active' => true,
        ]);

        foreach ($users as $userData) {
            // Criar usuário
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]);

            // Atribuir role
            $user->assignRole($userData['role']);

            // Criar funcionário
            $department = Department::where('name', $userData['department'])->first();

            Employee::create([
                'company_id' => $company->id,
                'department_id' => $department->id,
                'user_id' => $user->id,
                'work_schedule_id' => $workSchedule->id,
                'cpf' => '000.000.000-0' . substr($userData['employee_id'], -1),
                'rg' => '12.345.678-' . substr($userData['employee_id'], -1),
                'birth_date' => Carbon::now()->subYears(rand(25, 50))->format('Y-m-d'),
                'gender' => rand(0, 1) ? 'M' : 'F',
                'registration_number' => $userData['employee_id'],
                'pis_pasep' => '123.45678.90-' . substr($userData['employee_id'], -1),
                'admission_date' => Carbon::now()->subMonths(rand(1, 24)),
                'position' => $userData['position'],
                'salary' => rand(3000, 15000),
                'address_data' => [
                    'street' => 'Rua dos Funcionários',
                    'number' => $userData['employee_id'] . '0',
                    'neighborhood' => 'Vila Exemplo',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zip_code' => '01234-567'
                ],
                'exempt_time_control' => false,
                'active' => true,
            ]);
        }

        // Criar horário de trabalho alternativo
        WorkSchedule::create([
            'company_id' => $company->id,
            'name' => 'Horário Flexível',
            'description' => 'Horário flexível com 30 minutos de tolerância',
            'weekly_hours' => 40,
            'daily_hours' => 8,
            'monday_schedule' => json_encode(['entry' => '09:00', 'exit' => '18:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'tuesday_schedule' => json_encode(['entry' => '09:00', 'exit' => '18:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'wednesday_schedule' => json_encode(['entry' => '09:00', 'exit' => '18:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'thursday_schedule' => json_encode(['entry' => '09:00', 'exit' => '18:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'friday_schedule' => json_encode(['entry' => '09:00', 'exit' => '18:00', 'meal_start' => '12:00', 'meal_end' => '13:00']),
            'has_meal_break' => true,
            'meal_break_duration' => 60,
            'meal_break_start' => '12:00:00',
            'meal_break_end' => '13:00:00',
            'entry_tolerance' => 30,
            'exit_tolerance' => 30,
            'general_tolerance' => 30,
            'flexible_schedule' => true,
            'flexible_minutes' => 30,
            'allows_overtime' => true,
            'max_daily_overtime' => 120,
            'compensatory_time' => true,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
            'active' => true,
        ]);

        // Criar feriados nacionais de 2025
        $holidays = [
            ['name' => 'Confraternização Universal', 'date' => '2025-01-01'],
            ['name' => 'Carnaval', 'date' => '2025-03-03'],
            ['name' => 'Carnaval', 'date' => '2025-03-04'],
            ['name' => 'Sexta-feira Santa', 'date' => '2025-04-18'],
            ['name' => 'Tiradentes', 'date' => '2025-04-21'],
            ['name' => 'Dia do Trabalhador', 'date' => '2025-05-01'],
            ['name' => 'Independência do Brasil', 'date' => '2025-09-07'],
            ['name' => 'Nossa Senhora Aparecida', 'date' => '2025-10-12'],
            ['name' => 'Finados', 'date' => '2025-11-02'],
            ['name' => 'Proclamação da República', 'date' => '2025-11-15'],
            ['name' => 'Natal', 'date' => '2025-12-25'],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create([
                'company_id' => null, // Feriados nacionais não pertencem a uma empresa específica
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'year' => 2025,
                'type' => 'nacional',
                'is_fixed' => true,
                'is_recurring' => true,
                'mandatory_rest' => true,
                'allows_work' => false,
                'work_multiplier' => 2.00,
                'active' => true,
            ]);
        }

        $this->command->info('✅ Sistema inicializado com dados de exemplo!');
        $this->command->info('');
        $this->command->info('🏢 Empresa: Empresa Exemplo Ltda');
        $this->command->info('⏰ Horários de trabalho: 2 horários criados');
        $this->command->info('🏛️ Departamentos: 5 departamentos criados');
        $this->command->info('🎉 Feriados: 11 feriados nacionais de 2025');
        $this->command->info('');
        $this->command->info('👥 Usuários criados (senha: password123):');
        $this->command->info('- admin@empresa.com.br (Administrador)');
        $this->command->info('- maria.silva@empresa.com.br (RH)');
        $this->command->info('- joao.santos@empresa.com.br (Supervisor)');
        $this->command->info('- ana.costa@empresa.com.br (Funcionário)');
        $this->command->info('- carlos.oliveira@empresa.com.br (Técnico)');
        $this->command->info('');
        $this->command->info('🚀 Sistema pronto para uso!');
    }
}
