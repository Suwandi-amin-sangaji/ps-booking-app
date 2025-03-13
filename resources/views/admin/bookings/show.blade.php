@extends('layouts.admin')

@section('title', 'Detail Booking')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Booking</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Booking Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Booking</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">ID Booking</label>
                                <div class="fw-bold">{{ $booking->booking_code }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Tanggal Booking</label>
                                <div>{{ $booking->formatted_booking_date }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Waktu Booking</label>
                                <div>{{ $booking->time_slot }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Jenis PlayStation</label>
                                <div>{{ $booking->service_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                                            {!! $booking->status_badge !!}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item status-update" href="#" data-booking-id="{{ $booking->id }}" data-status="pending">
                                                    <span class="badge bg-warning">Pending</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item status-update" href="#" data-booking-id="{{ $booking->id }}" data-status="processing">
                                                    <span class="badge bg-info">Processing</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item status-update" href="#" data-booking-id="{{ $booking->id }}" data-status="paid">
                                                    <span class="badge bg-success">Paid</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item status-update" href="#" data-booking-id="{{ $booking->id }}" data-status="completed">
                                                    <span class="badge bg-primary">Completed</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item status-update" href="#" data-booking-id="{{ $booking->id }}" data-status="cancelled">
                                                    <span class="badge bg-danger">Cancelled</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Tanggal Pemesanan</label>
                                <div>{{ $booking->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Terakhir Diperbarui</label>
                                <div>{{ $booking->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($booking->notes)
                            <div class="mb-3">
                                <label class="form-label text-muted">Catatan</label>
                                <div>{{ $booking->notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Rincian Biaya</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Harga Dasar</th>
                                    <td class="text-end">{{ $booking->formatted_base_price }}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Weekend</th>
                                    <td class="text-end">{{ $booking->formatted_weekend_surcharge }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Total</th>
                                    <td class="text-end fw-bold">{{ $booking->formatted_total_price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer and Payment Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Pelanggan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nama</label>
                        <div>{{ $booking->customer_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <div>{{ $booking->customer_email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Telepon</label>
                        <div>{{ $booking->customer_phone }}</div>
                    </div>
                    @if($booking->user)
                    <div class="mb-3">
                        <label class="form-label text-muted">Akun Terdaftar</label>
                        <div>Ya (ID: {{ $booking->user_id }})</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if($booking->payment)
                    <div class="mb-3">
                        <label class="form-label text-muted">Status Pembayaran</label>
                        <div>{!! $booking->payment->status_badge !!}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Metode Pembayaran</label>
                        <div>{{ ucfirst($booking->payment->payment_type ?? 'N/A') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">ID Transaksi</label>
                        <div>{{ $booking->payment->transaction_id ?? 'N/A' }}</div>
                    </div>
                    @if($booking->payment->paid_at)
                    <div class="mb-3">
                        <label class="form-label text-muted">Tanggal Pembayaran</label>
                        <div>{{ $booking->payment->paid_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                    @else
                    <div class="alert alert-warning mb-0">
                        Belum ada informasi pembayaran
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus booking <span class="fw-bold">{{ $booking->booking_code }}</span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Update
        $('.status-update').on('click', function(e) {
            e.preventDefault();
            
            const bookingId = $(this).data('booking-id');
            const status = $(this).data('status');
            
            $.ajax({
                url: `/admin/bookings/${bookingId}/status`,
                type: 'PATCH',
                data: {
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat memperbarui status.');
                }
            });
        });
    });
</script>
@endsection