<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'driver_id',
        'status',
        'driver_latitude',
        'driver_longitude',
        'pickup_latitude',
        'pickup_longitude',
        'drop_latitude',
        'drop_longitude',
        'pickup_address',
        'drop_address',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'driver_id' => 'integer',
        'driver_latitude' => 'double',
        'driver_longitude' => 'double',
        'pickup_latitude' => 'double',
        'pickup_longitude' => 'double',
        'drop_latitude' => 'double',
        'drop_longitude' => 'double',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}
