<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $dateRange = $request->date_range;
        $search = $request->search;
        
        $query = Booking::with('payment')
            ->orderBy('booking_date', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }
        
        // Filter by date range
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) == 2) {
                $startDate = Carbon::createFromFormat('Y-m-d', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $dates[1])->endOfDay();
                $query->whereBetween('booking_date', [$startDate, $endDate]);
            }
        }
        
        // Search by booking code, customer name, or email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        
        $bookings = $query->paginate(15);
        
        return view('admin.bookings.index', compact('bookings', 'status', 'dateRange', 'search'));
    }

    public function show(Booking $booking)
    {
        $booking->load('payment');
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $timeSlots = TimeSlot::where('is_active', true)->get();
        return view('admin.bookings.edit', compact('booking', 'timeSlots'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|in:ps4,ps5',
            'booking_date' => 'required|date',
            'time_slot' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'status' => 'required|in:pending,processing,paid,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
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
        
        // $totalPrice = $basePrice + $weekendSurcharge  {
        //     $weekendSurcharge = 50000;
        // }
        
        $totalPrice = $basePrice + $weekendSurcharge;

        // Update booking
        $booking->service_type = $request->service_type;
        $booking->booking_date = $bookingDate;
        $booking->time_slot = $request->time_slot;
        $booking->customer_name = $request->customer_name;
        $booking->customer_email = $request->customer_email;
        $booking->customer_phone = $request->customer_phone;
        $booking->base_price = $basePrice;
        $booking->weekend_surcharge = $weekendSurcharge;
        $booking->total_price = $totalPrice;
        $booking->status = $request->status;
        $booking->notes = $request->notes;
        $booking->save();

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking berhasil diperbarui');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,paid,cancelled,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $booking->status = $request->status;
        $booking->save();

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dihapus');
    }

    public function calendar()
    {
        $timeSlots = TimeSlot::where('is_active', true)->get();
        return view('admin.bookings.calendar', compact('timeSlots'));
    }

    public function getCalendarEvents(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        
        $bookings = Booking::whereBetween('booking_date', [$start, $end])
            ->get();
        
        $events = [];
        
        foreach ($bookings as $booking) {
            $color = '#3788d8'; // Default blue
            
            switch ($booking->status) {
                case 'pending':
                    $color = '#ffc107'; // Warning yellow
                    break;
                case 'processing':
                    $color = '#17a2b8'; // Info blue
                    break;
                case 'paid':
                    $color = '#28a745'; // Success green
                    break;
                case 'cancelled':
                    $color = '#dc3545'; // Danger red
                    break;
                case 'completed':
                    $color = '#6c757d'; // Secondary gray
                    break;
            }
            
            $events[] = [
                'id' => $booking->id,
                'title' => $booking->customer_name . ' - ' . $booking->service_name,
                'start' => $booking->booking_date->format('Y-m-d') . 'T' . explode(' - ', $booking->time_slot)[0],
                'end' => $booking->booking_date->format('Y-m-d') . 'T' . explode(' - ', $booking->time_slot)[1],
                'color' => $color,
                'extendedProps' => [
                    'booking_code' => $booking->booking_code,
                    'customer_name' => $booking->customer_name,
                    'service_type' => $booking->service_name,
                    'status' => $booking->status,
                ]
            ];
        }
        
        return response()->json($events);
    }
}