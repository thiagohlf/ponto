<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TimeClock extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'serial_number',
        'model',
        'manufacturer',
        'location',
        'ip_address',
        'mac_address',
        'connection_type',
        'settings',
        'certification_number',
        'certification_date',
        'last_calibration',
        'next_calibration',
        'active',
        'last_sync',
        'status',
    ];

    protected $casts = [
        'settings' => 'json',
        'certification_date' => 'date',
        'last_calibration' => 'date',
        'next_calibration' => 'date',
        'active' => 'boolean',
        'last_sync' => 'datetime',
    ];

    // Relacionamentos
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_time_clock')
            ->withPivot(['can_register', 'requires_supervisor_approval', 'biometric_template', 'rfid_card_number', 'pin_code', 'access_start_time', 'access_end_time', 'registered_at', 'registered_by', 'active'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Métodos auxiliares
    public function isOnline()
    {
        return $this->status === 'online';
    }

    public function needsCalibration()
    {
        return $this->next_calibration && $this->next_calibration->isPast();
    }
}
