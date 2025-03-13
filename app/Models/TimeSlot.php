<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFormattedTimeAttribute()
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }

    public static function getAvailableSlots($date, $serviceType)
    {
        // Get all active time slots
        $timeSlots = self::where('is_active', true)->get();
        
        // Get booked slots for the given date and service type
        $bookedSlots = Booking::where('booking_date', $date)
            ->where('service_type', $serviceType)
            ->whereIn('status', ['pending', 'processing', 'paid'])
            ->pluck('time_slot')
            ->toArray();
        
        // Filter out booked slots
        $availableSlots = $timeSlots->filter(function($slot) use ($bookedSlots) {
            return !in_array($slot->formatted_time, $bookedSlots);
        });
        
        return $availableSlots;
    }
}