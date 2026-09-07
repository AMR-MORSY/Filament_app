<?php

namespace App\Livewire\Booking;

use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Livewire\Component;

/**
 * Step two, written once.
 *
 * Rendered inline on the search results page (so a doctor and a time are chosen
 * on one screen) and standalone on /doctors/{doctor} (so the deep link still
 * works). Both mount this component, so the two can never drift apart.
 */
class SlotPicker extends Component
{
    /** How far ahead mount() will look for the first day that has openings. */
    protected const AUTO_SELECT_DAYS = 14;

    public Doctor $doctor;

    public ?string $date = null;

    public ?string $time = null;

    public int $weekOffset = 0;

    /** Standalone renders its own heading and a full-width action; inline is compact. */
    public bool $standalone = false;

    public function mount(Doctor $doctor, ?string $date = null, ?string $time = null, bool $standalone = false): void
    {
        $this->doctor = $doctor;
        $this->standalone = $standalone;
        $this->time = $time;

        // Land on a day that actually has openings rather than on an empty
        // today. Nothing is more discouraging at step two than "no open slots".
        $this->date = $date ?? $this->firstOpenDate() ?? Carbon::today()->toDateString();

        $this->syncWeekOffsetToDate();
    }

    public function selectDate(string $date): void
    {
        $this->date = $date;
        $this->time = null;

        $this->announce();
    }

    public function selectTime(string $time): void
    {
        $this->time = $time;

        $this->announce();
    }

    /** Let the hosting page keep its step rail in step with the choice. */
    protected function announce(): void
    {
        $this->dispatch(
            'slot-selected',
            date: $this->date,
            time: $this->time,
            duration: $this->slotDurationFor(Carbon::parse($this->date)),
        );
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
        ], [
            'time.required' => 'Pick a time to continue.',
        ]);

        $this->redirect(route('booking.create', [
            'doctor' => $this->doctor->id,
            'date' => $this->date,
            'time' => $this->time,
        ]), navigate: true);
    }

    public function render(AvailabilityService $availability)
    {
        $days = collect(range(0, 6))->map(function (int $i) use ($availability) {
            $date = Carbon::today()->addDays($this->weekOffset * 7 + $i);

            return (object) [
                'date' => $date,
                'iso' => $date->toDateString(),
                'count' => $availability->getAvailableSlots($this->doctor, $date)->count(),
            ];
        });

        $selectedDate = Carbon::parse($this->date);
        $slots = $availability->getAvailableSlots($this->doctor, $selectedDate);

        return view('livewire.booking.slot-picker', [
            'days' => $days,
            'morning' => $slots->filter(fn (string $s) => (int) explode(':', $s)[0] < 12)->values(),
            'afternoon' => $slots->filter(fn (string $s) => (int) explode(':', $s)[0] >= 12)->values(),
            'slotDuration' => $this->slotDurationFor($selectedDate),
        ]);
    }

    protected function slotDurationFor(Carbon $date): int
    {
        return $this->doctor->schedules()
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->value('slot_duration') ?? 30;
    }

    protected function firstOpenDate(): ?string
    {
        $availability = app(AvailabilityService::class);

        for ($i = 0; $i < self::AUTO_SELECT_DAYS; $i++) {
            $date = Carbon::today()->addDays($i);

            if ($availability->getAvailableSlots($this->doctor, $date)->isNotEmpty()) {
                return $date->toDateString();
            }
        }

        return null;
    }

    /** Keep the visible week containing the selected day. */
    protected function syncWeekOffsetToDate(): void
    {
        $days = Carbon::today()->diffInDays(Carbon::parse($this->date), false);

        $this->weekOffset = $days > 0 ? intdiv((int) $days, 7) : 0;
    }
}
