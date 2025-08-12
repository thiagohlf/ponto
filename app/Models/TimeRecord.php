<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'record_date',
        'record_time',
        'full_datetime',
        'record_type',
        'identification_method',
        'nsr',
        'digital_signature',
        'hash_verification',
        'status',
        'observations',
        'change_data',
        'ip_address',
        'user_agent',
        'device_info',
        'attachments',
    ];

    protected $casts = [
        'record_date' => 'date',
        'record_time' => 'datetime:H:i:s',
        'full_datetime' => 'datetime',
        'change_data' => 'json',
        'attachments' => 'array',
    ];

    // Relacionamentos
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Relacionamentos polimórficos
    public function locations()
    {
        return $this->morphMany(Location::class, 'locatable');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
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
        return !is_null($this->change_data);
    }

    public function getOriginalDateTime()
    {
        return $this->change_data['original_datetime'] ?? null;
    }

    public function getChangeJustification()
    {
        return $this->change_data['change_justification'] ?? null;
    }

    public function getChangedBy()
    {
        return $this->change_data['changed_by'] ?? null;
    }

    public function getChangedAt()
    {
        return $this->change_data['changed_at'] ?? null;
    }

    public function isValid()
    {
        return $this->status === 'valid';
    }
}
