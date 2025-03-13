<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TimeSlot;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $timeSlots = TimeSlot::where('is_active', true)->get();
        return view('bookings.index', compact('timeSlots'));
    }

    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_date' => 'required|date|after_or_equal:today',
            'service_type' => 'required|in:ps4,ps5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $date = $request->booking_date;
        $serviceType = $request->service_type;
        
        $availableSlots = TimeSlot::getAvailableSlots($date, $serviceType);
        
        return response()->json([
            'available_slots' => $availableSlots->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'slot' => $slot->slot,
                    'time' => $slot->formatted_time,
                ];
            }),
            'is_weekend' => Carbon::parse($date)->isWeekend(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|in:ps4,ps5',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->route('booking.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate prices
        $basePrice = $request->service_type === 'ps4' ? 30000 : 40000;
        
        $bookingDate = Carbon::parse($request->booking_date);
        $weekendSurcharge = 0;
        
        if ($bookingDate->isWeekend()) {
            $weekendSurcharge = 50000;
        }
        
        $totalPrice = $basePrice + $weekendSurcharge;

        // Create booking
        $booking = new Booking();
        $booking->booking_code = $booking->generateBookingCode();
        $booking->user_id = Auth::check() ? Auth::id() : null;
        $booking->customer_name = $request->customer_name;
        $booking->customer_email = $request->customer_email;
        $booking->customer_phone = $request->customer_phone;
        $booking->service_type = $request->service_type;
        $booking->booking_date = $bookingDate;
        $booking->time_slot = $request->time_slot;
        $booking->base_price = $basePrice;
        $booking->weekend_surcharge = $weekendSurcharge;
        $booking->total_price = $totalPrice;
        $booking->status = 'pending';
        $booking->save();

        return redirect()->route('booking.payment', $booking);
    }

    public function payment(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('booking.index')->with('error', 'Booking ini sudah diproses');
        }

        // Generate Midtrans snap token
        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_code,
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->customer_name,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
            ],
            'item_details' => [
                [
                    'id' => $booking->service_type,
                    'price' => (int) $booking->base_price,
                    'quantity' => 1,
                    'name' => $booking->service_name,
                ],
            ],
        ];

        // Add weekend surcharge if applicable
        if ($booking->weekend_surcharge > 0) {
            $params['item_details'][] = [
                'id' => 'weekend-surcharge',
                'price' => (int) $booking->weekend_surcharge,
                'quantity' => 1,
                'name' => 'Weekend Surcharge',
            ];
        }

        $snapToken = $this->midtransService->getSnapToken($params);

        return view('bookings.payment', compact('booking', 'snapToken'));
    }

    public function confirmation(Booking $booking)
    {
        if (!in_array($booking->status, ['paid', 'completed'])) {
            return redirect()->route('booking.index')->with('error', 'Booking belum dibayar');
        }

        return view('bookings.confirmation', compact('booking'));
    }

    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $bookings = Auth::user()->bookings()->orderBy('created_at', 'desc')->paginate(10);
        return view('bookings.history', compact('bookings'));
    }
}