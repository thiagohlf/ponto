<?php

namespace App\Http\Controllers;

use App\Models\TimeRecord;
use App\Models\Employee;
use App\Models\TimeClock;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class TimeRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = TimeRecord::with(['employee', 'timeClock']);

        // Filtros
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('time_clock_id')) {
            $query->where('time_clock_id', $request->time_clock_id);
        }

        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $timeRecords = $query->latest('full_datetime')->paginate(20);
        $employees = Employee::active()->get();
        $timeClocks = TimeClock::active()->get();

        return view('time-records.index', compact('timeRecords', 'employees', 'timeClocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $employees = Employee::active()->get();
        $timeClocks = TimeClock::active()->get();

        return view('time-records.create', compact('employees', 'timeClocks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'time_clock_id' => 'nullable|exists:time_clocks,id',
            'record_date' => 'required|date',
            'record_time' => 'required|date_format:H:i',
            'record_type' => 'required|in:entry,exit,meal_start,meal_end,break_start,break_end',
            'identification_method' => 'required|in:biometric,rfid,pin,facial,manual',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'observations' => 'nullable|string|max:1000',
            'change_justification' => 'nullable|string|max:1000',
        ]);

        // Criar timestamp completo
        $fullDatetime = Carbon::createFromFormat('Y-m-d H:i', 
            $validated['record_date'] . ' ' . $validated['record_time']);

        // Gerar NSR único
        $nsr = $this->generateNSR();

        $timeRecord = TimeRecord::create([
            ...$validated,
            'full_datetime' => $fullDatetime,
            'nsr' => $nsr,
            'hash_verification' => $this->generateHash($validated, $nsr),
            'status' => $validated['identification_method'] === 'manual' ? 'pending_approval' : 'valid',
            'changed_by' => $validated['identification_method'] === 'manual' ? auth()->id() : null,
            'changed_at' => $validated['identification_method'] === 'manual' ? now() : null,
        ]);

        return redirect()->route('time-records.show', $timeRecord)
            ->with('success', 'Registro de ponto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeRecord $timeRecord): View
    {
        $timeRecord->load(['employee', 'timeClock', 'changedBy']);

        return view('time-records.show', compact('timeRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeRecord $timeRecord): View
    {
        $employees = Employee::active()->get();
        $timeClocks = TimeClock::active()->get();

        return view('time-records.edit', compact('timeRecord', 'employees', 'timeClocks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeRecord $timeRecord): RedirectResponse
    {
        $validated = $request->validate([
            'record_date' => 'required|date',
            'record_time' => 'required|date_format:H:i',
            'record_type' => 'required|in:entry,exit,meal_start,meal_end,break_start,break_end',
            'observations' => 'nullable|string|max:1000',
            'change_justification' => 'required|string|max:1000',
        ]);

        // Salvar dados originais se for a primeira alteração
        if (is_null($timeRecord->original_datetime)) {
            $timeRecord->original_datetime = $timeRecord->full_datetime;
        }

        // Criar novo timestamp
        $fullDatetime = Carbon::createFromFormat('Y-m-d H:i', 
            $validated['record_date'] . ' ' . $validated['record_time']);

        $timeRecord->update([
            'record_date' => $validated['record_date'],
            'record_time' => $validated['record_time'],
            'full_datetime' => $fullDatetime,
            'record_type' => $validated['record_type'],
            'observations' => $validated['observations'],
            'change_justification' => $validated['change_justification'],
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'status' => 'pending_approval',
        ]);

        return redirect()->route('time-records.show', $timeRecord)
            ->with('success', 'Registro de ponto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeRecord $timeRecord): RedirectResponse
    {
        // Marcar como inválido ao invés de deletar (conformidade legal)
        $timeRecord->update([
            'status' => 'invalid',
            'change_justification' => 'Registro invalidado pelo sistema',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        return redirect()->route('time-records.index')
            ->with('success', 'Registro de ponto invalidado com sucesso!');
    }

    /**
     * Approve a time record
     */
    public function approve(TimeRecord $timeRecord): RedirectResponse
    {
        $timeRecord->update([
            'status' => 'valid',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Registro de ponto aprovado com sucesso!');
    }

    /**
     * Reject a time record
     */
    public function reject(Request $request, TimeRecord $timeRecord): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $timeRecord->update([
            'status' => 'invalid',
            'change_justification' => $request->rejection_reason,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Registro de ponto rejeitado!');
    }

    /**
     * API endpoint for time clock registration
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_identification' => 'required|string',
            'time_clock_serial' => 'required|string',
            'identification_method' => 'required|in:biometric,rfid,pin,facial',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Buscar funcionário
        $employee = Employee::where('cpf', $validated['employee_identification'])
            ->orWhere('registration_number', $validated['employee_identification'])
            ->orWhere('rfid_card', $validated['employee_identification'])
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Funcionário não encontrado'], 404);
        }

        // Buscar relógio de ponto
        $timeClock = TimeClock::where('serial_number', $validated['time_clock_serial'])->first();

        if (!$timeClock) {
            return response()->json(['error' => 'Relógio de ponto não encontrado'], 404);
        }

        // Determinar tipo de marcação baseado no último registro
        $lastRecord = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', today())
            ->latest('full_datetime')
            ->first();

        $recordType = $this->determineRecordType($lastRecord);

        // Gerar NSR único
        $nsr = $this->generateNSR();

        $timeRecord = TimeRecord::create([
            'employee_id' => $employee->id,
            'time_clock_id' => $timeClock->id,
            'record_date' => now()->toDateString(),
            'record_time' => now()->toTimeString(),
            'full_datetime' => now(),
            'record_type' => $recordType,
            'identification_method' => $validated['identification_method'],
            'nsr' => $nsr,
            'hash_verification' => $this->generateHash($validated, $nsr),
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => 'valid',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ponto registrado com sucesso',
            'data' => [
                'employee_name' => $employee->name,
                'record_type' => $recordType,
                'record_time' => $timeRecord->record_time,
                'nsr' => $nsr,
            ]
        ]);
    }

    /**
     * Generate unique NSR (Número Sequencial de Registro)
     */
    private function generateNSR(): string
    {
        $lastRecord = TimeRecord::latest('id')->first();
        $lastNSR = $lastRecord ? intval($lastRecord->nsr) : 0;
        
        return str_pad($lastNSR + 1, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate hash for record verification
     */
    private function generateHash(array $data, string $nsr): string
    {
        $hashData = $nsr . json_encode($data) . config('app.key');
        return hash('sha256', $hashData);
    }

    /**
     * Determine record type based on last record
     */
    private function determineRecordType(?TimeRecord $lastRecord): string
    {
        if (!$lastRecord) {
            return 'entry';
        }

        return match ($lastRecord->record_type) {
            'entry' => 'meal_start',
            'meal_start' => 'meal_end',
            'meal_end' => 'exit',
            'exit' => 'entry',
            default => 'entry',
        };
    }
}
