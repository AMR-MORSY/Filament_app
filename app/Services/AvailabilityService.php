<?php

namespace App\Services;

use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Collection;



class AvailabilityService
{

    /**
     * Get available bookable time slots for a doctor on a specific date.
     *
     * @return Collection<int, string> e.g. ["09:00", "09:30", "10:00"]
     */

    public function getAvailableSlots(Doctor $doctor, Carbon $date,?int $ignoreAppointmentId = null): Collection
    {

        // 1. Check for a date-specific exception first (overrides weekly schedule)
        $exception = $doctor->exceptions()->where('date', $date->toString())->first(); //////to_string function, returns the string date from the carbon instance

        if ($exception && !$exception->is_available) // doctor is off this day, no slots at all
        {
            return collect();
        }

        /////take the start_time,end_time, and slot duration from schedule_exceptions table 
        if ($exception && $exception->is_available && $exception->start_time && $exception->end_time) {
            $startTime = Carbon::parse($exception->start_time);  ////carbon::parse() function returns a carbon instance from a string date
            $endTime = Carbon::parse($exception->end_time);
            $slotDuration = $doctor->schedules()->where('is_active', true)->value('slot_duration') ?? 30;
        }
        /////take the start_time,end_time, and slot duration from normal doctor_schedules table
        // 2. Fall back to the doctor's regular weekly schedule
        else {

            $schedule = $doctor->schedules()
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (! $schedule) {
                return collect(); // doctor doesn't work this day of week
            }

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $slotDuration = $schedule->slot_duration;
        }

        // 3. Generate all theoretical slots for the working window
        $slots = $this->generateSlots($startTime, $endTime, $slotDuration);

        // 4. Remove slots that collide with existing (non-cancelled) appointments
        $bookedTimes = $doctor->appointments()
            ->whereDate('appointment_date', $date->toDateString()) ////toDateString() it is a carbon instance function
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))////ignore the appointment id if it is passed, this is useful when we are editing an existing appointment and we want to ignore the current appointment from the booked times
            ->pluck('start_time')
            ->map(fn($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

            $slots = $slots->reject(fn (string $slot) => in_array($slot, $bookedTimes));

          // 5. If the date is today, remove slots that have already passed (+ buffer)
          if ($date->isToday()) {
            $cutoff = now()->addMinutes(config('clinic.booking_lead_minutes', 60));
            $slots = $slots->reject(
                fn (string $slot) => Carbon::parse($date->toDateString() . ' ' . $slot)->lessThan($cutoff)
            );
        }

        return $slots->values();
    }

    /**
     * Break a start/end window into fixed-size slot strings.
     */
    protected function generateSlots(Carbon $start, Carbon $end, int $slotDuration): Collection
    {
        $slots = collect();
        $cursor = $start->copy();

        while ($cursor->copy()->addMinutes($slotDuration)->lessThanOrEqualTo($end)) {
            $slots->push($cursor->format('H:i'));
            $cursor->addMinutes($slotDuration);
        }

        return $slots;
    }


    public function isSlotAvailable(Doctor $doctor, Carbon $date, string $startTime, ?int $ignoreAppointmentId = null)
    {
        return $this->getAvailableSlots($doctor, $date)->contains($startTime);

    }
}
