<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            // Empresas
            'gerenciar_empresas',
            'visualizar_empresas',
            
            // Departamentos
            'gerenciar_departamentos',
            'visualizar_departamentos',
            
            // Funcionários
            'gerenciar_funcionarios',
            'visualizar_funcionarios',
            
            // Relógios de ponto
            'gerenciar_relogios',
            'visualizar_relogios',
            
            // Registros de ponto
            'gerenciar_registros_ponto',
            'visualizar_registros_ponto',
            'aprovar_registros_ponto',
            'registrar_ponto_proprio',
            
            // Escalas de trabalho
            'gerenciar_escalas',
            'visualizar_escalas',
            
            // Ausências
            'gerenciar_ausencias',
            'visualizar_ausencias',
            'solicitar_ausencias',
            'aprovar_ausencias',
            
            // Horas extras
            'gerenciar_horas_extras',
            'visualizar_horas_extras',
            'solicitar_horas_extras',
            'aprovar_horas_extras',
            
            // Feriados
            'gerenciar_feriados',
            'visualizar_feriados',
            
            // Relatórios
            'visualizar_relatorios',
            'exportar_relatorios',
            'relatorios_avancados',
            
            // Sistema
            'gerenciar_usuarios',
            'gerenciar_permissoes',
            'configurar_sistema',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $rhRole = Role::firstOrCreate(['name' => 'RH']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $funcionarioRole = Role::firstOrCreate(['name' => 'Funcionário']);
        $tecnicoRole = Role::firstOrCreate(['name' => 'Técnico']);

        // Atribuir permissões aos roles
        
        // Administrador - todas as permissões
        $adminRole->givePermissionTo(Permission::all());

        // RH - gerenciamento de pessoas e relatórios
        $rhRole->givePermissionTo([
            'visualizar_empresas',
            'gerenciar_departamentos',
            'visualizar_departamentos',
            'gerenciar_funcionarios',
            'visualizar_funcionarios',
            'visualizar_relogios',
            'gerenciar_registros_ponto',
            'visualizar_registros_ponto',
            'aprovar_registros_ponto',
            'gerenciar_escalas',
            'visualizar_escalas',
            'gerenciar_ausencias',
            'visualizar_ausencias',
            'aprovar_ausencias',
            'gerenciar_horas_extras',
            'visualizar_horas_extras',
            'aprovar_horas_extras',
            'gerenciar_feriados',
            'visualizar_feriados',
            'visualizar_relatorios',
            'exportar_relatorios',
            'relatorios_avancados',
        ]);

        // Supervisor - gerenciamento da equipe
        $supervisorRole->givePermissionTo([
            'visualizar_empresas',
            'visualizar_departamentos',
            'visualizar_funcionarios',
            'visualizar_relogios',
            'gerenciar_registros_ponto',
            'visualizar_registros_ponto',
            'aprovar_registros_ponto',
            'visualizar_escalas',
            'gerenciar_ausencias',
            'visualizar_ausencias',
            'aprovar_ausencias',
            'gerenciar_horas_extras',
            'visualizar_horas_extras',
            'aprovar_horas_extras',
            'visualizar_feriados',
            'visualizar_relatorios',
            'exportar_relatorios',
        ]);

        // Técnico - gerenciamento de equipamentos
        $tecnicoRole->givePermissionTo([
            'gerenciar_relogios',
            'visualizar_relogios',
            'visualizar_registros_ponto',
            'visualizar_funcionarios',
            'visualizar_departamentos',
            'visualizar_empresas',
        ]);

        // Funcionário - operações básicas
        $funcionarioRole->givePermissionTo([
            'visualizar_registros_ponto',
            'registrar_ponto_proprio',
            'visualizar_ausencias',
            'solicitar_ausencias',
            'visualizar_horas_extras',
            'solicitar_horas_extras',
            'visualizar_feriados',
            'visualizar_escalas',
        ]);
    }
}