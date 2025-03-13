<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function finish(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu untuk melakukan pembayaran.');
        }
    
        $orderId = $request->order_id;
        $booking = Booking::where('booking_code', $orderId)->firstOrFail();
        
        $booking->status = 'paid';
        $booking->save();

        return redirect()->route('booking.confirmation', $booking);
    }

    public function unfinish(Request $request)
    {
        return redirect()->route('booking.index')
            ->with('error', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    public function error(Request $request)
    {
        return redirect()->route('booking.index')
            ->with('error', 'Terjadi kesalahan dalam proses pembayaran. Silakan coba lagi.');
    }

    public function notification(Request $request)
    {
        $notification = $request->all();
        
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;
        
        Log::info('Midtrans Notification', $notification);

        $booking = Booking::where('booking_code', $orderId)->firstOrFail();
        
        // Create or update payment record
        $payment = Payment::firstOrNew(['booking_id' => $booking->id]);
        $payment->transaction_id = $notification['transaction_id'];
        $payment->order_id = $notification['order_id'];
        $payment->payment_type = $notification['payment_type'];
        $payment->gross_amount = $notification['gross_amount'];
        $payment->payment_data = $notification;

        // Handle transaction status
        if ($statusCode == '200') {
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Transaction is challenged as fraud
                    $booking->status = 'pending';
                    $payment->status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    // Transaction is not fraud
                    $booking->status = 'paid';
                    $payment->status = 'success';
                    $payment->paid_at = now();
                }
            } else if ($transactionStatus == 'settlement') {
                // Transaction is settled
                $booking->status = 'paid';
                $payment->status = 'success';
                $payment->paid_at = now();
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Transaction is cancelled, denied, or expired
                $booking->status = 'cancelled';
                $payment->status = 'failed';
            } else if ($transactionStatus == 'pending') {
                // Transaction is pending
                $booking->status = 'pending';
                $payment->status = 'pending';
            }
        }

        $booking->save();
        $payment->save();

        return response()->json(['status' => 'success']);
    }
}