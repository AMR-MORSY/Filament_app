<?php

namespace App\Livewire\Booking;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL as UrlGenerator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.site-layout')]
class BookingForm extends Component
{
    public Doctor $doctor;

    #[Url]
    public string $date = '';

    #[Url]
    public string $time = '';

    public bool $slotTaken = false;

    public string $mode = 'guest';

    // Guest / patient details
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $date_of_birth = '';
    public string $notes = '';
    public bool $createAccount = false;

    // Inline sign-in
    public string $signin_email = '';
    public string $signin_password = '';

    public function mount(Doctor $doctor): void
    {
        abort_unless($doctor->is_active, 404);
        abort_if($this->date === '' || $this->time === '', 404);

        $this->doctor = $doctor;
        $this->refreshAvailability();

        if (Auth::guard('patient')->check()) {
            $patient = Auth::guard('patient')->user();
            $this->name = $patient->name;
            $this->phone = $patient->phone;
            $this->email = $patient->email;
        }
    }

    protected function refreshAvailability(): void
    {
        $this->slotTaken = ! app(AvailabilityService::class)->isSlotAvailable(
            $this->doctor,
            Carbon::parse($this->date),
            $this->time
        );
    }

    protected function slotDuration(): int
    {
        return $this->doctor->schedules()
            ->where('day_of_week', Carbon::parse($this->date)->dayOfWeek)
            ->where('is_active', true)
            ->value('slot_duration') ?? 30;
    }

    public function signIn(): void
    {
        $credentials = $this->validate([
            'signin_email' => 'required|email',
            'signin_password' => 'required',
        ]);

        if (! Auth::guard('patient')->attempt([
            'email' => $credentials['signin_email'],
            'password' => $credentials['signin_password'],
        ])) {
            $this->addError('signin_email', 'Those credentials don\'t match our records.');

            return;
        }

        session()->regenerate();

        $patient = Auth::guard('patient')->user();
        $this->name = $patient->name;
        $this->phone = $patient->phone;
        $this->email = $patient->email;
        $this->mode = 'guest';
    }

    public function confirmBooking(): void
    {
        $this->refreshAvailability();

        if ($this->slotTaken) {
            return;
        }

        $isAuthenticated = Auth::guard('patient')->check();

        $data = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient = $isAuthenticated ? Auth::guard('patient')->user() : null;

        if (! $isAuthenticated && $this->createAccount) {
            $this->validate([
                'email' => 'required|email|unique:patients,email',
                'phone' => 'unique:patients,phone',
            ], [
                'email.unique' => 'An account already exists for this email — sign in instead to book.',
                'phone.unique' => 'An account already exists with this phone number — sign in instead to book.',
            ]);

            $patient = Patient::create([
                'name' => $data['name'],
                'email' => $this->email,
                'phone' => $data['phone'],
                'date_of_birth' => $data['date_of_birth'] ?: null,
                'password' => Hash::make(Str::random(40)),
            ]);

            event(new Registered($patient));
            Password::broker('patients')->sendResetLink(['email' => $patient->email]);
        }

        try {
            $appointment = Appointment::create([
                'clinic_id' => $this->doctor->clinic_id,
                'doctor_id' => $this->doctor->id,
                'patient_id' => $patient?->id,
                'guest_name' => $patient ? null : $data['name'],
                'guest_phone' => $patient ? null : $data['phone'],
                'guest_email' => $patient ? null : $this->email,
                'appointment_date' => $this->date,
                'start_time' => $this->time,
                'end_time' => Carbon::parse($this->time)->addMinutes($this->slotDuration())->format('H:i'),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'booked_via' => $patient ? 'patient_self' : 'guest',
            ]);
        } catch (QueryException) {
            $this->slotTaken = true;

            return;
        }

        $this->redirect(UrlGenerator::signedRoute('booking.confirmation', ['appointment' => $appointment->id]));
    }

    public function render()
    {
        return view('livewire.booking.booking-form', [
            'slotDuration' => $this->slotDuration(),
        ]);
    }
}
