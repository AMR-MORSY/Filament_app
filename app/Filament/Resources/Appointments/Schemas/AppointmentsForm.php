<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\AppointmentEnums;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;


class AppointmentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('clinic_id')
                    ->relationship('clinic', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('doctor_id', null)),

                Select::make('doctor_id')
                    ->relationship(
                        'doctor',
                        'name',
                        fn(Builder $query, callable $get) => $query->where('clinic_id', $get('clinic_id'))
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set('start_time', null)), // reset slot when doctor changes
                Section::make('Patient')->schema([
                    Select::make('patient_id')
                        ->label('Registered Patient')
                        ->relationship('patient', 'name')
                        ->searchable(['name', 'phone', 'email']) // search by name, phone, or email
                        ->preload()
                        ->live() // triggers the guest fields to hide/show reactively
                        ->helperText('Search by name, phone, or email. Leave empty to enter guest details below.'),
                    Fieldset::make('Guest Details')
                        ->schema([
                            TextInput::make('guest_name')
                                ->required(fn(callable $get) => blank($get('patient_id'))),
                            TextInput::make('guest_phone')
                                ->tel()
                                ->required(fn(callable $get) => blank($get('patient_id'))),
                            TextInput::make('guest_email')
                                ->email(),
                        ])
                        ->visible(fn(callable $get) => blank($get('patient_id'))),
                ]),

                // TextInput::make('guest_name')
                //     ->label('Guest Name')
                //     ->required(),
                // TextInput::make('guest_phone')
                //     ->label('Guest Phone')
                //     ->required(),
                // TextInput::make('guest_email')
                //     ->label('Guest Email')
                //     ->required(),
                DatePicker::make('appointment_date')
                    ->format('Y-m-d')
                    ->label('Appointment Date')
                    ->required()
                    ->afterStateUpdated(fn(callable $set) => $set('start_time', null)), // reset slot when date changes,
                select::make('start_time')
                    ->label('Available Slots')
                    ->options(function (callable $get) {
                        $doctorId = $get('doctor_id');
                        $date = $get('appointment_date');

                        if (! $doctorId || ! $date) {
                            return [];
                        }

                        $doctor = Doctor::find($doctorId);
                        $slots = app(AvailabilityService::class)
                            ->getAvailableSlots($doctor, Carbon::parse($date));

                        return $slots->mapWithKeys(fn(string $slot) => [
                            $slot => Carbon::parse($slot)->format('h:i A'),
                        ]);
                    })
                    // ->rule(function ($get) {
                    //     return function (string $attribute, $value, \Closure $fail) use ($get) {
                    //         $doctor = Doctor::find($get('doctor_id'));
                    //         $date = $get('appointment_date');
                    //         if (! $doctor || ! $date) {
                    //             return;
                    //         }

                    //         $service = app(AvailabilityService::class);
                    //         if (! $service->isSlotAvailable($doctor, Carbon::parse($date), $value)) {
                    //             $fail('This time slot is not available for the selected doctor.');
                    //         }
                    //     };
                    // })

                    ->live()
                    ->required()
                    ->disabled(fn(callable $get) => blank($get('doctor_id')) || blank($get('appointment_date')))
                    ->helperText(fn(callable $get) => blank($get('doctor_id')) || blank($get('appointment_date'))
                        ? 'Select a clinic, doctor, and date first.'
                        : null)
                        ->afterStateUpdated(function (callable $get, callable $set) {
                            $doctor = Doctor::find($get('doctor_id'));
                            $date = $get('appointment_date');
                            $startTime = $get('start_time');
                    
                            if ($doctor && $date && $startTime) {
                                $slotDuration = $doctor->schedules()
                                    ->where('day_of_week', Carbon::parse($date)->dayOfWeek)
                                    ->where('is_active', true)
                                    ->value('slot_duration') ?? 30;
                    
                                $set('end_time', Carbon::parse($startTime)->addMinutes($slotDuration)->format('H:i'));
                            }
                        }),
                TimePicker::make('end_time')
                    ->label('End Time')
                    ->required()   ->disabled()
                    ->dehydrated(),
                Textarea::make('notes')
                    ->label('Notes')
                   ,
                Textarea::make('admin_notes')
                    ->label('Admin Notes')
                   ,
                Select::make('status')
                    ->label('Status')
                    ->options(AppointmentEnums::class)
                    ->required(),
            ]);
    }
}
