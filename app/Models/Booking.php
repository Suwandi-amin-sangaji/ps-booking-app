<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'service_type',
        'booking_date',
        'time_slot',
        'base_price',
        'weekend_surcharge',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'base_price' => 'decimal:2',
        'weekend_surcharge' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function generateBookingCode()
    {
        $prefix = 'PS-';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return $prefix . $date . '-' . $random;
    }

    public function getFormattedBookingDateAttribute()
    {
        return Carbon::parse($this->booking_date)->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    public function getServiceNameAttribute()
    {
        return $this->service_type === 'ps4' ? 'PlayStation 4' : 'PlayStation 5';
    }

    public function getFormattedBasePriceAttribute()
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    public function getFormattedWeekendSurchargeAttribute()
    {
        return 'Rp ' . number_format($this->weekend_surcharge, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'paid' => 'bg-success',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-primary',
        ];

        return '<span class="badge ' . $badges[$this->status] . '">' . ucfirst($this->status) . '</span>';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('booking_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('booking_date', Carbon::now()->month)
                    ->whereYear('booking_date', Carbon::now()->year);
    }
}