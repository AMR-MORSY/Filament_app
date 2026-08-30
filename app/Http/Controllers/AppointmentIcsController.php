<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AppointmentIcsController extends Controller
{
    public function __invoke(Request $request, Appointment $appointment): Response
    {
        $ownedByCurrentPatient = $appointment->patient_id
            && Auth::guard('patient')->check()
            && Auth::guard('patient')->id() === $appointment->patient_id;

        abort_unless($request->hasValidSignature() || $ownedByCurrentPatient, 403);

        $appointment->load('doctor.clinic');

        $start = $appointment->appointment_date->copy()->setTimeFromTimeString($appointment->start_time);
        $end = $appointment->appointment_date->copy()->setTimeFromTimeString($appointment->end_time);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Clinic//Booking//EN',
            'BEGIN:VEVENT',
            'UID:appointment-' . $appointment->id . '@' . parse_url(config('app.url'), PHP_URL_HOST),
            'DTSTAMP:' . now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:' . $start->utc()->format('Ymd\THis\Z'),
            'DTEND:' . $end->utc()->format('Ymd\THis\Z'),
            'SUMMARY:' . $this->escape($appointment->doctor->name . ' — ' . $appointment->doctor->clinic->name),
            'LOCATION:' . $this->escape($appointment->doctor->clinic->floor ?? $appointment->doctor->clinic->name),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return response(implode("\r\n", $lines))
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="appointment-' . $appointment->id . '.ics"');
    }

    protected function escape(string $value): string
    {
        return addcslashes($value, ",;\\");
    }
}
