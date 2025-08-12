<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'department_id',
        'user_id',
        'work_schedule_id',
        'cpf',
        'rg',
        'birth_date',
        'gender',
        'registration_number',
        'pis_pasep',
        'admission_date',
        'dismissal_date',
        'position',
        'salary',
        'address_data',
        'exempt_time_control',
        'fingerprint_template',
        'photo_path',
        'active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'admission_date' => 'date',
        'dismissal_date' => 'date',
        'salary' => 'decimal:2',
        'address_data' => 'json',
        'exempt_time_control' => 'boolean',
        'active' => 'boolean',
    ];

    // Relacionamentos
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function overtime(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function medicalCertificates(): HasMany
    {
        return $this->hasMany(MedicalCertificate::class);
    }

    // Relacionamentos Many-to-Many (tabelas pivot)

    public function holidays(): BelongsToMany
    {
        return $this->belongsToMany(Holiday::class, 'employee_holiday')
            ->withPivot(['status', 'work_start_time', 'work_end_time', 'worked_minutes', 'base_salary_day', 'holiday_multiplier', 'calculated_amount', 'compensation_date', 'compensated', 'compensation_notes', 'work_justification', 'payment_status', 'payment_date'])
            ->withTimestamps();
    }

    public function workSchedules(): BelongsToMany
    {
        return $this->belongsToMany(WorkSchedule::class, 'employee_work_schedule')
            ->withPivot(['start_date', 'end_date', 'custom_schedule', 'custom_tolerance', 'notes', 'temporary', 'reason', 'active'])
            ->withTimestamps();
    }

    // Relacionamentos polimórficos
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Métodos auxiliares
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function isActive()
    {
        return $this->active && is_null($this->dismissal_date);
    }
}
