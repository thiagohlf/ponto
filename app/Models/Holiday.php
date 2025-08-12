<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Holiday extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'date',
        'year',
        'type',
        'state',
        'city',
        'is_fixed',
        'is_recurring',
        'description',
        'mandatory_rest',
        'allows_work',
        'work_multiplier',
        'calculation_rule',
        'days_offset',
        'active',
    ];

    protected $casts = [
        'date' => 'date',
        'year' => 'integer',
        'is_fixed' => 'boolean',
        'is_recurring' => 'boolean',
        'mandatory_rest' => 'boolean',
        'allows_work' => 'boolean',
        'work_multiplier' => 'decimal:2',
        'days_offset' => 'integer',
        'active' => 'boolean',
    ];

    // Relacionamentos
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_holiday')
            ->withPivot(['status', 'work_start_time', 'work_end_time', 'worked_minutes', 'base_salary_day', 'holiday_multiplier', 'calculated_amount', 'compensation_date', 'compensated', 'compensation_notes', 'work_justification', 'payment_status', 'payment_date'])
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

    public function scopeNational($query)
    {
        return $query->where('type', 'national');
    }

    public function scopeForState($query, $state)
    {
        return $query->where('state', $state)->orWhere('type', 'national');
    }

    public function scopeForCity($query, $city, $state = null)
    {
        return $query->where(function ($q) use ($city, $state) {
            $q->where('city', $city)
              ->orWhere('type', 'national');
            if ($state) {
                $q->orWhere('state', $state);
            }
        });
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeFixed($query)
    {
        return $query->where('is_fixed', true);
    }

    public function scopeMovable($query)
    {
        return $query->where('is_fixed', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Métodos auxiliares
    public function isNational()
    {
        return $this->type === 'national';
    }

    public function isState()
    {
        return $this->type === 'state';
    }

    public function isMunicipal()
    {
        return $this->type === 'municipal';
    }

    public function isCompany()
    {
        return $this->type === 'company';
    }

    public function isFixed()
    {
        return $this->is_fixed;
    }

    public function isMovable()
    {
        return !$this->is_fixed;
    }

    public function isRecurring()
    {
        return $this->is_recurring;
    }

    public function isMandatoryRest()
    {
        return $this->mandatory_rest;
    }

    public function allowsWork()
    {
        return $this->allows_work;
    }

    public function getWorkMultiplier()
    {
        return $this->work_multiplier;
    }

    public function isToday()
    {
        return $this->date->isToday();
    }

    public function isPast()
    {
        return $this->date->isPast();
    }

    public function isFuture()
    {
        return $this->date->isFuture();
    }

    public function appliesTo($state = null, $city = null)
    {
        if ($this->isNational()) {
            return true;
        }

        if ($this->isState() && $this->state === $state) {
            return true;
        }

        if ($this->isMunicipal() && $this->city === $city && $this->state === $state) {
            return true;
        }

        return false;
    }
}
