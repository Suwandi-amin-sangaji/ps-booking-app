<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_type',
        'transaction_id',
        'order_id',
        'status',
        'gross_amount',
        'payment_data',
        'paid_at',
    ];

    protected $casts = [
        'payment_data' => 'array',
        'gross_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFormattedGrossAmountAttribute()
    {
        return 'Rp ' . number_format($this->gross_amount, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            'expired' => 'bg-secondary',
            'refunded' => 'bg-info',
        ];

        return '<span class="badge ' . $badges[$this->status] . '">' . ucfirst($this->status) . '</span>';
    }
}