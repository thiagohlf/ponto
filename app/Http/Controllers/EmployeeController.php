<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['company', 'department']);

        // Filtros
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('active', false);
            }
        }

        $employees = $query->paginate(15);
        $companies = Company::active()->get();
        $departments = Department::active()->get();

        return view('employees.index', compact('employees', 'companies', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $companies = Company::active()->get();
        $departments = Department::active()->get();

        return view('employees.create', compact('companies', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:employees,cpf|size:14',
            'rg' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'registration_number' => 'required|string|unique:employees,registration_number',
            'pis_pasep' => 'nullable|string|size:14',
            'admission_date' => 'required|date',
            'position' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|size:9',
            'exempt_time_control' => 'boolean',
        ]);

        $employee = Employee::create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Funcionário cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'company',
            'department',
            'timeRecords' => function ($query) {
                $query->latest()->limit(10);
            },
            'absences' => function ($query) {
                $query->latest()->limit(5);
            },
            'overtime' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): View
    {
        $companies = Company::active()->get();
        $departments = Department::active()->get();

        return view('employees.edit', compact('employee', 'companies', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|size:14|unique:employees,cpf,' . $employee->id,
            'rg' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'registration_number' => 'required|string|unique:employees,registration_number,' . $employee->id,
            'pis_pasep' => 'nullable|string|size:14',
            'admission_date' => 'required|date',
            'dismissal_date' => 'nullable|date|after:admission_date',
            'position' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|size:9',
            'exempt_time_control' => 'boolean',
            'active' => 'boolean',
        ]);

        // Separar dados do usuário dos dados do funcionário
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
        ];

        $employeeData = collect($validated)->except(['name', 'email', 'phone', 'address', 'number', 'complement', 'neighborhood', 'city', 'state', 'zip_code'])->toArray();

        // Criar address_data JSON para os campos de endereço
        if (isset($validated['address'])) {
            $employeeData['address_data'] = [
                'street' => $validated['address'] ?? null,
                'number' => $validated['number'] ?? null,
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'zip_code' => $validated['zip_code'] ?? null,
            ];
        }

        // Atualizar dados do usuário se existir
        if ($employee->user) {
            $employee->user->update($userData);
        }

        // Atualizar dados do funcionário
        $employee->update($employeeData);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Funcionário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        // Soft delete - apenas desativa o funcionário
        $employee->update([
            'active' => false,
            'dismissal_date' => now()->toDateString()
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Funcionário desativado com sucesso!');
    }

    /**
     * Show employee time records
     */
    public function timeRecords(Employee $employee): View
    {
        $timeRecords = $employee->timeRecords()
            ->latest('full_datetime')
            ->paginate(20);

        return view('employees.time-records', compact('employee', 'timeRecords'));
    }

    /**
     * Show employee absences
     */
    public function absences(Employee $employee): View
    {
        $absences = $employee->absences()
            ->latest('start_date')
            ->paginate(15);

        return view('employees.absences', compact('employee', 'absences'));
    }

    /**
     * Show employee overtime
     */
    public function overtime(Employee $employee): View
    {
        $overtime = $employee->overtime()
            ->latest('work_date')
            ->paginate(15);

        return view('employees.overtime', compact('employee', 'overtime'));
    }

    /**
     * Show employee user permissions
     */
    public function userPermissions(Employee $employee): View
    {
        $user = $employee->user;

        if (!$user) {
            return redirect()->route('employees.show', $employee)
                ->with('error', 'Este funcionário não possui usuário associado.');
        }

        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();
        $user->load(['roles', 'permissions']);

        return view('employees.user-permissions', compact('employee', 'user', 'roles', 'permissions'));
    }

    /**
     * Update employee user permissions
     */
    public function updateUserPermissions(Request $request, Employee $employee)
    {
        // Log simples para verificar se o método está sendo chamado
        \Log::info('=== MÉTODO updateUserPermissions CHAMADO ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('All request data:', $request->all());
        \Log::info('Query parameters:', $request->query());
        \Log::info('Has manage_role: ' . ($request->query('manage_role') ?: 'null'));
        
        $user = $employee->user;

        if (!$user) {
            return redirect()->route('employees.show', $employee)
                ->with('error', 'Este funcionário não possui usuário associado.');
        }

        // Verificar se está gerenciando permissões de um role específico
        if ($request->has('manage_role')) {
            \Log::info('Chamando updateRolePermissions');
            return $this->updateRolePermissions($request);
        }

        // Caso contrário, atualizar roles do usuário
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name'
        ]);

        // Preparar roles para sincronização (remover valores vazios)
        $rolesToSync = isset($validated['roles']) ? array_filter($validated['roles']) : [];

        // Atualizar apenas os roles - as permissões vêm através dos roles
        $user->syncRoles($rolesToSync);

        return redirect()->route('employees.user-permissions', $employee)
            ->with('success', 'Perfis do usuário atualizados com sucesso!');
    }

    /**
     * Update role permissions
     */
    private function updateRolePermissions(Request $request)
    {
        // Log para debug
        \Log::info('=== ATUALIZANDO PERMISSÕES DO ROLE ===');
        \Log::info('Todos os dados da requisição:', $request->all());
        
        // O role_id agora vem no corpo da requisição (manage_role)
        $roleId = $request->input('manage_role');
        
        \Log::info('Role ID recebido:', ['role_id' => $roleId]);
        
        if (!$roleId) {
            \Log::error('Role ID não fornecido');
            return response()->json([
                'success' => false,
                'message' => 'ID do perfil não fornecido'
            ], 400);
        }

        try {
            $validated = $request->validate([
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            \Log::info('Dados validados:', $validated);

            $role = \Spatie\Permission\Models\Role::findOrFail($roleId);
            
            \Log::info('Role encontrado:', ['name' => $role->name, 'id' => $role->id]);

            // Preparar permissões para sincronização (remover valores vazios)
            $permissionsToSync = isset($validated['permissions']) ? array_filter($validated['permissions']) : [];

            \Log::info('Permissões para sincronizar:', $permissionsToSync);
            \Log::info('Permissões antes da sincronização:', $role->permissions->pluck('name')->toArray());

            // Atualizar permissões do role
            $role->syncPermissions($permissionsToSync);
            
            // Verificar resultado
            $role->refresh();
            \Log::info('Permissões após sincronização:', $role->permissions->pluck('name')->toArray());

            return redirect()->route('employees.user-permissions', $request->route('employee'))
                ->with('success', "Permissões do perfil '{$role->name}' atualizadas com sucesso! (" . count($permissionsToSync) . " permissões)");
            
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar permissões do role:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar permissões: ' . $e->getMessage()
            ], 500);
        }
    }
}
