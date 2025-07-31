<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TimeClockApiController extends Controller
{

    /**
     * Exibir página de registro de ponto
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Funcionário não encontrado. Entre em contato com o RH.');
        }

        // Buscar último registro do dia
        $today = Carbon::today();
        $lastRecord = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', $today)
            ->orderBy('full_datetime', 'desc')
            ->first();

        // Determinar próximo tipo de registro
        $nextRecordType = $this->getNextRecordType($lastRecord);

        return view('time-clock.register', compact('employee', 'lastRecord', 'nextRecordType'));
    }

    /**
     * Registrar ponto
     */
    public function register(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['error' => 'Funcionário não encontrado'], 404);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        // Buscar último registro do dia
        $lastRecord = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', $today)
            ->orderBy('full_datetime', 'desc')
            ->first();

        // Determinar tipo do próximo registro
        $recordType = $this->getNextRecordType($lastRecord);

        // Gerar NSR (Número Sequencial de Registro)
        $nsr = 'NSR' . date('Ymd') . str_pad(TimeRecord::whereDate('record_date', $today)->count() + 1, 6, '0', STR_PAD_LEFT);

        // Criar novo registro
        $timeRecord = TimeRecord::create([
            'employee_id' => $employee->id,
            'record_date' => $now->toDateString(),
            'record_time' => $now->toTimeString(),
            'full_datetime' => $now,
            'record_type' => $recordType,
            'identification_method' => 'manual',
            'nsr' => $nsr,
            'status' => 'valid',
            'observations' => 'Registro realizado pelo próprio funcionário via web',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ponto registrado com sucesso!',
            'record' => [
                'id' => $timeRecord->id,
                'datetime' => $now->format('d/m/Y H:i:s'),
                'type' => $this->getRecordTypeLabel($recordType),
                'next_type' => $this->getRecordTypeLabel($this->getNextRecordType($timeRecord))
            ]
        ]);
    }

    /**
     * Determinar próximo tipo de registro
     */
    private function getNextRecordType($lastRecord)
    {
        if (!$lastRecord) {
            return 'entry'; // Primeiro registro do dia = entrada
        }

        switch ($lastRecord->record_type) {
            case 'entry':
                return 'meal_start'; // Após entrada = início do almoço
            case 'meal_start':
                return 'meal_end'; // Após início do almoço = fim do almoço
            case 'meal_end':
                return 'exit'; // Após fim do almoço = saída
            case 'exit':
                return 'entry'; // Após saída = nova entrada (hora extra)
            default:
                return 'entry';
        }
    }

    /**
     * Obter label do tipo de registro
     */
    private function getRecordTypeLabel($type)
    {
        $labels = [
            'entry' => 'Entrada',
            'exit' => 'Saída',
            'meal_start' => 'Início do Almoço',
            'meal_end' => 'Fim do Almoço',
        ];

        return $labels[$type] ?? 'Registro';
    }

    /**
     * Obter label do status
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'valid' => 'Válido',
            'invalid' => 'Inválido',
            'pending_approval' => 'Pendente',
        ];

        return $labels[$status] ?? 'Desconhecido';
    }

    /**
     * Obter registros do dia atual
     */
    public function todayRecords()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['error' => 'Funcionário não encontrado'], 404);
        }

        $today = Carbon::today();
        $records = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', $today)
            ->orderBy('full_datetime', 'asc')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'datetime' => Carbon::parse($record->full_datetime)->format('d/m/Y H:i:s'),
                    'time_only' => Carbon::parse($record->full_datetime)->format('H:i:s'),
                    'type' => $this->getRecordTypeLabel($record->record_type),
                    'status' => $this->getStatusLabel($record->status),
                ];
            });

        return response()->json(['records' => $records]);
    }
}
