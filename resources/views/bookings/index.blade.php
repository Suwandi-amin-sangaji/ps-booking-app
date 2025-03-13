@extends('layouts.app')

@section('title', 'PS Rental Booking')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">PlayStation Rental Booking</h1>
                <p class="lead">Sewa PlayStation 4 atau PlayStation 5 dengan mudah dan cepat</p>
            </div>

            <div class="row g-4">
                <!-- Step 1: Choose Service -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">1. Pilih Jenis PlayStation</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="service_type" id="ps4"
                                        value="ps4" checked>
                                    <label class="form-check-label d-flex justify-content-between w-100" for="ps4">
                                        <div>
                                            <span class="fw-bold">PlayStation 4</span>
                                            <p class="text-muted mb-0 small">Konsol game generasi ke-8 dari Sony</p>
                                        </div>
                                        <span class="fw-bold">Rp 30.000 / sesi</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="service_type" id="ps5"
                                        value="ps5">
                                    <label class="form-check-label d-flex justify-content-between w-100" for="ps5">
                                        <div>
                                            <span class="fw-bold">PlayStation 5</span>
                                            <p class="text-muted mb-0 small">Konsol game generasi terbaru dari Sony</p>
                                        </div>
                                        <span class="fw-bold">Rp 40.000 / sesi</span>
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div class="me-2">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </div>
                                    <div>
                                        <small>Tambahan biaya Rp 50.000 untuk pemesanan di hari Sabtu atau Minggu.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Choose Date and Time -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">2. Pilih Tanggal & Waktu</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="datepicker" class="form-label">Tanggal Booking</label>
                                <div class="calendar-container">
                                    <input type="text" id="datepicker" class="form-control" placeholder="Pilih tanggal">
                                </div>
                            </div>

                            <div id="time-slots-container" class="mt-4 d-none">
                                <label class="form-label">Waktu Booking</label>
                                <div id="time-slots" class="d-flex flex-wrap gap-2">
                                    <!-- Time slots will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Customer Information -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">3. Informasi Pelanggan</h5>
                        </div>
                        <div class="card-body">
                            <form id="booking-form" action="{{ route('booking.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_type" id="service_type" value="ps4">
                                <input type="hidden" name="booking_date" id="booking_date">
                                <input type="hidden" name="time_slot" id="time_slot">

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="customer_name" class="form-label">Nama Lengkap</label>
                                        <input type="text"
                                            class="form-control @error('customer_name') is-invalid @enderror"
                                            id="customer_name" name="customer_name"
                                            value="{{ old('customer_name', Auth::user()->name ?? '') }}"
                                            placeholder="Masukkan nama lengkap" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="customer_email" class="form-label">Email</label>
                                        <input type="email"
                                            class="form-control @error('customer_email') is-invalid @enderror"
                                            id="customer_email" name="customer_email"
                                            value="{{ old('customer_email', Auth::user()->email ?? '') }}"
                                            placeholder="Masukkan email" required>
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="customer_phone" class="form-label">Nomor Telepon</label>
                                        <input type="tel"
                                            class="form-control @error('customer_phone') is-invalid @enderror"
                                            id="customer_phone" name="customer_phone"
                                            value="{{ old('customer_phone', Auth::user()->phone ?? '') }}"
                                            placeholder="Masukkan nomor telepon" required>
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Summary and Payment -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">4. Ringkasan Pemesanan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="price-item">
                                        <span>Jenis PlayStation:</span>
                                        <span id="summary-service">PlayStation 4</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Tanggal Booking:</span>
                                        <span id="summary-date">-</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Waktu Booking:</span>
                                        <span id="summary-time">-</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="price-item">
                                        <span>Harga Dasar:</span>
                                        <span id="summary-base-price">Rp 30.000</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Biaya Weekend:</span>
                                        <span id="summary-weekend-surcharge">Rp 0</span>
                                    </div>
                                    <hr>
                                    <div class="price-item total-price">
                                        <span>Total:</span>
                                        <span id="summary-total-price">Rp 30.000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <button id="submit-btn" class="btn btn-primary w-100 py-2" disabled>
                                <i class="bi bi-credit-card me-2"></i> Lanjutkan ke Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let selectedDate = null;
            let selectedTimeSlot = null;
            let serviceType = 'ps4';
            let basePrice = 30000;
            let weekendSurcharge = 0;
            let totalPrice = 30000;

            // Initialize datepicker
            const datepicker = flatpickr("#datepicker", {
                locale: "id",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {
                    selectedDate = selectedDates[0];
                    document.getElementById('booking_date').value = dateStr;

                    // Format date for display
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const formattedDate = selectedDate.toLocaleDateString('id-ID', options);
                    document.getElementById('summary-date').textContent = formattedDate;

                    // Reset time slot selection
                    selectedTimeSlot = null;
                    document.getElementById('time_slot').value = '';
                    document.getElementById('summary-time').textContent = '-';

                    // Check availability and load time slots
                    checkAvailability(dateStr, serviceType);

                    updatePrices();
                    updateSubmitButton();
                }
            });

            // Service type selection
            const serviceRadios = document.querySelectorAll('input[name="service_type"]');
            serviceRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    serviceType = this.value;
                    document.getElementById('service_type').value = serviceType;
                    document.getElementById('summary-service').textContent = serviceType === 'ps4' ?
                        'PlayStation 4' : 'PlayStation 5';

                    // If date is already selected, check availability again
                    if (selectedDate) {
                        checkAvailability(document.getElementById('booking_date').value,
                            serviceType);
                    }

                    updatePrices();
                });
            });

            // Check availability and load time slots
            function checkAvailability(date, service) {
                fetch(`/check-availability?booking_date=${date}&service_type=${service}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update weekend surcharge based on response
                        weekendSurcharge = data.is_weekend ? 50000 : 0;
                        updatePrices();

                        // Populate time slots
                        const timeSlotsContainer = document.getElementById('time-slots-container');
                        const timeSlotsDiv = document.getElementById('time-slots');

                        if (data.available_slots && data.available_slots.length > 0) {
                            timeSlotsContainer.classList.remove('d-none');
                            timeSlotsDiv.innerHTML = '';

                            data.available_slots.forEach(slot => {
                                const timeSlotDiv = document.createElement('div');
                                timeSlotDiv.className = 'time-slot';
                                timeSlotDiv.dataset.time = slot.time;
                                timeSlotDiv.innerHTML = `
                    <div class="fw-bold">${slot.slot}</div>
                    <div class="small">${slot.time}</div>
                `;

                                timeSlotDiv.addEventListener('click', function() {
                                    // Remove selected class from all time slots
                                    document.querySelectorAll('.time-slot').forEach(el => {
                                        el.classList.remove('selected');
                                    });

                                    // Add selected class to clicked time slot
                                    this.classList.add('selected');

                                    // Update selected time slot
                                    selectedTimeSlot = this.dataset.time;
                                    document.getElementById('time_slot').value =
                                        selectedTimeSlot;
                                    document.getElementById('summary-time').textContent =
                                        selectedTimeSlot;

                                    updateSubmitButton();
                                });

                                timeSlotsDiv.appendChild(timeSlotDiv);
                            });
                        } else {
                            timeSlotsContainer.classList.remove('d-none');
                            timeSlotsDiv.innerHTML =
                                '<div class="alert alert-warning w-100">Tidak ada slot waktu yang tersedia untuk tanggal dan jenis PlayStation yang dipilih.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking availability:', error);
                        const timeSlotsContainer = document.getElementById('time-slots-container');
                        const timeSlotsDiv = document.getElementById('time-slots');
                        timeSlotsContainer.classList.remove('d-none');
                        timeSlotsDiv.innerHTML =
                            '<div class="alert alert-danger w-100">Terjadi kesalahan saat memeriksa ketersediaan.</div>';
                    });
            }

            // Update prices based on selections
            function updatePrices() {
                // Set base price based on service
                basePrice = serviceType === 'ps4' ? 30000 : 40000;
                document.getElementById('summary-base-price').textContent =
                    `Rp ${basePrice.toLocaleString('id-ID')}`;

                // Update weekend surcharge display
                document.getElementById('summary-weekend-surcharge').textContent =
                    `Rp ${weekendSurcharge.toLocaleString('id-ID')}`;

                // Calculate total
                totalPrice = basePrice + weekendSurcharge;
                document.getElementById('summary-total-price').textContent =
                    `Rp ${totalPrice.toLocaleString('id-ID')}`;
            }

            // Enable/disable submit button
            function updateSubmitButton() {
                const submitBtn = document.getElementById('submit-btn');
                const customerName = document.getElementById('customer_name').value;
                const customerEmail = document.getElementById('customer_email').value;
                const customerPhone = document.getElementById('customer_phone').value;

                submitBtn.disabled = !selectedDate || !selectedTimeSlot || !customerName || !customerEmail || !
                    customerPhone;

                // Add click event if all required fields are filled
                if (!submitBtn.disabled) {
                    submitBtn.onclick = function() {
                        document.getElementById('booking-form').submit();
                    };
                }
            }

            // Listen for changes in customer info fields
            document.getElementById('customer_name').addEventListener('input', updateSubmitButton);
            document.getElementById('customer_email').addEventListener('input', updateSubmitButton);
            document.getElementById('customer_phone').addEventListener('input', updateSubmitButton);
        });
    </script>
@endsection
