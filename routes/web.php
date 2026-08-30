<?php

use App\Http\Controllers\AppointmentIcsController;
use App\Livewire\Booking\BookingForm;
use App\Livewire\Booking\Confirmation;
use App\Livewire\Booking\DoctorSlots;
use App\Livewire\Booking\Landing;
use App\Livewire\Booking\MyAppointments;
use App\Livewire\Booking\Search;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class)->name('home');
Route::get('/search', Search::class)->name('search');
Route::get('/doctors/{doctor}', DoctorSlots::class)->name('doctors.show');
Route::get('/book/{doctor}', BookingForm::class)->name('booking.create');
Route::get('/appointments/{appointment}/confirmation', Confirmation::class)->name('booking.confirmation');
Route::get('/appointments/{appointment}/ics', AppointmentIcsController::class)->name('appointments.ics');

Route::middleware(['auth:patient', 'patient.verified'])->group(function () {
    Route::get('/my-appointments', MyAppointments::class)->name('appointments.index');
});

require_once __DIR__ . '/auth.php';
