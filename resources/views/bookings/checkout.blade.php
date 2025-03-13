@extends('layouts.app')

@section('title', 'Checkout - PS Rental Booking')

@section('content')
    <a href="{{ route('booking.index') }}" class="back-link d-inline-flex align-items-center mb-4">
        <i class="bi bi-arrow-left me-2"></i> Kembali
    </a>
    
    <h1 class="text-center mb-4">Checkout</h1>
    
    <form action="{{ route('booking.process-checkout', $booking) }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Customer Information Card -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informasi Pelanggan</h5>
                        <p class="card-text text-muted small">Masukkan data diri Anda untuk pemesanan</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                id="customer_name" name="customer_name" value="{{ $bookingData['customer_name'] ?? '' }}"  
                                placeholder="Masukkan nama lengkap" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                id="customer_email" name="customer_email" value="{{ $bookingData['customer_email'] ?? '' }}" 
                                placeholder="Masukkan email" required>
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                id="customer_phone" name="customer_phone" value="{{ $bookingData['customer_phone'] ?? '' }}" 
                                placeholder="Masukkan nomor telepon" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Card -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Ringkasan Pemesanan</h5>
                        <p class="card-text text-muted small">Detail pemesanan dan pembayaran Anda</p>
                    </div>
                    <div class="card-body">
                        <div class="price-item">
                            <span>Jenis PlayStation:</span>
                            <span>{{ $booking->service_name }}</span>
                        </div>
                        <div class="price-item">
                            <span>Tanggal Booking:</span>
                            <span>{{ $booking->formatted_booking_date }}</span>
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
                            <span>Total:</span>
                            <span>{{ $booking->formatted_total_price }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-credit-card me-2"></i> Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection