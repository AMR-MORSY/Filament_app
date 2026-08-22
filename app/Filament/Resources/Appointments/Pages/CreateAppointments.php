<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Actions\BookAppointment;
use App\Actions\SlotUnavailableException;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Doctor;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateAppointments extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    // protected function getHeaderWidgets(): array
    // {
    //     return [AppointmentCalendarWidget::class];
    // }

    // In the Create page (CreateAppointment.php)
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['created_by'] = Auth::user()->id;
    //     $data['booked_via'] = 'staff';
    //     return $data;
    // }

    protected function handleRecordCreation(array $data): Model
    {
        $doctor = Doctor::findOrFail($data['doctor_id']);

        try {
            return app(BookAppointment::class)->execute($doctor, Carbon::parse($data['appointment_date']), $data['start_time'], [
                ...$data,
                $data['created_by'] = Auth::user()->id,
                $data['booked_via'] = 'staff'
            ]);
        } catch (SlotUnavailableException $e) {
            throw ValidationException::withMessages([
                'data.start_time' => $e->getMessage(),
            ]);
        }
    }
}
