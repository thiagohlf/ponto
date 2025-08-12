<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DepartmentRoleSeeder extends Seeder
{
    /**
     * Seeder para criar uma integração inteligente entre departamentos e roles
     * Atribui roles baseados no departamento, mas mantém a flexibilidade
     */
    public function run(): void
    {
        // Mapeamento sugerido de departamento -> role padrão
        $departmentRoleMapping = [
            'Recursos Humanos' => 'RH',
            'Tecnologia da Informação' => 'Técnico',
            'Financeiro' => 'Supervisor',
            'Operações' => 'Supervisor',
            'Vendas' => 'Funcionário',
        ];

        $departments = Department::all();
        
        foreach ($departments as $department) {
            $employees = $department->employees()->with('user')->get();
            
            foreach ($employees as $employee) {
                if ($employee->user) {
                    $suggestedRole = $departmentRoleMapping[$department->name] ?? 'Funcionário';
                    
                    // Só atribui se o usuário não tiver nenhum role ainda
                    if (!$employee->user->hasAnyRole(Role::all())) {
                        $employee->user->assignRole($suggestedRole);
                        
                        $this->command->info("✅ {$employee->user->name} ({$department->name}) -> Role: {$suggestedRole}");
                    }
                }
            }
        }
        
        $this->command->info('🔗 Integração departamento-role concluída!');
    }
}