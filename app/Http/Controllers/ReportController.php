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
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $query = TimeRecord::with(['employee.company', 'timeClock'])
            ->whereBetween('record_date', [$request->start_date, $request->end_date]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $timeRecords = $query->orderBy('record_date')
            ->orderBy('record_time')
            ->get();

        $employees = Employee::active()->get();
        $companies = Company::active()->get();

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
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $employees = Employee::active();
        
        if ($request->filled('company_id')) {
            $employees->where('company_id', $request->company_id);
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

        $companies = Company::active()->get();

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
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'status' => 'nullable|in:pending,approved,rejected,paid',
        ]);

        $query = Overtime::with(['employee.company'])
            ->whereBetween('work_date', [$request->start_date, $request->end_date]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
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

        $employees = Employee::active()->get();

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
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
            'absence_type' => 'nullable|string',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $query = Absence::with(['employee.company'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            });

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('absence_type')) {
            $query->where('absence_type', $request->absence_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $absences = $query->orderBy('start_date')->get();
        
        $totalDays = $absences->sum('total_days');
        $employees = Employee::active()->get();

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
    public function exportTimeRecords(Request $request): Response
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $query = TimeRecord::with(['employee.company', 'timeClock'])
            ->whereBetween('record_date', [$request->start_date, $request->end_date]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $timeRecords = $query->orderBy('record_date')
            ->orderBy('record_time')
            ->get();

        $filename = 'registros_ponto_' . $request->start_date . '_' . $request->end_date . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($timeRecords) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho CSV
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
            ]);

            // Dados
            foreach ($timeRecords as $record) {
                fputcsv($file, [
                    $record->record_date->format('d/m/Y'),
                    $record->record_time,
                    $record->employee->name,
                    $record->employee->cpf,
                    $record->employee->company->name,
                    $this->translateRecordType($record->record_type),
                    $this->translateIdentificationMethod($record->identification_method),
                    $record->timeClock->name ?? 'N/A',
                    $record->nsr,
                    $this->translateStatus($record->status),
                    $record->observations ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
}
