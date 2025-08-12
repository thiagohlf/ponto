<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'weekly_hours',
        'daily_hours',
        'monday_schedule',
        'tuesday_schedule',
        'wednesday_schedule',
        'thursday_schedule',
        'friday_schedule',
        'saturday_schedule',
        'sunday_schedule',
        'has_meal_break',
        'meal_break_duration',
        'meal_break_start',
        'meal_break_end',
        'entry_tolerance',
        'exit_tolerance',
        'general_tolerance',
        'flexible_schedule',
        'flexible_minutes',
        'allows_overtime',
        'max_daily_overtime',
        'compensatory_time',
        'created_by',
        'updated_by',
        'active',
    ];

    protected $casts = [
        'weekly_hours' => 'integer',
        'daily_hours' => 'integer',
        'monday_schedule' => 'json',
        'tuesday_schedule' => 'json',
        'wednesday_schedule' => 'json',
        'thursday_schedule' => 'json',
        'friday_schedule' => 'json',
        'saturday_schedule' => 'json',
        'sunday_schedule' => 'json',
        'has_meal_break' => 'boolean',
        'meal_break_duration' => 'integer',
        'meal_break_start' => 'datetime:H:i:s',
        'meal_break_end' => 'datetime:H:i:s',
        'entry_tolerance' => 'integer',
        'exit_tolerance' => 'integer',
        'general_tolerance' => 'integer',
        'flexible_schedule' => 'boolean',
        'flexible_minutes' => 'integer',
        'allows_overtime' => 'boolean',
        'max_daily_overtime' => 'integer',
        'compensatory_time' => 'boolean',
        'active' => 'boolean',
    ];

    // Relacionamentos
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_work_schedule')
            ->withPivot(['start_date', 'end_date', 'custom_schedule', 'custom_tolerance', 'notes', 'temporary', 'reason', 'active'])
            ->withTimestamps();
    }

    public function employeesWithDefaultSchedule(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    // Métodos auxiliares
    public function getScheduleForDay($dayOfWeek)
    {
        $schedules = [
            0 => 'sunday_schedule',
            1 => 'monday_schedule',
            2 => 'tuesday_schedule',
            3 => 'wednesday_schedule',
            4 => 'thursday_schedule',
            5 => 'friday_schedule',
            6 => 'saturday_schedule',
        ];

        return $this->{$schedules[$dayOfWeek]} ?? null;
    }

    public function isWorkDay($dayOfWeek)
    {
        $schedule = $this->getScheduleForDay($dayOfWeek);
        return !is_null($schedule) && !empty($schedule);
    }

    public function isFlexible()
    {
        return $this->flexible_schedule;
    }

    public function allowsOvertime()
    {
        return $this->allows_overtime;
    }
}
