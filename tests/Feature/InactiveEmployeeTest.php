<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use App\Models\WorkSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InactiveEmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_employee_cannot_login(): void
    {
        // Criar dados necessários
        $company = Company::factory()->create();
        $workSchedule = WorkSchedule::factory()->create(['company_id' => $company->id]);
        
        // Criar usuário
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Criar funcionário inativo
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'work_schedule_id' => $workSchedule->id,
            'active' => false, // Funcionário inativo
        ]);

        // Tentar fazer login
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Deve ser redirecionado de volta para login com erro
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Sua conta está inativa. Entre em contato com o RH.']);
        
        // Usuário não deve estar autenticado
        $this->assertGuest();
    }

    public function test_active_employee_can_login(): void
    {
        // Criar dados necessários
        $company = Company::factory()->create();
        $workSchedule = WorkSchedule::factory()->create(['company_id' => $company->id]);
        
        // Criar usuário
        $user = User::factory()->create([
            'email' => 'active@example.com',
            'password' => bcrypt('password'),
        ]);

        // Criar funcionário ativo
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'work_schedule_id' => $workSchedule->id,
            'active' => true, // Funcionário ativo
        ]);

        // Tentar fazer login
        $response = $this->post('/login', [
            'email' => 'active@example.com',
            'password' => 'password',
        ]);

        // Deve ser redirecionado para dashboard
        $response->assertRedirect('/dashboard');
        
        // Usuário deve estar autenticado
        $this->assertAuthenticated();
    }

    public function test_logged_in_inactive_employee_is_logged_out(): void
    {
        // Criar dados necessários
        $company = Company::factory()->create();
        $workSchedule = WorkSchedule::factory()->create(['company_id' => $company->id]);
        
        // Criar usuário
        $user = User::factory()->create();

        // Criar funcionário ativo inicialmente
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'work_schedule_id' => $workSchedule->id,
            'active' => true,
        ]);

        // Fazer login
        $this->actingAs($user);
        $this->assertAuthenticated();

        // Desativar o funcionário
        $employee->update(['active' => false]);

        // Tentar acessar uma página protegida
        $response = $this->get('/dashboard');

        // Deve ser redirecionado para login com mensagem de erro
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Sua conta foi desativada. Entre em contato com o RH.');
        
        // Usuário não deve mais estar autenticado
        $this->assertGuest();
    }

    public function test_user_without_employee_can_still_login(): void
    {
        // Criar usuário sem funcionário associado (ex: admin do sistema)
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Tentar fazer login
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Deve conseguir fazer login normalmente
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
