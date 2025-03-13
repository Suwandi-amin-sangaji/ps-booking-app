@extends('layouts.app')

@section('title', 'Riwayat Booking - PS Rental')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Riwayat Booking</h1>
            <a href="{{ route('booking.index') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Booking Baru
            </a>
        </div>
        
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID Booking</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Jenis</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->booking_code }}</td>
                                <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                                <td>{{ $booking->time_slot }}</td>
                                <td>{{ $booking->service_name }}</td>
                                <td>{{ $booking->formatted_total_price }}</td>
                                <td>{!! $booking->status_badge !!}</td>
                                <td>
                                    @if($booking->status == 'pending')
                                        <a href="{{ route('booking.payment', $booking) }}" class="btn btn-sm btn-primary">
                                            Bayar
                                        </a>
                                    @elseif(in_array($booking->status, ['paid', 'completed']))
                                        <a href="{{ route('booking.confirmation', $booking) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    @else
                                        <a href="{{ route('booking.index') }}" class="btn btn-sm btn-outline-secondary">
                                            Booking Lagi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="py-3">
                                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                        <p class="mt-2">Anda belum memiliki riwayat booking</p>
                                        <a href="{{ route('booking.index') }}" class="btn btn-primary">
                                            Buat Booking Sekarang
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($bookings->count() > 0)
            <div class="card-footer bg-white">
                {{ $bookings->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection