<?php

namespace App\Livewire\Booking;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.site-layout')]
class MyAppointments extends Component
{
    public string $tab = 'upcoming';

    public function cancel(int $appointmentId): void
    {
        $appointment = $this->ownedAppointment($appointmentId);

        if ($appointment && in_array($appointment->status, ['pending', 'confirmed'], true)) {
            $appointment->update(['status' => 'cancelled']);
        }
    }

    public function reschedule(int $appointmentId): void
    {
        $appointment = $this->ownedAppointment($appointmentId);

        if (! $appointment) {
            return;
        }

        if (in_array($appointment->status, ['pending', 'confirmed'], true)) {
            $appointment->update(['status' => 'cancelled']);
        }

        $this->redirect(route('doctors.show', $appointment->doctor_id), navigate: true);
    }

    protected function ownedAppointment(int $appointmentId): ?Appointment
    {
        return Appointment::where('id', $appointmentId)
            ->where('patient_id', Auth::guard('patient')->id())
            ->first();
    }

    public function render()
    {
        $patientId = Auth::guard('patient')->id();
        $today = now()->toDateString();

        $base = Appointment::where('patient_id', $patientId)->with('doctor.clinic');

        $counts = [
            'upcoming' => (clone $base)->where('appointment_date', '>=', $today)->whereIn('status', ['pending', 'confirmed'])->count(),
            'past' => (clone $base)->where(function ($q) use ($today) {
                $q->where('appointment_date', '<', $today)->orWhere('status', 'completed');
            })->where('status', '!=', 'cancelled')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
        ];

        $appointments = match ($this->tab) {
            'past' => (clone $base)->where(function ($q) use ($today) {
                $q->where('appointment_date', '<', $today)->orWhere('status', 'completed');
            })->where('status', '!=', 'cancelled')->orderByDesc('appointment_date')->orderByDesc('start_time'),
            'cancelled' => (clone $base)->where('status', 'cancelled')->orderByDesc('appointment_date'),
            default => (clone $base)->where('appointment_date', '>=', $today)->whereIn('status', ['pending', 'confirmed'])->orderBy('appointment_date')->orderBy('start_time'),
        };

        return view('livewire.booking.my-appointments', [
            'counts' => $counts,
            'appointments' => $appointments->get(),
        ]);
    }
}
