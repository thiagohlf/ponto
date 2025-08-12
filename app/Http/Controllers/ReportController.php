<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeRecord;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Show reports dashboard
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Time records report
     */
    public function timeRecords(Request $request): View
    {
        // Se não há parâmetros, apenas mostrar a tela de filtros
        if (!$request->hasAny(['start_date', 'end_date'])) {
            $user = auth()->user();
            
            if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
                $employees = Employee::active()->get();
                $companies = Company::active()->get();
            } else {
                $employees = collect();
                $companies = collect();
            }
            
            return view('reports.time-records', [
                'timeRecords' => collect(),
                'employees' => $employees,
                'companies' => $companies,
                'request' => $request
            ]);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = auth()->user();
        $query = TimeRecord::with(['employee.company'])
            ->whereBetween('record_date', [$request->start_date, $request->end_date]);

        // Se o usuário não for supervisor ou acima, só pode ver seus próprios registros
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // Apenas supervisores ou acima podem filtrar por funcionário
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->filled('company_id')) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            }
        }

        $timeRecords = $query->join('employees', 'time_records.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->orderBy('time_records.record_date')
            ->orderBy('time_records.record_time')
            ->select('time_records.*')
            ->get();

        // Para funcionários comuns, não mostrar listas de funcionários e empresas
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $employees = Employee::active()->get();
            $companies = Company::active()->get();
        } else {
            $employees = collect();
            $companies = collect();
        }

        return view('reports.time-records', compact(
            'timeRecords', 
            'employees', 
            'companies',
            'request'
        ));
    }

    /**
     * Attendance summary report
     */
    public function attendanceSummary(Request $request): View
    {
        // Se não há parâmetros, apenas mostrar a tela de filtros
        if (!$request->hasAny(['start_date', 'end_date'])) {
            $user = auth()->user();
            
            if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
                $companies = Company::active()->get();
            } else {
                $companies = collect();
            }
            
            return view('reports.attendance-summary', [
                'employees' => collect(),
                'attendanceData' => [],
                'companies' => $companies,
                'request' => $request
            ]);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = auth()->user();
        $employees = Employee::active();
        
        // Se o usuário não for supervisor ou acima, só pode ver seus próprios dados
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $employees->where('id', $employee->id);
            } else {
                $employees->whereRaw('1 = 0');
            }
        } else {
            // Apenas supervisores ou acima podem filtrar por empresa
            if ($request->filled('company_id')) {
                $employees->where('company_id', $request->company_id);
            }
        }

        $employees = $employees->get();
        $attendanceData = [];

        foreach ($employees as $employee) {
            $attendanceData[$employee->id] = $this->calculateAttendanceForEmployee(
                $employee, 
                $request->start_date, 
                $request->end_date
            );
        }

        // Para funcionários comuns, não mostrar lista de empresas
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $companies = Company::active()->get();
        } else {
            $companies = collect();
        }

        return view('reports.attendance-summary', compact(
            'employees',
            'attendanceData',
            'companies',
            'request'
        ));
    }

    /**
     * Overtime report
     */
    public function overtime(Request $request): View
    {
        // Se não há parâmetros, apenas mostrar a tela de filtros
        if (!$request->hasAny(['start_date', 'end_date'])) {
            $user = auth()->user();
            
            if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
                $employees = Employee::active()->get();
            } else {
                $employees = collect();
            }
            
            return view('reports.overtime', [
                'overtimeRecords' => collect(),
                'totalHours' => 0,
                'totalAmount' => 0,
                'employees' => $employees,
                'request' => $request
            ]);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'nullable|in:pending,approved,rejected,paid',
        ]);

        $user = auth()->user();
        $query = Overtime::with(['employee.company'])
            ->whereBetween('work_date', [$request->start_date, $request->end_date]);

        // Se o usuário não for supervisor ou acima, só pode ver suas próprias horas extras
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // Apenas supervisores ou acima podem filtrar por funcionário
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $overtimeRecords = $query->orderBy('work_date')->get();
        
        // Calcular totais
        $totalHours = $overtimeRecords->sum(function ($record) {
            return $record->total_minutes / 60;
        });
        
        $totalAmount = $overtimeRecords->sum('calculated_amount');

        // Para funcionários comuns, não mostrar lista de funcionários
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $employees = Employee::active()->get();
        } else {
            $employees = collect();
        }

        return view('reports.overtime', compact(
            'overtimeRecords',
            'totalHours',
            'totalAmount',
            'employees',
            'request'
        ));
    }

    /**
     * Absences report
     */
    public function absences(Request $request): View
    {
        // Se não há parâmetros, apenas mostrar a tela de filtros
        if (!$request->hasAny(['start_date', 'end_date'])) {
            $user = auth()->user();
            
            if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
                $employees = Employee::active()->get();
            } else {
                $employees = collect();
            }
            
            return view('reports.absences', [
                'absences' => collect(),
                'totalDays' => 0,
                'employees' => $employees,
                'request' => $request
            ]);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'absence_type' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $user = auth()->user();
        $query = Absence::with(['employee.company'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            });

        // Se o usuário não for supervisor ou acima, só pode ver suas próprias ausências
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // Apenas supervisores ou acima podem filtrar por funcionário
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }
        }

        if ($request->filled('absence_type')) {
            $query->where('absence_type', $request->absence_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $absences = $query->orderBy('start_date')->get();
        
        $totalDays = $absences->sum('total_days');
        
        // Para funcionários comuns, não mostrar lista de funcionários
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $employees = Employee::active()->get();
        } else {
            $employees = collect();
        }

        return view('reports.absences', compact(
            'absences',
            'totalDays',
            'employees',
            'request'
        ));
    }

    /**
     * Export time records to CSV
     */
    public function exportTimeRecords(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);
    
        $user = auth()->user();
        
        // 1. Inicie a query já com os joins necessários.
        $query = TimeRecord::query()
            ->join('employees', 'time_records.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->join('companies', 'employees.company_id', '=', 'companies.id')
            ->whereBetween('record_date', [$request->start_date, $request->end_date]);
    
        // Se o usuário não for supervisor ou acima, só pode exportar seus próprios registros
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $query->where('time_records.employee_id', $employee->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            // Apenas supervisores ou acima podem filtrar por funcionário
            if ($request->filled('employee_id')) {
                $query->where('time_records.employee_id', $request->employee_id);
            }
        }
    
        // 2. Selecione todas as colunas necessárias, usando aliases.
        $timeRecords = $query
            ->orderBy('users.name')
            ->orderBy('time_records.record_date')
            ->orderBy('time_records.record_time')
            ->select(
                'time_records.*',
                'users.name as employee_name',
                'employees.cpf as employee_cpf',
                'companies.name as company_name'
            )
            ->get();
    
        $filename = 'registros_ponto_' . $request->start_date . '_' . $request->end_date . '.csv';
    
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    
        $callback = function () use ($timeRecords) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Data',
                'Hora',
                'Funcionário',
                'CPF',
                'Empresa',
                'Tipo de Marcação',
                'Método de Identificação',
                'Relógio de Ponto',
                'NSR',
                'Status',
                'Observações'
            ], ';');
    
            // 3. Acesse os dados diretamente pelos aliases definidos no select.
            foreach ($timeRecords as $record) {
                fputcsv($file, [
                    $record->record_date->format('d/m/Y'),
                    \Carbon\Carbon::parse($record->record_time)->format('H:i:s'),
                    $record->employee_name, // Usando o alias 'employee_name'
                    $record->employee_cpf,  // Usando o alias 'employee_cpf'
                    $record->company_name,  // Usando o alias 'company_name'
                    $this->translateRecordType($record->record_type),
                    $this->translateIdentificationMethod($record->identification_method),
                    'Sistema Web',
                    $record->nsr,
                    $this->translateStatus($record->status),
                    $record->observations ?? ''
                ], ';');
            }
    
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate individual timesheet PDF for an employee
     */
    public function generateTimesheetPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $user = auth()->user();
        $employee = Employee::with('company')->findOrFail($request->employee_id);

        // Verificar permissões
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $userEmployee = $user->employee;
            if (!$userEmployee || $userEmployee->id !== $employee->id) {
                abort(403, 'Não autorizado a gerar este espelho de ponto.');
            }
        }

        // Buscar registros de ponto do funcionário no período
        $timeRecords = TimeRecord::where('employee_id', $employee->id)
            ->whereBetween('record_date', [$request->start_date, $request->end_date])
            ->orderBy('record_date')
            ->orderBy('record_time')
            ->get();

        // Processar dados para formato de espelho de ponto
        $timesheetData = $this->processTimesheetData($timeRecords, $request->start_date, $request->end_date);

        $data = [
            'employee' => $employee,
            'company' => $employee->company,
            'startDate' => Carbon::parse($request->start_date),
            'endDate' => Carbon::parse($request->end_date),
            'timeRecords' => $timeRecords,
            'timesheetData' => collect($timesheetData),
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('reports.timesheet-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'espelho_ponto_' . $employee->name . '_' . $request->start_date . '_' . $request->end_date . '.pdf';
        $filename = $this->sanitizeFilename($filename);

        return $pdf->download($filename);
    }

    /**
     * Generate timesheet PDFs for all employees in the period
     */
    public function generateAllTimesheetsPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = auth()->user();

        // Apenas supervisores ou acima podem gerar PDFs de todos os funcionários
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            abort(403, 'Não autorizado a gerar espelhos de ponto de todos os funcionários.');
        }

        $employees = Employee::active()->with('company');

        if ($request->filled('company_id')) {
            $employees->where('company_id', $request->company_id);
        }

        $employees = $employees->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'Nenhum funcionário encontrado para gerar os espelhos de ponto.');
        }

        // Criar um ZIP com todos os PDFs
        $zip = new \ZipArchive();
        $zipFilename = 'espelhos_ponto_' . $request->start_date . '_' . $request->end_date . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);

        // Criar diretório temporário se não existir
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'Não foi possível criar o arquivo ZIP.');
        }

        foreach ($employees as $employee) {
            // Buscar registros de ponto do funcionário no período
            $timeRecords = TimeRecord::where('employee_id', $employee->id)
                ->whereBetween('record_date', [$request->start_date, $request->end_date])
                ->orderBy('record_date')
                ->orderBy('record_time')
                ->get();

            // Pular funcionários sem registros
            if ($timeRecords->isEmpty()) {
                continue;
            }

            // Processar dados para formato de espelho de ponto
            $timesheetData = $this->processTimesheetData($timeRecords, $request->start_date, $request->end_date);

            $data = [
                'employee' => $employee,
                'company' => $employee->company,
                'startDate' => Carbon::parse($request->start_date),
                'endDate' => Carbon::parse($request->end_date),
                'timeRecords' => $timeRecords,
                'timesheetData' => collect($timesheetData),
                'generatedAt' => now(),
            ];

            $pdf = Pdf::loadView('reports.timesheet-pdf', $data);
            $pdf->setPaper('A4', 'portrait');

            $pdfFilename = 'espelho_ponto_' . $employee->name . '_' . $request->start_date . '_' . $request->end_date . '.pdf';
            $pdfFilename = $this->sanitizeFilename($pdfFilename);

            // Adicionar PDF ao ZIP
            $zip->addFromString($pdfFilename, $pdf->output());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Sanitize filename for safe file system usage
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove caracteres especiais e substitui espaços por underscores
        $filename = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return $filename;
    }

    /**
     * Calculate attendance data for an employee
     */
    private function calculateAttendanceForEmployee(Employee $employee, string $startDate, string $endDate): array
    {
        $timeRecords = TimeRecord::where('employee_id', $employee->id)
            ->whereBetween('record_date', [$startDate, $endDate])
            ->orderBy('record_date')
            ->orderBy('record_time')
            ->get();

        $absences = Absence::where('employee_id', $employee->id)
            ->approved()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->get();

        $workDays = $this->calculateWorkDays($startDate, $endDate);
        $presentDays = $timeRecords->groupBy('record_date')->count();
        $absentDays = $absences->sum('total_days');
        $lateArrivals = $this->calculateLateArrivals($timeRecords);

        return [
            'work_days' => $workDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_arrivals' => $lateArrivals,
            'attendance_rate' => $workDays > 0 ? round(($presentDays / $workDays) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate work days between dates (excluding weekends)
     */
    private function calculateWorkDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workDays++;
            }
            $start->addDay();
        }

        return $workDays;
    }

    /**
     * Calculate late arrivals from time records
     */
    private function calculateLateArrivals($timeRecords): int
    {
        return $timeRecords->where('record_type', 'entry')
            ->filter(function ($record) {
                // Considerando 8:10 como limite (8:00 + 10 min tolerância)
                return Carbon::parse($record->record_time)->gt(Carbon::parse('08:10:00'));
            })
            ->count();
    }

    /**
     * Translate record type to Portuguese
     */
    private function translateRecordType(string $type): string
    {
        return match ($type) {
            'entry' => 'Entrada',
            'exit' => 'Saída',
            'meal_start' => 'Saída Almoço',
            'meal_end' => 'Retorno Almoço',
            'break_start' => 'Início Pausa',
            'break_end' => 'Fim Pausa',
            default => $type,
        };
    }

    /**
     * Translate identification method to Portuguese
     */
    private function translateIdentificationMethod(string $method): string
    {
        return match ($method) {
            'biometric' => 'Biometria',
            'rfid' => 'Cartão RFID',
            'pin' => 'PIN/Senha',
            'facial' => 'Reconhecimento Facial',
            'manual' => 'Manual',
            default => $method,
        };
    }

    /**
     * Translate status to Portuguese
     */
    private function translateStatus(string $status): string
    {
        return match ($status) {
            'valid' => 'Válido',
            'invalid' => 'Inválido',
            'pending_approval' => 'Pendente Aprovação',
            default => $status,
        };
    }

    /**
     * Process time records data for timesheet format
     */
    private function processTimesheetData($timeRecords, string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $timesheetData = [];

        // Agrupar registros por data
        $recordsByDate = $timeRecords->groupBy(function ($record) {
            return $record->record_date->format('Y-m-d');
        });

        // Gerar dados para cada dia do período
        $current = $start->copy();
        while ($current->lte($end)) {
            $dateKey = $current->format('Y-m-d');
            $dayRecords = $recordsByDate->get($dateKey, collect());

            // Organizar registros por tipo
            $entry = $dayRecords->where('record_type', 'entry')->first();
            $mealStart = $dayRecords->where('record_type', 'meal_start')->first();
            $mealEnd = $dayRecords->where('record_type', 'meal_end')->first();
            $exit = $dayRecords->where('record_type', 'exit')->first();

            // Verificar status especiais
            $hasManual = $dayRecords->where('identification_method', 'manual')->count() > 0;
            $hasPending = $dayRecords->where('status', 'pending_approval')->count() > 0;
            $hasInvalid = $dayRecords->where('status', 'invalid')->count() > 0;

            $timesheetData[] = [
                'date' => $current->copy(),
                'day' => $current->format('d'),
                'month' => $current->format('m'),
                'weekday' => $this->getWeekdayName($current->dayOfWeek),
                'is_weekend' => $current->isWeekend(),
                'entry' => $entry ? Carbon::parse($entry->record_time)->format('H:i') : null,
                'meal_start' => $mealStart ? Carbon::parse($mealStart->record_time)->format('H:i') : null,
                'meal_end' => $mealEnd ? Carbon::parse($mealEnd->record_time)->format('H:i') : null,
                'exit' => $exit ? Carbon::parse($exit->record_time)->format('H:i') : null,
                'has_manual' => $hasManual,
                'has_pending' => $hasPending,
                'has_invalid' => $hasInvalid,
                'records_count' => $dayRecords->count(),
            ];

            $current->addDay();
        }

        return $timesheetData;
    }

    /**
     * Get weekday name in Portuguese
     */
    private function getWeekdayName(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'Dom',
            1 => 'Seg',
            2 => 'Ter',
            3 => 'Qua',
            4 => 'Qui',
            5 => 'Sex',
            6 => 'Sáb',
            default => '',
        };
    }
}
