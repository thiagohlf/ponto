<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
     * Realizar backup do banco de dados
     */
    public function createBackup()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_database_{$timestamp}.sql";

            // Obter configurações do banco
            $database = config('database.default');
            $connection = config("database.connections.{$database}");

            if ($database === 'mysql') {
                $backupPath = $this->createMysqlBackup($connection, $filename);
            } elseif ($database === 'sqlite') {
                $backupPath = $this->createSqliteBackup($connection, $filename);
            } else {
                throw new \Exception('Tipo de banco de dados não suportado para backup.');
            }

            // Limpar backups antigos
            $this->cleanOldBackups();

            return response()->download($backupPath)->deleteFileAfterSend(false);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao criar backup: ' . $e->getMessage());
        }
    }

    /**
     * Criar backup MySQL
     */
    private function createMysqlBackup($connection, $filename)
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $host = $connection['host'];
        $port = $connection['port'] ?? 3306;
        $database = $connection['database'];
        $username = $connection['username'];
        $password = $connection['password'] ?? '';

        // Tentar diferentes métodos de backup
        
        // Método 1: Usar mysqldump se disponível
        if ($this->isMysqldumpAvailable()) {
            return $this->createMysqlDumpBackup($host, $port, $database, $username, $password, $backupPath);
        }
        
        // Método 2: Backup usando PHP/PDO (fallback)
        return $this->createPhpMysqlBackup($connection, $backupPath);
    }

    /**
     * Verificar se mysqldump está disponível
     */
    private function isMysqldumpAvailable()
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump' : 'which mysqldump';
        exec($command, $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Criar backup usando mysqldump
     */
    private function createMysqlDumpBackup($host, $port, $database, $username, $password, $backupPath)
    {
        // Construir comando mysqldump compatível com Windows
        $command = 'mysqldump';
        $command .= ' --host=' . escapeshellarg($host);
        $command .= ' --port=' . escapeshellarg($port);
        $command .= ' --user=' . escapeshellarg($username);
        
        if (!empty($password)) {
            $command .= ' --password=' . escapeshellarg($password);
        }
        
        $command .= ' --single-transaction --routines --triggers --add-drop-table';
        $command .= ' ' . escapeshellarg($database);
        
        // No Windows, usar redirecionamento diferente
        if (PHP_OS_FAMILY === 'Windows') {
            $command .= ' > ' . escapeshellarg($backupPath);
        } else {
            $command .= ' > ' . escapeshellarg($backupPath);
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Falha ao executar mysqldump. Código de erro: ' . $returnCode . '. Output: ' . implode("\n", $output));
        }

        if (!file_exists($backupPath) || filesize($backupPath) === 0) {
            throw new \Exception('Arquivo de backup não foi criado ou está vazio.');
        }

        return $backupPath;
    }

    /**
     * Criar backup usando PHP/PDO (método alternativo)
     */
    private function createPhpMysqlBackup($connection, $backupPath)
    {
        try {
            $pdo = new \PDO(
                "mysql:host={$connection['host']};port={$connection['port']};dbname={$connection['database']}",
                $connection['username'],
                $connection['password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );

            $backup = "-- Backup MySQL gerado em " . date('Y-m-d H:i:s') . "\n";
            $backup .= "-- Banco de dados: {$connection['database']}\n\n";
            $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Obter todas as tabelas
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                // Estrutura da tabela
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $backup .= "-- Estrutura da tabela `{$table}`\n";
                $backup .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $backup .= $createTable['Create Table'] . ";\n\n";

                // Dados da tabela
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    $backup .= "-- Dados da tabela `{$table}`\n";
                    
                    foreach ($rows as $row) {
                        $values = array_map(function($value) use ($pdo) {
                            return $value === null ? 'NULL' : $pdo->quote($value);
                        }, array_values($row));
                        
                        $columns = '`' . implode('`, `', array_keys($row)) . '`';
                        $backup .= "INSERT INTO `{$table}` ({$columns}) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $backup .= "\n";
                }
            }

            $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";

            if (file_put_contents($backupPath, $backup) === false) {
                throw new \Exception('Não foi possível escrever o arquivo de backup.');
            }

            return $backupPath;

        } catch (\PDOException $e) {
            throw new \Exception('Erro na conexão com MySQL: ' . $e->getMessage());
        }
    }

    /**
     * Criar backup SQLite
     */
    private function createSqliteBackup($connection, $filename)
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backupPath = $backupDir . '/' . $filename;
        $databasePath = $connection['database'];

        if (!File::exists($databasePath)) {
            throw new \Exception('Arquivo de banco SQLite não encontrado.');
        }

        // Para SQLite, simplesmente copiamos o arquivo
        File::copy($databasePath, $backupPath);

        return $backupPath;
    }

    /**
     * Limpar backups antigos
     */
    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $retentionDays = config('app.backup_retention_days', 30);

        if (!File::exists($backupDir)) {
            return;
        }

        $files = File::files($backupDir);
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        foreach ($files as $file) {
            $fileTime = Carbon::createFromTimestamp(File::lastModified($file->getPathname()));

            if ($fileTime->lt($cutoffDate)) {
                File::delete($file->getPathname());
            }
        }
    }

    /**
     * Listar backups existentes
     */
    public function listBackups()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);

            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'created_at' => Carbon::createFromTimestamp(File::lastModified($file->getPathname())),
                    'path' => $file->getPathname()
                ];
            }

            // Ordenar por data de criação (mais recente primeiro)
            usort($backups, function ($a, $b) {
                return $b['created_at']->timestamp - $a['created_at']->timestamp;
            });
        }

        return response()->json($backups);
    }

    /**
     * Download de backup específico
     */
    public function downloadBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            return redirect()->back()->with('error', 'Arquivo de backup não encontrado.');
        }

        // Verificar se o arquivo está dentro do diretório de backups (segurança)
        $realBackupPath = realpath($backupPath);
        $realBackupDir = realpath(storage_path('app/backups'));

        if (!$realBackupPath || strpos($realBackupPath, $realBackupDir) !== 0) {
            return redirect()->back()->with('error', 'Acesso negado ao arquivo.');
        }

        return response()->download($backupPath);
    }

    /**
     * Formatar bytes em formato legível
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
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
