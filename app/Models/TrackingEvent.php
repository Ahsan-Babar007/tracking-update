<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id','event_date','location_city','location_state','status','message'
    ];

    protected $casts = ['event_date' => 'datetime'];

    public function tracking()
    {
        return $this->belongsTo(Tracking::class);
    }
}