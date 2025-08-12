<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_redirects_when_disabled(): void
    {
        // Se o registro estiver desabilitado, deve redirecionar para login
        if (!config('app.registration_enabled', true)) {
            $response = $this->get('/register');
            $response->assertRedirect(route('login'));
            return;
        }

        // Se estiver habilitado, deve mostrar a tela de registro
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_new_users_can_register_when_enabled(): void
    {
        // Se o registro estiver desabilitado, pular este teste
        if (!config('app.registration_enabled', true)) {
            $this->markTestSkipped('Registration is disabled');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_post_redirects_when_disabled(): void
    {
        // Se o registro estiver desabilitado, POST também deve redirecionar
        if (!config('app.registration_enabled', true)) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
            
            $response->assertRedirect(route('login'));
            $this->assertGuest();
            return;
        }

        $this->markTestSkipped('Registration is enabled, this test is for disabled registration');
    }
}
