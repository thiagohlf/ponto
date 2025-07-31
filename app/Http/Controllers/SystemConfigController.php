<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:configurar_sistema']);
    }

    /**
     * Exibir configurações do sistema
     */
    public function index()
    {
        $configs = [
            'registration_enabled' => config('app.registration_enabled', true),
            'backup_enabled' => config('app.backup_enabled', true),
            'backup_retention_days' => config('app.backup_retention_days', 30),
        ];

        return view('system.config.index', compact('configs'));
    }

    /**
     * Atualizar configurações do sistema
     */
    public function update(Request $request)
    {
        $request->validate([
            'registration_enabled' => 'boolean',
            'backup_enabled' => 'boolean',
            'backup_retention_days' => 'integer|min:1|max:365',
        ]);

        // Atualizar arquivo .env
        $this->updateEnvFile([
            'REGISTRATION_ENABLED' => $request->boolean('registration_enabled') ? 'true' : 'false',
            'BACKUP_ENABLED' => $request->boolean('backup_enabled') ? 'true' : 'false',
            'BACKUP_RETENTION_DAYS' => $request->input('backup_retention_days', 30),
        ]);

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Alternar status do registro
     */
    public function toggleRegistration()
    {
        $currentStatus = config('app.registration_enabled', true);
        $newStatus = !$currentStatus;

        $this->updateEnvFile([
            'REGISTRATION_ENABLED' => $newStatus ? 'true' : 'false'
        ]);

        $message = $newStatus ? 'Registro de novos usuários ativado!' : 'Registro de novos usuários desativado!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Atualizar arquivo .env
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envFile, $envContent);

        // Limpar cache de configuração
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}