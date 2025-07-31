<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\TimeClock;
use App\Models\WorkSchedule;
use App\Models\Holiday;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // Criar empresa padrão
        $company = Company::create([
            'name' => 'Empresa Exemplo Ltda',
            'cnpj' => '12.345.678/0001-90',
            'address' => 'Rua das Empresas',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01234-567',
            'phone' => '(11) 1234-5678',
            'email' => 'contato@empresa.com.br',
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
                'description' => $dept['description'],
                'active' => true,
            ]);
        }

        // Criar usuários e funcionários
        $users = [
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@empresa.com.br',
                'role' => 'Administrador',
                'department' => 'Tecnologia da Informação',
                'position' => 'Administrador do Sistema',
                'employee_id' => '0001',
            ],
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
                'name' => $userData['name'],
                'cpf' => '000.000.000-0' . substr($userData['employee_id'], -1), // CPF fictício
                'registration_number' => $userData['employee_id'],
                'admission_date' => Carbon::now()->subMonths(rand(1, 24)),
                'position' => $userData['position'],
                'salary' => rand(3000, 15000),
                'email' => $userData['email'],
                'active' => true,
            ]);
        }

        // Criar relógios de ponto
        $timeClocks = [
            [
                'name' => 'Relógio Entrada Principal',
                'location' => 'Recepção - Térreo',
                'ip_address' => '192.168.1.100',
                'serial_number' => 'REP001',
            ],
            [
                'name' => 'Relógio Refeitório',
                'location' => 'Refeitório - 2º Andar',
                'ip_address' => '192.168.1.101',
                'serial_number' => 'REF001',
            ],
            [
                'name' => 'Relógio Produção',
                'location' => 'Área de Produção',
                'ip_address' => '192.168.1.102',
                'serial_number' => 'PROD001',
            ],
        ];

        foreach ($timeClocks as $clock) {
            TimeClock::create([
                'company_id' => $company->id,
                'name' => $clock['name'],
                'location' => $clock['location'],
                'ip_address' => $clock['ip_address'],
                'serial_number' => $clock['serial_number'],
                'active' => true,
            ]);
        }

        // Criar escala de trabalho padrão
        WorkSchedule::create([
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
                'company_id' => $company->id,
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'year' => 2025,
                'type' => 'national',
                'active' => true,
            ]);
        }

        $this->command->info('Sistema inicializado com dados de exemplo!');
        $this->command->info('Usuários criados:');
        $this->command->info('- admin@empresa.com.br (Administrador) - senha: password123');
        $this->command->info('- maria.silva@empresa.com.br (RH) - senha: password123');
        $this->command->info('- joao.santos@empresa.com.br (Supervisor) - senha: password123');
        $this->command->info('- ana.costa@empresa.com.br (Funcionário) - senha: password123');
        $this->command->info('- carlos.oliveira@empresa.com.br (Técnico) - senha: password123');
    }
}
