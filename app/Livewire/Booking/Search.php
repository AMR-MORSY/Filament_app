<?php

namespace App\Livewire\Booking;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.site-layout')]
class Search extends Component
{
    #[Url(as: 'clinic')]
    public ?int $clinicId = null;

    #[Url]
    public array $timeOfDay = [];

    #[Url]
    public string $window = 'week';

    /**
     * Which result card has its slot picker open.
     * null = not resolved yet (the soonest result opens itself), 0 = all closed.
     */
    public ?int $expandedDoctorId = null;

    /** Mirrors the open picker's choice so the step rail can report it. */
    public ?string $railDate = null;

    public ?string $railTime = null;

    public ?int $railDuration = null;

    public function mount(): void
    {
        $when = request()->query('when');

        $this->window = match ($when) {
            'today' => 'today',
            '3days' => '3days',
            default => $this->window,
        };
    }

    public function toggleClinic(int $clinicId): void
    {
        $this->clinicId = $this->clinicId === $clinicId ? null : $clinicId;
        $this->resetExpansion();
    }

    public function toggleTimeOfDay(string $slot): void
    {
        if (in_array($slot, $this->timeOfDay, true)) {
            $this->timeOfDay = array_values(array_diff($this->timeOfDay, [$slot]));
        } else {
            $this->timeOfDay[] = $slot;
        }

        $this->resetExpansion();
    }

    public function updatedWindow(): void
    {
        $this->resetExpansion();
    }

    /** Open one doctor's times in place, closing whichever was open. */
    public function toggleDoctor(int $doctorId): void
    {
        $this->expandedDoctorId = $this->expandedDoctorId === $doctorId ? 0 : $doctorId;

        $this->railDate = null;
        $this->railTime = null;
        $this->railDuration = null;
    }

    #[On('slot-selected')]
    public function onSlotSelected(?string $date = null, ?string $time = null, ?int $duration = null): void
    {
        $this->railDate = $date;
        $this->railTime = $time;
        $this->railDuration = $duration;
    }

    /** After a filter change the previous selection no longer means anything. */
    protected function resetExpansion(): void
    {
        $this->expandedDoctorId = null;
        $this->railDate = null;
        $this->railTime = null;
        $this->railDuration = null;
    }

    protected function windowDays(): int
    {
        return match ($this->window) {
            'today' => 1,
            '3days' => 3,
            default => 7,
        };
    }

    public function render(AvailabilityService $availability)
    {
        $clinics = Clinic::query()->where('is_active', true)->orderBy('name')->get();

        $doctors = Doctor::query()
            ->where('is_active', true)
            ->with('clinic')
            ->when($this->clinicId, fn ($q) => $q->where('clinic_id', $this->clinicId))
            ->orderBy('name')
            ->get()
            ->map(function (Doctor $doctor) use ($availability) {
                [$nextDay, $slots] = $this->nextAvailability($doctor, $availability);

                return (object) [
                    'doctor' => $doctor,
                    'nextDay' => $nextDay,
                    'slots' => $slots,
                ];
            })
            ->filter(fn ($row) => $row->nextDay !== null)
            ->sortBy(fn ($row) => $row->nextDay->timestamp)
            ->values();

        // The soonest result opens itself, so step two is already on screen.
        // Resolving it back onto the property makes the next toggle a real toggle.
        if ($this->expandedDoctorId === null) {
            $this->expandedDoctorId = $doctors->first()?->doctor->id ?? 0;
        }

        return view('livewire.booking.search', [
            'clinics' => $clinics,
            'results' => $doctors,
        ]);
    }

    protected function nextAvailability(Doctor $doctor, AvailabilityService $availability): array
    {
        $days = $this->windowDays();

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::today()->addDays($i);
            $slots = $availability->getAvailableSlots($doctor, $date);

            if (! empty($this->timeOfDay)) {
                $slots = $slots->filter(function (string $slot) {
                    $hour = (int) explode(':', $slot)[0];
                    $isMorning = $hour < 12;

                    return (in_array('morning', $this->timeOfDay, true) && $isMorning)
                        || (in_array('afternoon', $this->timeOfDay, true) && ! $isMorning);
                })->values();
            }

            if ($slots->isNotEmpty()) {
                return [$date, $slots];
            }
        }

        return [null, collect()];
    }
}
