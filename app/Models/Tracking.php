<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number', 'carrier', 'country_type',
        'origin_city','origin_state','origin_zip',
        'destination_city','destination_state','destination_zip',
        'start_date','expected_delivery_date',
        'delayed_until','delay_reason','status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_delivery_date' => 'date',
        'delayed_until' => 'date',
    ];

    public function events()
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_date');
    }
}