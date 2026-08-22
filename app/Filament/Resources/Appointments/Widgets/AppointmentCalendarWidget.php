<?php

namespace App\Filament\Resources\Appointments\Widgets;


use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Doctor;

class AppointmentCalendarWidget extends FullCalendarWidget
{


    public Model|string|null $model = Appointment::class;

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function (Schema $form, array $arguments) {
                    $form->fill([
                        'appointment_date' => $arguments['start'] ?? now()->toDateString(),
                    ]);
                }),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::query()
            ->whereBetween('appointment_date', [$fetchInfo['start'], $fetchInfo['end']])
            ->with(['doctor', 'clinic'])
            ->get()
            ->map(
                fn(Appointment $appointment) => EventData::make()
                    ->id($appointment->id)
                    ->title($appointment->doctor->name . ' — ' . $appointment->patient_name)
                    ->start(Carbon::parse($appointment->appointment_date->toDateString() . ' ' . $appointment->start_time))
                    ->end(Carbon::parse($appointment->appointment_date->toDateString() . ' ' . $appointment->end_time))
                    ->backgroundColor(match ($appointment->status) {
                        'pending' => '#f59e0b',
                        'confirmed' => '#10b981',
                        'cancelled', 'no_show' => '#ef4444',
                        'completed' => '#6b7280',
                    })
                    ->url(AppointmentResource::getUrl('edit', ['record' => $appointment]))
            )
            ->toArray();
    }



    public function getFormSchema(): array
    {
        return [
            Select::make('clinic_id')
                ->relationship('clinic', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('doctor_id', null); // Reset doctor selection when clinic changes,
                }),
            Select::make('doctor_id')
                ->options(function (Get $get) {
                    $clinicId = $get('clinic_id');

                    if (!$clinicId) {
                        return []; // No options if no clinic selected
                    }

                    // Get doctors for the selected clinic
                    return Doctor::where('clinic_id', $clinicId)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->required(),
            Select::make('patient_id')
                ->relationship('patient', 'name')
                ->searchable(),
            DatePicker::make('appointment_date')->required(),
            TimePicker::make('start_time')->seconds(false)->required(),
            TimePicker::make('end_time')->seconds(false)->required(),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                    'completed' => 'Completed',
                    'no_show' => 'No Show',
                ])
                ->default('pending')
                ->required(),
        ];
    }
}
