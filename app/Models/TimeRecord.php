<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'time_clock_id',
        'record_date',
        'record_time',
        'full_datetime',
        'record_type',
        'identification_method',
        'nsr',
        'digital_signature',
        'hash_verification',
        'latitude',
        'longitude',
        'status',
        'observations',
        'original_datetime',
        'change_justification',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'record_date' => 'date',
        'record_time' => 'datetime:H:i:s',
        'full_datetime' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'original_datetime' => 'datetime',
        'changed_at' => 'datetime',
    ];

    // Relacionamentos
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeClock(): BelongsTo
    {
        return $this->belongsTo(TimeClock::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('record_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('record_type', $type);
    }

    // Métodos auxiliares
    public function isEntry()
    {
        return $this->record_type === 'entry';
    }

    public function isExit()
    {
        return $this->record_type === 'exit';
    }

    public function isMealBreak()
    {
        return in_array($this->record_type, ['meal_start', 'meal_end']);
    }

    public function wasManuallyChanged()
    {
        return !is_null($this->original_datetime);
    }

    public function isValid()
    {
        return $this->status === 'valid';
    }
}
