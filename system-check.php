<?php

/**
 * Sistema de Verificação - Ponto Eletrônico
 * Verifica se todas as configurações estão corretas
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================\n";
echo "  VERIFICAÇÃO DO SISTEMA DE PONTO\n";
echo "========================================\n\n";

try {
    // Inicializar Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $errors = 0;
    $warnings = 0;

    // 1. Verificar arquivo .env
    echo "[1/10] Verificando arquivo .env...\n";
    if (!file_exists('.env')) {
        echo "❌ Arquivo .env não encontrado\n";
        $errors++;
    } else {
        echo "✅ Arquivo .env encontrado\n";
    }

    // 2. Verificar APP_KEY
    echo "[2/10] Verificando APP_KEY...\n";
    if (empty(env('APP_KEY'))) {
        echo "⚠️  APP_KEY não configurada - execute: php artisan key:generate\n";
        $warnings++;
    } else {
        echo "✅ APP_KEY configurada\n";
    }

    // 3. Verificar conexão com banco de dados
    echo "[3/10] Verificando conexão com banco de dados...\n";
    try {
        DB::connection()->getPdo();
        echo "✅ Conexão com banco de dados OK\n";
    } catch (Exception $e) {
        echo "❌ Erro na conexão com banco: " . $e->getMessage() . "\n";
        $errors++;
    }

    // 4. Verificar se as migrações foram executadas
    echo "[4/10] Verificando migrações...\n";
    try {
        $tables = [
            'users', 'companies', 'departments', 'employees', 
            'time_clocks', 'time_records', 'work_schedules',
            'absences', 'overtime', 'holidays', 'permissions', 'roles'
        ];
        
        $missingTables = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            echo "✅ Todas as tabelas estão criadas\n";
        } else {
            echo "❌ Tabelas não encontradas: " . implode(', ', $missingTables) . "\n";
            echo "   Execute: php artisan migrate\n";
            $errors++;
        }
    } catch (Exception $e) {
        echo "❌ Erro ao verificar tabelas: " . $e->getMessage() . "\n";
        $errors++;
    }

    // 5. Verificar se os seeders foram executados
    echo "[5/10] Verificando dados iniciais...\n";
    try {
        $userCount = DB::table('users')->count();
        $roleCount = DB::table('roles')->count();
        $permissionCount = DB::table('permissions')->count();
        
        if ($userCount > 0 && $roleCount > 0 && $permissionCount > 0) {
            echo "✅ Dados iniciais encontrados ($userCount usuários, $roleCount roles, $permissionCount permissões)\n";
        } else {
            echo "⚠️  Poucos dados iniciais - execute: php artisan db:seed\n";
            $warnings++;
        }
    } catch (Exception $e) {
        echo "❌ Erro ao verificar dados: " . $e->getMessage() . "\n";
        $errors++;
    }

    // 6. Verificar estrutura de arquivos
    echo "[6/10] Verificando estrutura de arquivos...\n";
    $requiredFiles = [
        'app/Models/User.php',
        'app/Models/Company.php',
        'app/Models/Employee.php',
        'app/Http/Controllers/DashboardController.php',
        'routes/web.php',
        'resources/views/dashboard.blade.php'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            $missingFiles[] = $file;
        }
    }
    
    if (empty($missingFiles)) {
        echo "✅ Estrutura de arquivos OK\n";
    } else {
        echo "❌ Arquivos não encontrados: " . implode(', ', $missingFiles) . "\n";
        $errors++;
    }

    // 7. Verificar permissões de pastas
    echo "[7/10] Verificando permissões de pastas...\n";
    $writableDirs = ['storage', 'bootstrap/cache'];
    $permissionIssues = [];
    
    foreach ($writableDirs as $dir) {
        if (!is_writable($dir)) {
            $permissionIssues[] = $dir;
        }
    }
    
    if (empty($permissionIssues)) {
        echo "✅ Permissões de pastas OK\n";
    } else {
        echo "⚠️  Pastas sem permissão de escrita: " . implode(', ', $permissionIssues) . "\n";
        echo "   Execute: chmod -R 775 " . implode(' ', $permissionIssues) . "\n";
        $warnings++;
    }

    // 8. Verificar configuração Docker
    echo "[8/10] Verificando configuração Docker...\n";
    $dockerFiles = [
        'docker-compose.yml',
        'Dockerfile',
        'docker/php/Dockerfile',
        'docker/nginx/nginx.conf',
        'docker/mysql/my.cnf',
        'docker/redis/redis.conf'
    ];
    
    $missingDockerFiles = [];
    foreach ($dockerFiles as $file) {
        if (!file_exists($file)) {
            $missingDockerFiles[] = $file;
        }
    }
    
    if (empty($missingDockerFiles)) {
        echo "✅ Configuração Docker completa\n";
    } else {
        echo "⚠️  Arquivos Docker não encontrados: " . implode(', ', $missingDockerFiles) . "\n";
        $warnings++;
    }

    // 9. Verificar dependências do Composer
    echo "[9/10] Verificando dependências...\n";
    if (file_exists('vendor/autoload.php')) {
        echo "✅ Dependências do Composer instaladas\n";
    } else {
        echo "❌ Dependências não instaladas - execute: composer install\n";
        $errors++;
    }

    // 10. Verificar configurações específicas
    echo "[10/10] Verificando configurações específicas...\n";
    $configs = [
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'CACHE_STORE' => env('CACHE_STORE'),
        'SESSION_DRIVER' => env('SESSION_DRIVER'),
        'QUEUE_CONNECTION' => env('QUEUE_CONNECTION')
    ];
    
    $configIssues = [];
    foreach ($configs as $key => $value) {
        if (empty($value)) {
            $configIssues[] = $key;
        }
    }
    
    if (empty($configIssues)) {
        echo "✅ Configurações específicas OK\n";
    } else {
        echo "⚠️  Configurações não definidas: " . implode(', ', $configIssues) . "\n";
        $warnings++;
    }

    // Resumo final
    echo "\n========================================\n";
    echo "  RESUMO DA VERIFICAÇÃO\n";
    echo "========================================\n";
    
    if ($errors === 0 && $warnings === 0) {
        echo "🎉 SISTEMA TOTALMENTE FUNCIONAL!\n";
        echo "✅ Todas as verificações passaram\n";
        echo "\nPara iniciar o sistema:\n";
        echo "- Desenvolvimento: php artisan serve\n";
        echo "- Docker: docker-start.bat\n";
    } elseif ($errors === 0) {
        echo "✅ SISTEMA FUNCIONAL COM AVISOS\n";
        echo "⚠️  $warnings avisos encontrados\n";
        echo "O sistema pode funcionar, mas recomenda-se corrigir os avisos.\n";
    } else {
        echo "❌ SISTEMA COM PROBLEMAS\n";
        echo "❌ $errors erros críticos encontrados\n";
        echo "⚠️  $warnings avisos encontrados\n";
        echo "Corrija os erros antes de usar o sistema.\n";
    }

    echo "\nComandos úteis:\n";
    echo "- php artisan key:generate    (gerar chave da aplicação)\n";
    echo "- php artisan migrate         (executar migrações)\n";
    echo "- php artisan db:seed         (popular banco com dados)\n";
    echo "- php artisan config:cache    (otimizar configurações)\n";
    echo "- php artisan route:cache     (otimizar rotas)\n";
    echo "- php artisan view:cache      (otimizar views)\n";

} catch (Exception $e) {
    echo "❌ ERRO CRÍTICO: " . $e->getMessage() . "\n";
    echo "Verifique se o Laravel está instalado corretamente.\n";
}

echo "\n========================================\n";