<?php

namespace App\Livewire\Booking;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.site-layout')]
class Confirmation extends Component
{
    public Appointment $appointment;

    public bool $accountEmailSent = false;

    public function mount(Appointment $appointment): void
    {
        $ownedByCurrentPatient = $appointment->patient_id
            && Auth::guard('patient')->check()
            && Auth::guard('patient')->id() === $appointment->patient_id;

        abort_unless(request()->hasValidSignature() || $ownedByCurrentPatient, 403);

        $this->appointment = $appointment->load('doctor.clinic', 'patient');
    }

    public function resendAccountEmail(): void
    {
        if (! $this->appointment->patient || $this->appointment->patient->hasVerifiedEmail()) {
            return;
        }

        Password::broker('patients')->sendResetLink(['email' => $this->appointment->patient->email]);

        $this->accountEmailSent = true;
    }

    public function render()
    {
        return view('livewire.booking.confirmation');
    }
}
