@extends('layouts.admin')

@section('title', 'Kalender Booking')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kalender Booking</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-list"></i> Daftar Booking
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Booking</label>
                        <div id="event-booking-code" class="fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <div id="event-customer-name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis PlayStation</label>
                        <div id="event-service-type"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div id="event-status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a id="event-detail-link" href="#" class="btn btn-primary">Lihat Detail</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            events: {
                url: '{{ route("admin.bookings.calendar.events") }}',
                failure: function() {
                    alert('Gagal memuat data booking');
                }
            },
            eventClick: function(info) {
                const event = info.event;
                const props = event.extendedProps;
                
                $('#event-booking-code').text(props.booking_code);
                $('#event-customer-name').text(props.customer_name);
                $('#event-service-type').text(props.service_type);
                
                // Set status with badge
                let statusBadge = '';
                switch (props.status) {
                    case 'pending':
                        statusBadge = '<span class="badge bg-warning">Pending</span>';
                        break;
                    case 'processing':
                        statusBadge = '<span class="badge bg-info">Processing</span>';
                        break;
                    case 'paid':
                        statusBadge = '<span class="badge bg-success">Paid</span>';
                        break;
                    case 'completed':
                        statusBadge = '<span class="badge bg-primary">Completed</span>';
                        break;
                    case 'cancelled':
                        statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                        break;
                    default:
                        statusBadge = '<span class="badge bg-secondary">' + props.status + '</span>';
                }
                $('#event-status').html(statusBadge);
                
                // Set detail link
                $('#event-detail-link').attr('href', '/admin/bookings/' + event.id);
                
                // Show modal
                const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                eventModal.show();
            }
        });
        
        calendar.render();
    });
</script>
@endsection