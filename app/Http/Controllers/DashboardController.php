<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeRecord;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\Company;
// TimeClock removido - sistema apenas web
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Se o usuário não for supervisor ou acima, redirecionar para registro de ponto
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            return redirect()->route('time-clock.register');
        }
        
        // Estatísticas gerais - apenas para supervisores ou acima
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $totalEmployees = Employee::active()->count();
            $totalCompanies = Company::active()->count();
            $totalTimeClocks = 0; // Sistema apenas web - sem relógios físicos
            $todayRecords = TimeRecord::whereDate('record_date', today())->count();
            $presentEmployees = $this->getPresentEmployees();
            $pendingAbsences = Absence::pending()->count();
            $pendingOvertime = Overtime::pending()->count();
            
            // Últimos registros de ponto - apenas para supervisores ou acima
            $recentTimeRecords = TimeRecord::with(['employee'])
                ->latest('full_datetime')
                ->limit(10)
                ->get();
            
            // Status do sistema - apenas para supervisores ou acima
            $offlineClocks = 0; // Sistema apenas web - sem relógios físicos
            $weekStats = $this->getWeekStats();
            
            // Aniversariantes do mês
            $birthdayEmployees = Employee::active()
                ->whereMonth('birth_date', now()->month)
                ->whereDay('birth_date', '>=', now()->day)
                ->orderBy('birth_date')
                ->limit(5)
                ->get();
        } else {
            // Para funcionários comuns - apenas dados pessoais
            $employee = $user->employee;
            
            if ($employee) {
                // Apenas registros próprios do funcionário
                $todayRecords = TimeRecord::where('employee_id', $employee->id)
                    ->whereDate('record_date', today())
                    ->count();
                
                // Últimos registros próprios
                $recentTimeRecords = TimeRecord::with(['employee'])
                    ->where('employee_id', $employee->id)
                    ->latest('full_datetime')
                    ->limit(5)
                    ->get();
                
                // Verificar se está presente hoje
                $lastRecord = TimeRecord::where('employee_id', $employee->id)
                    ->whereDate('record_date', today())
                    ->latest('full_datetime')
                    ->first();
                
                $presentEmployees = 0;
                if ($lastRecord && in_array($lastRecord->record_type, ['entry', 'meal_end'])) {
                    $presentEmployees = 1; // Ele mesmo está presente
                }
            } else {
                // Se não tem funcionário associado
                $todayRecords = 0;
                $recentTimeRecords = collect();
                $presentEmployees = 0;
            }
            
            // Dados não disponíveis para funcionários comuns
            $totalEmployees = null;
            $totalCompanies = null;
            $totalTimeClocks = null;
            $pendingAbsences = null;
            $pendingOvertime = null;
            $offlineClocks = null;
            $weekStats = null;
            $birthdayEmployees = collect();
        }

        return view('dashboard', compact(
            'totalEmployees',
            'totalCompanies', 
            'totalTimeClocks',
            'todayRecords',
            'presentEmployees',
            'pendingAbsences',
            'pendingOvertime',
            'recentTimeRecords',
            'offlineClocks',
            'weekStats',
            'birthdayEmployees'
        ));
    }

    /**
     * Get employees currently present
     */
    private function getPresentEmployees(): int
    {
        $presentCount = 0;
        
        $employees = Employee::active()->get();
        
        foreach ($employees as $employee) {
            $todayRecords = TimeRecord::where('employee_id', $employee->id)
                ->whereDate('record_date', today())
                ->orderBy('full_datetime')
                ->get();
            
            if ($todayRecords->isEmpty()) {
                continue;
            }
            
            $lastRecord = $todayRecords->last();
            
            // Se o último registro foi entrada ou retorno do almoço, está presente
            if (in_array($lastRecord->record_type, ['entry', 'meal_end'])) {
                $presentCount++;
            }
        }
        
        return $presentCount;
    }

    /**
     * Get week statistics
     */
    private function getWeekStats(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return [
            'total_records' => TimeRecord::whereBetween('record_date', [$startOfWeek, $endOfWeek])->count(),
            'total_absences' => Absence::whereBetween('start_date', [$startOfWeek, $endOfWeek])->count(),
            'total_overtime_hours' => Overtime::whereBetween('work_date', [$startOfWeek, $endOfWeek])
                ->sum('total_minutes') / 60,
            'late_arrivals' => $this->getLateArrivals($startOfWeek, $endOfWeek),
        ];
    }

    /**
     * Get late arrivals count for the week
     */
    private function getLateArrivals(Carbon $startDate, Carbon $endDate): int
    {
        // Esta é uma implementação simplificada
        // Na prática, você precisaria comparar com os horários de trabalho de cada funcionário
        return TimeRecord::whereBetween('record_date', [$startDate, $endDate])
            ->where('record_type', 'entry')
            ->whereTime('record_time', '>', '08:10:00') // Considerando tolerância de 10 minutos
            ->count();
    }

    /**
     * Get dashboard data for API
     */
    public function apiData(): array
    {
        $user = auth()->user();
        
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            // Dados completos para supervisores ou acima
            return [
                'employees' => [
                    'total' => Employee::count(),
                    'active' => Employee::active()->count(),
                    'present_today' => $this->getPresentEmployees(),
                ],
                'time_records' => [
                    'today' => TimeRecord::whereDate('record_date', today())->count(),
                    'this_week' => TimeRecord::whereBetween('record_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])->count(),
                ],
                'pending_approvals' => [
                    'absences' => Absence::pending()->count(),
                    'overtime' => Overtime::pending()->count(),
                    'time_records' => TimeRecord::where('status', 'pending_approval')->count(),
                ],
                'system_status' => [
                    'online_clocks' => 0, // Sistema apenas web - sem relógios físicos
                    'offline_clocks' => 0, // Sistema apenas web - sem relógios físicos
                    'maintenance_clocks' => 0, // Sistema apenas web - sem relógios físicos
                ]
            ];
        } else {
            // Dados limitados para funcionários comuns
            $employee = $user->employee;
            
            if ($employee) {
                $todayRecords = TimeRecord::where('employee_id', $employee->id)
                    ->whereDate('record_date', today())
                    ->count();
                
                $weekRecords = TimeRecord::where('employee_id', $employee->id)
                    ->whereBetween('record_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])->count();
            } else {
                $todayRecords = 0;
                $weekRecords = 0;
            }
            
            return [
                'time_records' => [
                    'today' => $todayRecords,
                    'this_week' => $weekRecords,
                ],
                'personal_status' => [
                    'has_employee_record' => $employee !== null,
                ]
            ];
        }
    }
}
