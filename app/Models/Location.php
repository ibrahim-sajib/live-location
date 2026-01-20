<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'address',
        'type',
        'user_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for live locations
     */
    public function scopeLive($query)
    {
        return $query->where('type', 'live_location');
    }

    /**
     * Scope for saved locations
     */
    public function scopeSaved($query)
    {
        return $query->where('type', '!=', 'live_location');
    }

    /**
     * Check if location is recent (within last 5 minutes)
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->updated_at >= Carbon::now()->subMinutes(5);
    }

    /**
     * Get formatted coordinates
     */
    public function getCoordinatesAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }
}