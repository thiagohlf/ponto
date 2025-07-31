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
        'name',
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
        'email',
        'phone',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'exempt_time_control',
        'weekly_hours',
        'has_meal_break',
        'meal_break_minutes',
        'fingerprint_template',
        'rfid_card',
        'photo_path',
        'active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'admission_date' => 'date',
        'dismissal_date' => 'date',
        'salary' => 'decimal:2',
        'exempt_time_control' => 'boolean',
        'has_meal_break' => 'boolean',
        'weekly_hours' => 'integer',
        'meal_break_minutes' => 'integer',
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

    // Relacionamentos Many-to-Many (tabelas pivot)
    public function timeClocks(): BelongsToMany
    {
        return $this->belongsToMany(TimeClock::class, 'employee_time_clock')
            ->withPivot(['can_register', 'requires_supervisor_approval', 'biometric_template', 'rfid_card_number', 'pin_code', 'access_start_time', 'access_end_time', 'registered_at', 'registered_by', 'active'])
            ->withTimestamps();
    }

    public function holidays(): BelongsToMany
    {
        return $this->belongsToMany(Holiday::class, 'employee_holiday')
            ->withPivot(['status', 'work_start_time', 'work_end_time', 'worked_minutes', 'base_salary_day', 'holiday_multiplier', 'calculated_amount', 'compensation_date', 'compensated', 'compensation_notes', 'work_justification', 'pre_authorized', 'authorized_by', 'authorized_at', 'payment_status', 'payment_date'])
            ->withTimestamps();
    }

    /**
     * Relacionamento com User (baseado no email)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function workSchedules(): BelongsToMany
    {
        return $this->belongsToMany(WorkSchedule::class, 'employee_work_schedule')
            ->withPivot(['start_date', 'end_date', 'custom_schedule', 'custom_tolerance', 'notes', 'temporary', 'reason', 'approved_by', 'approved_at', 'active'])
            ->withTimestamps();
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
