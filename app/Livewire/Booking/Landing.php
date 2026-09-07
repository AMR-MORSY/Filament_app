<?php

namespace App\Livewire\Booking;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.site-layout')]
class Landing extends Component
{
    /**
     * How far ahead the hero looks for the soonest opening. A week, because
     * doctor schedules recur weekly — a shorter window silently reports "no
     * openings" for a doctor who simply works Tuesdays.
     */
    protected const HERO_LOOKAHEAD_DAYS = 7;

    /** Ceiling on doctors scanned per render, so the landing page cannot fan out. */
    protected const HERO_DOCTOR_LIMIT = 24;

    public ?int $clinic_id = null;

    public string $when = 'soonest';

    public function search(): void
    {
        $this->redirect(route('search', array_filter([
            'clinic' => $this->clinic_id,
            'when' => $this->when !== 'soonest' ? $this->when : null,
        ])), navigate: true);
    }

    public function render(AvailabilityService $availability)
    {
        $clinics = Clinic::query()
            ->where('is_active', true)
            ->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $openings = $this->openings($availability);

        // Soonest opening per clinic, so a clinic tile says when rather than how many.
        $soonestByClinic = $openings
            ->groupBy(fn ($row) => $row['clinic_id'])
            ->map(fn ($rows) => $rows->first());

        return view('livewire.booking.landing', [
            'clinics' => $clinics,
            'openings' => $openings->take(3),
            'soonestByClinic' => $soonestByClinic,
        ]);
    }

    /**
     * The real next openings across the clinic, soonest first.
     *
     * Walking availability is query-heavy, so the result is cached briefly and
     * keyed to the minute — fresh enough that a slot booked moments ago drops
     * off, cheap enough that a burst of visitors costs one pass.
     *
     * @return \Illuminate\Support\Collection<int, array{doctor: Doctor, clinic_id: int, date: Carbon, time: string, duration: int}>
     */
    protected function openings(AvailabilityService $availability): \Illuminate\Support\Collection
    {
        $key = 'landing:openings:'.now()->format('Y-m-d-H-i');

        $rows = Cache::remember($key, now()->addSeconds(60), function () use ($availability) {
            return Doctor::query()
                ->where('is_active', true)
                ->with('clinic')
                ->orderBy('name')
                ->limit(self::HERO_DOCTOR_LIMIT)
                ->get()
                ->map(function (Doctor $doctor) use ($availability) {
                    for ($i = 0; $i < self::HERO_LOOKAHEAD_DAYS; $i++) {
                        $date = Carbon::today()->addDays($i);
                        $slots = $availability->getAvailableSlots($doctor, $date);

                        if ($slots->isEmpty()) {
                            continue;
                        }

                        return [
                            'doctor_id' => $doctor->id,
                            'clinic_id' => $doctor->clinic_id,
                            'date' => $date->toDateString(),
                            'time' => $slots->first(),
                            'duration' => $doctor->schedules()
                                ->where('day_of_week', $date->dayOfWeek)
                                ->where('is_active', true)
                                ->value('slot_duration') ?? 30,
                        ];
                    }

                    return null;
                })
                ->filter()
                ->sortBy(fn (array $row) => $row['date'].' '.$row['time'])
                ->values()
                ->all();
        });

        // Models are rehydrated outside the cache so the payload stays serialisable.
        $doctors = Doctor::query()
            ->with('clinic')
            ->findMany(collect($rows)->pluck('doctor_id'))
            ->keyBy('id');

        return collect($rows)
            ->map(fn (array $row) => [
                ...$row,
                'doctor' => $doctors->get($row['doctor_id']),
                'date' => Carbon::parse($row['date']),
            ])
            ->filter(fn (array $row) => $row['doctor'] !== null)
            ->values();
    }
}
