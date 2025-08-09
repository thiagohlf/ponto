<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TimeClockController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TimeClockApiController;
use Illuminate\Support\Facades\Route;

// Página inicial - redireciona para dashboard se autenticado
Route::get('/', function () {
    return auth()->user() ? redirect()->route('dashboard') : view('welcome');
});

// Dashboard principal
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Perfil do usuário (todos podem acessar)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // === EMPRESAS === (Apenas administradores)
    Route::middleware(['permission:gerenciar_empresas'])->group(function () {
        Route::resource('companies', CompanyController::class);
    });

    // === DEPARTAMENTOS === (Administradores e RH)
    Route::middleware(['permission:gerenciar_departamentos'])->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    // === FUNCIONÁRIOS === 
    // Criação, edição e exclusão de funcionários (apenas admin e RH) - DEVE VIR PRIMEIRO
    Route::middleware(['permission:gerenciar_funcionarios'])->group(function () {
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Visualização de funcionários (Administradores, RH e Supervisores)
    Route::middleware(['permission:gerenciar_funcionarios|visualizar_funcionarios'])->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');

        // Rotas específicas de funcionários
        Route::prefix('employees/{employee}')->name('employees.')->group(function () {
            Route::get('/time-records', [EmployeeController::class, 'timeRecords'])->name('time-records');
            Route::get('/absences', [EmployeeController::class, 'absences'])->name('absences');
            Route::get('/overtime', [EmployeeController::class, 'overtime'])->name('overtime');
        });
    });

    // === RELÓGIOS DE PONTO === (Administradores e Técnicos)
    Route::middleware(['permission:gerenciar_relogios'])->group(function () {
        Route::resource('time-clocks', TimeClockController::class);
    });

    // === REGISTROS DE PONTO ===
    // Solicitação de ajuste (todos os usuários autenticados podem solicitar) - deve vir antes das rotas com parâmetros
    Route::get('time-records/create', [TimeRecordController::class, 'create'])->name('time-records.create');
    Route::post('time-records', [TimeRecordController::class, 'store'])->name('time-records.store');

    // Visualização (todos os usuários autenticados)
    Route::middleware(['permission:visualizar_registros_ponto'])->group(function () {
        Route::get('time-records', [TimeRecordController::class, 'index'])->name('time-records.index');
        Route::get('time-records/{timeRecord}', [TimeRecordController::class, 'show'])->name('time-records.show');
    });

    // Edição e exclusão (apenas RH e Supervisores)
    Route::middleware(['permission:gerenciar_registros_ponto'])->group(function () {
        Route::get('time-records/{timeRecord}/edit', [TimeRecordController::class, 'edit'])->name('time-records.edit');
        Route::put('time-records/{timeRecord}', [TimeRecordController::class, 'update'])->name('time-records.update');
        Route::delete('time-records/{timeRecord}', [TimeRecordController::class, 'destroy'])->name('time-records.destroy');
    });

    // Aprovação de registros (Supervisores e RH)
    Route::middleware(['permission:aprovar_registros_ponto'])->group(function () {
        Route::patch('time-records/{timeRecord}/approve', [TimeRecordController::class, 'approve'])->name('time-records.approve');
        Route::patch('time-records/{timeRecord}/reject', [TimeRecordController::class, 'reject'])->name('time-records.reject');
    });

    // Download de anexos (usuários podem baixar seus próprios anexos ou supervisores podem baixar de qualquer um)
    Route::get('time-records/{timeRecord}/attachment/{filename}', [TimeRecordController::class, 'downloadAttachment'])->name('time-records.attachment.download');

    // === ESCALAS DE TRABALHO === (RH e Supervisores)
    Route::middleware(['permission:gerenciar_escalas'])->group(function () {
        Route::resource('work-schedules', WorkScheduleController::class);
    });

    // === AUSÊNCIAS ===
    // Visualização (todos podem ver suas próprias ausências)
    Route::middleware(['permission:visualizar_ausencias'])->group(function () {
        Route::get('absences', [AbsenceController::class, 'index'])->name('absences.index');
        Route::get('absences/{absence}', [AbsenceController::class, 'show'])->name('absences.show');
    });

    // Solicitação de ausências (funcionários podem solicitar)
    Route::middleware(['permission:solicitar_ausencias'])->group(function () {
        Route::get('absences/create', [AbsenceController::class, 'create'])->name('absences.create');
        Route::post('absences', [AbsenceController::class, 'store'])->name('absences.store');
    });

    // Gerenciamento de ausências (RH e Supervisores)
    Route::middleware(['permission:gerenciar_ausencias'])->group(function () {
        Route::get('absences/{absence}/edit', [AbsenceController::class, 'edit'])->name('absences.edit');
        Route::put('absences/{absence}', [AbsenceController::class, 'update'])->name('absences.update');
        Route::delete('absences/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');
    });

    // === HORAS EXTRAS ===
    // Visualização (todos podem ver suas próprias horas extras)
    Route::middleware(['permission:visualizar_horas_extras'])->group(function () {
        Route::get('overtime', [OvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/{overtime}', [OvertimeController::class, 'show'])->name('overtime.show');
    });

    // Solicitação de horas extras (funcionários podem solicitar)
    Route::middleware(['permission:solicitar_horas_extras'])->group(function () {
        Route::get('overtime/create', [OvertimeController::class, 'create'])->name('overtime.create');
        Route::post('overtime', [OvertimeController::class, 'store'])->name('overtime.store');
    });

    // Gerenciamento de horas extras (RH e Supervisores)
    Route::middleware(['permission:gerenciar_horas_extras'])->group(function () {
        Route::get('overtime/{overtime}/edit', [OvertimeController::class, 'edit'])->name('overtime.edit');
        Route::put('overtime/{overtime}', [OvertimeController::class, 'update'])->name('overtime.update');
        Route::delete('overtime/{overtime}', [OvertimeController::class, 'destroy'])->name('overtime.destroy');
    });

    // === FERIADOS === (RH e Administradores)
    Route::middleware(['permission:gerenciar_feriados'])->group(function () {
        Route::resource('holidays', HolidayController::class);
    });

    // === RELATÓRIOS === (RH, Supervisores e Administradores)
    Route::middleware(['permission:visualizar_relatorios'])->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');

            // Relatórios específicos
            Route::get('/time-records', [ReportController::class, 'timeRecords'])->name('time-records');
            Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary'])->name('attendance-summary');
            Route::get('/overtime', [ReportController::class, 'overtime'])->name('overtime');
            Route::get('/absences', [ReportController::class, 'absences'])->name('absences');

            // Exportações (apenas RH e Administradores)
            Route::middleware(['permission:exportar_relatorios'])->group(function () {
                Route::get('/export/time-records', [ReportController::class, 'exportTimeRecords'])->name('export.time-records');
                Route::get('/pdf/timesheet', [ReportController::class, 'generateTimesheetPDF'])->name('pdf.timesheet');
                Route::get('/pdf/all-timesheets', [ReportController::class, 'generateAllTimesheetsPDF'])->name('pdf.all-timesheets');
            });
        });
    });

    // === API PARA DASHBOARD === (todos os usuários autenticados)
    Route::get('/api/dashboard-data', [DashboardController::class, 'apiData'])->name('api.dashboard-data');

    // === REGISTRO DE PONTO === (todos os usuários autenticados)
    Route::prefix('time-clock')->name('time-clock.')->group(function () {
        Route::get('/register', [TimeClockApiController::class, 'index'])->name('register');
        Route::post('/register', [TimeClockApiController::class, 'register'])->name('register.store');
        Route::get('/today-records', [TimeClockApiController::class, 'todayRecords'])->name('today-records');
    });

    // === CONFIGURAÇÕES DO SISTEMA === (apenas administradores)
    Route::middleware(['permission:configurar_sistema'])->prefix('system')->name('system.')->group(function () {
        Route::get('/config', [App\Http\Controllers\SystemConfigController::class, 'index'])->name('config.index');
        Route::put('/config', [App\Http\Controllers\SystemConfigController::class, 'update'])->name('config.update');
        Route::get('/config/toggle-registration', [App\Http\Controllers\SystemConfigController::class, 'toggleRegistration'])->name('config.toggle-registration');
        
        // Rotas de backup
        Route::post('/backup/create', [App\Http\Controllers\SystemConfigController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/list', [App\Http\Controllers\SystemConfigController::class, 'listBackups'])->name('backup.list');
        Route::get('/backup/download/{filename}', [App\Http\Controllers\SystemConfigController::class, 'downloadBackup'])->name('backup.download');
    });
});



require __DIR__ . '/auth.php';
