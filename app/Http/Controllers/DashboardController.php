<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeRecord;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\Company;
use App\Models\TimeClock;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        // Estatísticas gerais
        $totalEmployees = Employee::active()->count();
        $totalCompanies = Company::active()->count();
        $totalTimeClocks = TimeClock::active()->count();
        
        // Registros de hoje
        $todayRecords = TimeRecord::whereDate('record_date', today())->count();
        
        // Funcionários presentes hoje (que fizeram entrada mas não saída)
        $presentEmployees = $this->getPresentEmployees();
        
        // Ausências pendentes de aprovação
        $pendingAbsences = Absence::pending()->count();
        
        // Horas extras pendentes
        $pendingOvertime = Overtime::pending()->count();
        
        // Últimos registros de ponto
        $recentTimeRecords = TimeRecord::with(['employee', 'timeClock'])
            ->latest('full_datetime')
            ->limit(10)
            ->get();
        
        // Relógios offline
        $offlineClocks = TimeClock::where('status', 'offline')->count();
        
        // Estatísticas da semana
        $weekStats = $this->getWeekStats();
        
        // Aniversariantes do mês
        $birthdayEmployees = Employee::active()
            ->whereMonth('birth_date', now()->month)
            ->whereDay('birth_date', '>=', now()->day)
            ->orderBy('birth_date')
            ->limit(5)
            ->get();

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
                'online_clocks' => TimeClock::where('status', 'online')->count(),
                'offline_clocks' => TimeClock::where('status', 'offline')->count(),
                'maintenance_clocks' => TimeClock::where('status', 'maintenance')->count(),
            ]
        ];
    }
}
