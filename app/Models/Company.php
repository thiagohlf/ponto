<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'trade_name',
        'cnpj',
        'state_registration',
        'municipal_registration',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'tolerance_minutes',
        'requires_justification',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'requires_justification' => 'boolean',
        'tolerance_minutes' => 'integer',
    ];

    // Relacionamentos
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function timeClocks(): HasMany
    {
        return $this->hasMany(TimeClock::class);
    }

    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
