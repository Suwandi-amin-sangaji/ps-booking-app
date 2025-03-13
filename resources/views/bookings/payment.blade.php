@extends('layouts.app')

@section('title', 'Pembayaran - PS Rental Booking')


@guest
    <div class="alert alert-warning">
        Anda harus <a href="{{ route('login') }}">login</a> terlebih dahulu untuk melakukan pembayaran.
    </div>
@endguest

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <h1>Pembayaran</h1>
                <p class="text-muted">Silakan selesaikan pembayaran Anda</p>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Detail Pemesanan</h5>
                </div>
                <div class="card-body">
                    <div class="price-item">
                        <span>ID Pemesanan:</span>
                        <span>{{ $booking->booking_code }}</span>
                    </div>
                    <div class="price-item">
                        <span>Jenis PlayStation:</span>
                        <span>{{ $booking->service_name }}</span>
                    </div>
                    <div class="price-item">
                        <span>Tanggal Booking:</span>
                        <span>{{ $booking->formatted_booking_date }}</span>
                    </div>
                    <div class="price-item">
                        <span>Waktu Booking:</span>
                        <span>{{ $booking->time_slot }}</span>
                    </div>
                    <div class="price-item">
                        <span>Nama Pelanggan:</span>
                        <span>{{ $booking->customer_name }}</span>
                    </div>
                    <hr>
                    <div class="price-item">
                        <span>Harga Dasar:</span>
                        <span>{{ $booking->formatted_base_price }}</span>
                    </div>
                    <div class="price-item">
                        <span>Biaya Weekend:</span>
                        <span>{{ $booking->formatted_weekend_surcharge }}</span>
                    </div>
                    <hr>
                    <div class="price-item total-price">
                        <span>Total Pembayaran:</span>
                        <span>{{ $booking->formatted_total_price }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button id="pay-button" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-credit-card me-2"></i> Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Midtrans JS SDK -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const snapToken = "{{ $snapToken }}";
            
            payButton.addEventListener('click', function() {
                // Open Snap payment popup
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('payment.finish') }}?order_id={{ $booking->booking_code }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('payment.finish') }}?order_id={{ $booking->booking_code }}";
                    },
                    onError: function(result) {
                        window.location.href = "{{ route('payment.error') }}";
                    },
                    onClose: function() {
                        // User closed the popup without finishing the payment
                        alert('Anda menutup popup pembayaran sebelum menyelesaikan pembayaran');
                    }
                });
            });
        });
    </script>
@endsection