<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for dashboard
        $totalBookings = Booking::count();
        $pendingBookings = Booking::pending()->count();
        $paidBookings = Booking::paid()->count();
        $completedBookings = Booking::completed()->count();
        $cancelledBookings = Booking::cancelled()->count();
        
        $totalRevenue = Payment::where('status', 'success')->sum('gross_amount');
        $todayRevenue = Payment::where('status', 'success')
            ->whereDate('paid_at', Carbon::today())
            ->sum('gross_amount');
        
        $totalCustomers = User::where('role', 'customer')->count();
        
        // Get recent bookings
        $recentBookings = Booking::with('payment')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get bookings for today
        $todayBookings = Booking::with('payment')
            ->whereDate('booking_date', Carbon::today())
            ->orderBy('time_slot', 'asc')
            ->get();
        
        // Get monthly revenue data for chart
        $monthlyRevenue = Payment::where('status', 'success')
            ->whereYear('paid_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(gross_amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return $item->revenue;
            });
        
        // Fill in missing months with zero
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $monthlyRevenue[$i] ?? 0;
        }
        
        return view('admin.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'paidBookings',
            'completedBookings',
            'cancelledBookings',
            'totalRevenue',
            'todayRevenue',
            'totalCustomers',
            'recentBookings',
            'todayBookings',
            'chartData'
        ));
    }
}