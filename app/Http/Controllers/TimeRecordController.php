<?php

namespace App\Http\Controllers;

use App\Models\TimeRecord;
use App\Models\Employee;
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
        $query = TimeRecord::with(['employee.user']);
        $user = auth()->user();

        // Se o usuário não for supervisor ou acima, só pode ver seus próprios registros
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if ($employee) {
                $query->where('employee_id', $employee->id);
            } else {
                // Se não tem funcionário associado, não pode ver nenhum registro
                $query->whereRaw('1 = 0');
            }
        }

        // Filtros
        if ($request->filled('employee_id')) {
            // Se não for supervisor ou acima, ignora o filtro de funcionário
            if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
                $query->where('employee_id', $request->employee_id);
            }
        }

        // Filtro de time_clock_id removido - sistema apenas web

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

        // Para funcionários comuns, não mostrar lista de funcionários no filtro
        if ($user->isSupervisor() || $user->isHR() || $user->isAdmin()) {
            $employees = Employee::active()->get();
        } else {
            $employees = collect(); // Lista vazia
        }

        // Sistema apenas web - sem relógios de ponto físicos
        return view('time-records.index', compact('timeRecords', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();

        // Se o usuário não for supervisor ou acima, só pode criar registros para si mesmo
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if (!$employee) {
                return redirect()->route('time-records.index')
                    ->with('error', 'Funcionário não encontrado. Entre em contato com o RH.');
            }
            $employees = collect([$employee]); // Apenas o próprio funcionário
        } else {
            $employees = Employee::active()->get();
        }

        // Sistema apenas web - sem relógios de ponto físicos
        return view('time-records.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'record_date' => 'required|date',
            'record_time' => 'required|date_format:H:i',
            'record_type' => 'required|in:entry,exit,meal_start,meal_end,break_start,break_end',
            'identification_method' => 'required|in:web_login,web_biometric,manual',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'change_justification' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max por arquivo
        ]);

        // Se o usuário não for supervisor ou acima, só pode criar registros para si mesmo
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if (!$employee || $validated['employee_id'] != $employee->id) {
                return redirect()->route('time-records.create')
                    ->with('error', 'Você só pode criar registros para si mesmo.');
            }
        }

        // Criar timestamp completo
        $fullDatetime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['record_date'] . ' ' . $validated['record_time']
        );

        // Gerar NSR único
        $nsr = $this->generateNSR();

        // Processar anexos se houver
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('time-records/attachments', $filename, 'public');

                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        // Determinar status baseado no usuário e método
        $isManualRequest = !$user->isSupervisor() && !$user->isHR() && !$user->isAdmin();
        $status = ($validated['identification_method'] === 'manual' || $isManualRequest) ? 'pending_approval' : 'valid';

        // Remover attachments dos dados validados para evitar erro
        $timeRecordData = $validated;
        unset($timeRecordData['attachments']);

        $timeRecord = TimeRecord::create([
            ...$timeRecordData,
            'full_datetime' => $fullDatetime,
            'nsr' => $nsr,
            'hash_verification' => $this->generateHash($validated, $nsr),
            'status' => $status,
            'attachments' => $attachments,
            'changed_by' => ($status === 'pending_approval') ? auth()->id() : null,
            'changed_at' => ($status === 'pending_approval') ? now() : null,
        ]);

        $message = $isManualRequest
            ? 'Solicitação de ajuste de ponto enviada para aprovação!'
            : 'Registro de ponto criado com sucesso!';

        return redirect()->route('time-records.show', $timeRecord)
            ->with('success', $message);
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment(TimeRecord $timeRecord, $filename)
    {
        $user = auth()->user();

        // Verificar se o usuário tem permissão para ver este registro
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if (!$employee || $timeRecord->employee_id !== $employee->id) {
                abort(403, 'Você não tem permissão para acessar este arquivo.');
            }
        }

        // Verificar se o arquivo existe nos anexos do registro
        $attachments = $timeRecord->attachments ?? [];
        $attachment = collect($attachments)->firstWhere('filename', $filename);

        if (!$attachment) {
            abort(404, 'Arquivo não encontrado.');
        }

        $filePath = storage_path('app/public/' . $attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado no sistema.');
        }

        return response()->download($filePath, $attachment['original_name']);
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeRecord $timeRecord): View
    {
        $user = auth()->user();

        // Se o usuário não for supervisor ou acima, só pode ver seus próprios registros
        if (!$user->isSupervisor() && !$user->isHR() && !$user->isAdmin()) {
            $employee = $user->employee;
            if (!$employee || $timeRecord->employee_id !== $employee->id) {
                abort(403, 'Você não tem permissão para visualizar este registro.');
            }
        }

        $timeRecord->load(['employee']);

        return view('time-records.show', compact('timeRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeRecord $timeRecord): View
    {
        $employees = Employee::active()->get();
        // Sistema apenas web - sem relógios de ponto físicos

        return view('time-records.edit', compact('timeRecord', 'employees'));
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
        $fullDatetime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['record_date'] . ' ' . $validated['record_time']
        );

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
     * Show the time registration form
     */
    public function showRegisterForm(): View
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Funcionário não encontrado. Entre em contato com o RH.');
        }

        // Buscar registros de hoje
        $todayRecords = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', today())
            ->orderBy('full_datetime')
            ->get();

        return view('time-records.register', compact('employee', 'todayRecords'));
    }

    /**
     * Get today's records for the authenticated user
     */
    public function todayRecords(): JsonResponse
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['error' => 'Funcionário não encontrado'], 404);
        }

        $todayRecords = TimeRecord::where('employee_id', $employee->id)
            ->whereDate('record_date', today())
            ->orderBy('full_datetime')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'record_time' => $record->record_time->format('H:i'),
                    'record_type' => $record->record_type,
                    'status' => $record->status,
                    'identification_method' => $record->identification_method,
                ];
            });

        return response()->json([
            'success' => true,
            'records' => $todayRecords
        ]);
    }

    /**
     * Web form time registration
     */
    public function webRegister(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'Funcionário não encontrado.');
        }

        try {
            // Buscar último registro do funcionário hoje para determinar o tipo
            $lastRecord = TimeRecord::where('employee_id', $employee->id)
                ->whereDate('record_date', today())
                ->latest('full_datetime')
                ->first();
            //Gera NSR
                $nsr = $this->generateNSR();
            // Criar registro de ponto
            $timeRecord = TimeRecord::create([
                'employee_id' => $employee->id,
                'record_date' => now()->toDateString(),
                'record_time' => now()->toTimeString(),
                'full_datetime' => now(),
                'record_type' => $this->determineRecordType($lastRecord),
                'nsr' => $nsr,
                'identification_method' => 'web_login',
                'status' => 'valid',
                'created_by' => $user->id,
            ]);

            return redirect()->back()->with('success', 'Ponto registrado com sucesso às ' . now()->format('H:i:s'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao registrar ponto: ' . $e->getMessage());
        }
    }



    /**
     * API endpoint for web time registration
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_identification' => 'required|string',
            'identification_method' => 'required|in:web_login,web_biometric,manual',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Buscar funcionário
        $employee = Employee::where('cpf', $validated['employee_identification'])
            ->orWhere('registration_number', $validated['employee_identification'])
            ->first();

        if (!$employee) {
            return response()->json(['error' => 'Funcionário não encontrado'], 404);
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
            'record_date' => now()->toDateString(),
            'record_time' => now()->toTimeString(),
            'full_datetime' => now(),
            'record_type' => $recordType,
            'identification_method' => $validated['identification_method'],
            'nsr' => $nsr,
            'hash_verification' => $this->generateHash($validated, $nsr),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'valid',
        ]);

        // Criar registro de localização se fornecido
        if ($validated['latitude'] && $validated['longitude']) {
            $timeRecord->locations()->create([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'source' => 'gps',
                'recorded_at' => now(),
            ]);
        }

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
