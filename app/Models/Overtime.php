<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $table = 'overtime';
    
    protected $fillable = [
        'employee_id',
        'work_date',
        'start_time',
        'end_time',
        'total_minutes',
        'overtime_type',
        'hourly_rate',
        'overtime_multiplier',
        'calculated_amount',
        'night_shift_applicable',
        'night_shift_minutes',
        'night_shift_percentage',
        'justification',
        'compensatory_time',
        'compensation_deadline',
        'compensated',
        'compensated_date',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'total_minutes' => 'integer',
        'hourly_rate' => 'decimal:2',
        'overtime_multiplier' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'night_shift_applicable' => 'boolean',
        'night_shift_minutes' => 'integer',
        'night_shift_percentage' => 'decimal:2',
        'compensatory_time' => 'boolean',
        'compensation_deadline' => 'date',
        'compensated' => 'boolean',
        'compensated_date' => 'date',
    ];

    // Relacionamentos
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Relacionamentos polimórficos
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('overtime_type', $type);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('work_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    public function scopeCompensatory($query)
    {
        return $query->where('compensatory_time', true);
    }

    public function scopeNotCompensated($query)
    {
        return $query->where('compensatory_time', true)->where('compensated', false);
    }

    // Métodos auxiliares
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function getLatestApproval()
    {
        return $this->approvals()->latest()->first();
    }

    public function isCompensatory()
    {
        return $this->compensatory_time;
    }

    public function isCompensated()
    {
        return $this->compensated;
    }

    public function hasNightShift()
    {
        return $this->night_shift_applicable;
    }

    public function getTotalHours()
    {
        return round($this->total_minutes / 60, 2);
    }

    public function getNightShiftHours()
    {
        return round($this->night_shift_minutes / 60, 2);
    }

    public function isOverdue()
    {
        return $this->compensatory_time && 
               $this->compensation_deadline && 
               $this->compensation_deadline->isPast() && 
               !$this->compensated;
    }
}
