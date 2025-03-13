@extends('layouts.app')

@section('title', 'Konfirmasi Pemesanan - PS Rental Booking')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <i class="bi bi-check-circle-fill success-icon"></i>
                <h1 class="mt-3">Pembayaran Berhasil!</h1>
                <p class="text-muted">Terima kasih telah melakukan pemesanan</p>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Detail Pemesanan</h5>
                    <p class="card-text text-muted small">Berikut adalah detail pemesanan Anda</p>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label">ID Pemesanan:</span>
                        <span class="fw-medium">{{ $booking->booking_code }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jenis PlayStation:</span>
                        <span>{{ $booking->service_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal:</span>
                        <span>{{ $booking->formatted_booking_date }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Waktu:</span>
                        <span>{{ $booking->time_slot }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nama Pelanggan:</span>
                        <span>{{ $booking->customer_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Pembayaran:</span>
                        <span class="fw-medium">{{ $booking->formatted_total_price }}</span>
                    </div>
                    @if($booking->payment)
                    <div class="detail-item">
                        <span class="detail-label">Metode Pembayaran:</span>
                        <span>{{ ucfirst($booking->payment->payment_type ?? 'N/A') }}</span>
                    </div>
                    @endif
                    <div class="detail-item">
                        <span class="detail-label">Status Pembayaran:</span>
                        <span class="success-status">Berhasil</span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row g-2">
                        <div class="col">
                            <a href="{{ route('booking.index') }}" class="btn btn-outline-primary w-100">
                                Booking Lagi
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('booking.history') }}" class="btn btn-primary w-100">
                                Lihat Riwayat Booking
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection