@extends('layouts.admin')

@section('title', 'Daftar Booking')

@section('styles')
<style>
    .filter-form {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Booking</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-calendar3"></i> Kalender
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-form">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_range" class="form-label">Rentang Tanggal</label>
                <input type="text" id="date_range" name="date_range" class="form-control" value="{{ request('date_range') }}" placeholder="Pilih rentang tanggal">
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Pencarian</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari booking code, nama, email...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID Booking</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Pelanggan</th>
                            <th>Jenis</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-decoration-none fw-medium">
                                    {{ $booking->booking_code }}
                                </a>
                            </td>
                            <td>{{ $booking->booking_date->format('d/m/Y') }}</td>
                            <td>{{ $booking->time_slot }}</td>
                            <td>
                                <div>{{ $booking->customer_name }}</div>
                                <div class="small text-muted">{{ $booking->customer_email }}</div>
                            </td>
                            <td>{{ $booking->service_name }}</td>
                            <td>{{ $booking->formatted_total_price }}</td>
                            <td>
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-booking" data-bs-toggle="modal" data-bs-target="#deleteModal" data-booking-id="{{ $booking->id }}" data-booking-code="{{ $booking->booking_code }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data booking yang ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} data
                </div>
                <div>
                    {{ $bookings->appends(request()->query())->links() }}
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
                    Apakah Anda yakin ingin menghapus booking <span id="booking-code-display" class="fw-bold"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-form" action="" method="POST">
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
        // Date Range Picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                fromLabel: 'Dari',
                toLabel: 'Sampai',
                customRangeLabel: 'Kustom',
                weekLabel: 'M',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1
            },
            autoUpdateInput: false,
            opens: 'left'
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Delete Modal
        $('.delete-booking').on('click', function() {
            const bookingId = $(this).data('booking-id');
            const bookingCode = $(this).data('booking-code');
            
            $('#booking-code-display').text(bookingCode);
            $('#delete-form').attr('action', `/admin/bookings/${bookingId}`);
        });

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