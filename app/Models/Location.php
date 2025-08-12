<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Location extends Model
{
    protected $fillable = [
        'locatable_type',
        'locatable_id',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'country',
        'accuracy',
        'source',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    // Relacionamentos
    public function locatable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeGPS($query)
    {
        return $query->where('source', 'gps');
    }

    public function scopeNetwork($query)
    {
        return $query->where('source', 'network');
    }

    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        return $query->selectRaw("
            *, (
                {$earthRadius} * acos(
                    cos(radians(?)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
        ->having('distance', '<=', $radiusKm)
        ->orderBy('distance');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    // Métodos auxiliares
    public function isGPS()
    {
        return $this->source === 'gps';
    }

    public function isNetwork()
    {
        return $this->source === 'network';
    }

    public function isManual()
    {
        return $this->source === 'manual';
    }

    public function isIP()
    {
        return $this->source === 'ip';
    }

    public function isAccurate($maxAccuracyMeters = 100)
    {
        return $this->accuracy && $this->accuracy <= $maxAccuracyMeters;
    }

    public function getDistanceFrom($latitude, $longitude)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    public function isWithinRadius($latitude, $longitude, $radiusMeters)
    {
        return $this->getDistanceFrom($latitude, $longitude) <= $radiusMeters;
    }

    public function getGoogleMapsUrl()
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public function getFormattedCoordinates()
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    public function getFullAddress()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country
        ]);

        return implode(', ', $parts);
    }
}