<?php

namespace App\Actions;

use App\Actions\SlotUnavailableException;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BookAppointment
{

    public function __construct(protected AvailabilityService $availabilityService) {}

    public function execute(Doctor $doctor, Carbon $date, string $startTime, array $data): Appointment
    {
        // Pre-check: fast, friendly rejection for the common case (slot already gone before we even try)
        if (! $this->availabilityService->isSlotAvailable($doctor, $date, $startTime)) {
            throw new SlotUnavailableException();
        }

        $slotDuration = $doctor->schedules()
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->value('slot_duration') ?? 30;

        $endTime = Carbon::parse($startTime)->addMinutes($slotDuration)->format('H:i');

        try {
            return DB::transaction(function () use ($doctor, $date, $startTime, $endTime, $data) {
                return Appointment::create([
                    ...$data,
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $doctor->clinic_id,
                    'appointment_date' => $date->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $data['status'] ?? 'pending', // caller can override, defaults to pending
                ]);
            });
        } catch (QueryException $e) {
            // 1062 = MySQL duplicate entry error code, triggered by our unique constraint
            if ($e->errorInfo[1] == 1062) {
                throw new SlotUnavailableException();
            }

            throw $e; // some other DB error — let it bubble up as a real 500
        }
    }
}
