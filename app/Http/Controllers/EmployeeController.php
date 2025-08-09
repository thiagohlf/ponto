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
            'weekly_hours' => 'required|integer|min:1|max:44',
            'has_meal_break' => 'boolean',
            'meal_break_minutes' => 'required|integer|min:30|max:120',
            'rfid_card' => 'nullable|string|max:50',
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
            'weekly_hours' => 'required|integer|min:1|max:44',
            'has_meal_break' => 'boolean',
            'meal_break_minutes' => 'required|integer|min:30|max:120',
            'rfid_card' => 'nullable|string|max:50',
            'active' => 'boolean',
        ]);

        $employee->update($validated);

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
            ->with('timeClock')
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
}
