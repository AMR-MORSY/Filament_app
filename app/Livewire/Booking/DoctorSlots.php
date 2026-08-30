<?php

namespace App\Livewire\Booking;

use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.site-layout')]
class DoctorSlots extends Component
{
    public Doctor $doctor;

    #[Url]
    public ?string $date = null;

    #[Url]
    public ?string $time = null;

    public int $weekOffset = 0;

    public function mount(Doctor $doctor): void
    {
        abort_unless($doctor->is_active, 404);

        $this->doctor = $doctor;
        $this->date = $this->date ?? Carbon::today()->toDateString();
    }

    public function selectDate(string $date): void
    {
        $this->date = $date;
        $this->time = null;
    }

    public function selectTime(string $time): void
    {
        $this->time = $time;
    }

    public function prevWeek(): void
    {
        if ($this->weekOffset > 0) {
            $this->weekOffset--;
        }
    }

    public function nextWeek(): void
    {
        $this->weekOffset++;
    }

    public function continueToBooking(): void
    {
        $this->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $this->redirect(route('booking.create', [
            'doctor' => $this->doctor->id,
            'date' => $this->date,
            'time' => $this->time,
        ]), navigate: true);
    }

    public function render(AvailabilityService $availability)
    {
        $days = collect(range(0, 6))->map(function (int $i) {
            $date = Carbon::today()->addDays($this->weekOffset * 7 + $i);

            return (object) [
                'date' => $date,
                'iso' => $date->toDateString(),
                'count' => app(AvailabilityService::class)->getAvailableSlots($this->doctor, $date)->count(),
            ];
        });

        $selectedDate = Carbon::parse($this->date);
        $slots = $availability->getAvailableSlots($this->doctor, $selectedDate);

        $morning = $slots->filter(fn (string $s) => (int) explode(':', $s)[0] < 12)->values();
        $afternoon = $slots->filter(fn (string $s) => (int) explode(':', $s)[0] >= 12)->values();

        $slotDuration = $this->doctor->schedules()
            ->where('day_of_week', $selectedDate->dayOfWeek)
            ->where('is_active', true)
            ->value('slot_duration') ?? 30;

        return view('livewire.booking.doctor-slots', [
            'days' => $days,
            'morning' => $morning,
            'afternoon' => $afternoon,
            'slotDuration' => $slotDuration,
        ]);
    }
}
