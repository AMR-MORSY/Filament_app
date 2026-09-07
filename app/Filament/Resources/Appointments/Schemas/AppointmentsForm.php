<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Filament\Forms\Components\PhoneField;

use App\Enums\AppointmentEnums;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AppointmentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Grid::make()
                        ->columns(2)
                        ->schema([
                            Section::make('Clinic & Doctor')->schema([
                                Select::make('clinic_id')
                                    ->relationship('clinic', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('doctor_id', null)),

                                Hidden::make('created_by')
                                    ->default(fn() => auth('web')->user()?->id),


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


                                Select::make('status')
                                    ->label('Status')
                                    ->options(AppointmentEnums::class),
                            ])->columnSpan([

                                'md' => 2,
                                '2xl' => 1,

                            ]),
                            Section::make('Appointment Details')->schema([
                                DatePicker::make('appointment_date')
                                    ->format('Y-m-d')
                                    ->label('Appointment Date')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('start_time', null)), // reset slot when date changes,
                                select::make('start_time')
                                    ->label('Available Slots')
                                    ->afterStateHydrated(function (Select $component, $state) {
                                        if (filled($state)) {
                                            $component->state(Carbon::parse($state)->format('H:i'));
                                        }
                                    })
                                    ->options(function (callable $get, ?Model $record) {
                                        $doctorId = $get('doctor_id');
                                        $date = $get('appointment_date');

                                        if (! $doctorId || ! $date) {
                                            return [];
                                        }

                                        $doctor = Doctor::find($doctorId);
                                        $slots = app(AvailabilityService::class)
                                            ->getAvailableSlots($doctor, Carbon::parse($date), ignoreAppointmentId: $record?->id);

                                        $options = $slots->mapWithKeys(fn(string $slot) => [
                                            $slot => Carbon::parse($slot)->format('h:i A'),
                                        ]);

                                        // Belt-and-suspenders: if the appointment's own slot somehow still isn't
                                        // in the list (e.g. schedule changed since booking), add it back explicitly
                                        // and flag it, rather than leaving the field unselected.
                                        if ($record && $record->start_time) {
                                            $currentSlot = Carbon::parse($record->start_time)->format('H:i');

                                            if (! $options->has($currentSlot)) {
                                                $options->put(
                                                    $currentSlot,
                                                    Carbon::parse($currentSlot)->format('h:i A') . ' (currently booked)'
                                                );
                                            }
                                        }

                                        return $options->sortKeys();
                                    })

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
                                    ->required()->disabled()
                                    ->dehydrated(),

                            ])->columnSpan([

                                'md' => 2,
                                '2xl' => 1,

                            ]),
                            Textarea::make('admin_notes')
                                ->label('Admin Notes')->columnSpan(2),

                        ]),




                    Hidden::make('created_by')
                        ->default(fn() => auth('web')->user()?->id),


                ]),

                Section::make('Patient')->schema([
                    Select::make('patient_id')
                        ->label('Registered Patient')
                        ->relationship('patient', 'name')
                        ->searchable(['name', 'phone', 'email']) // search by name, phone, or email
                        ->preload()
                        ->live() // triggers the guest fields to hide/show reactively
                        ->helperText('Search by name, phone, or email. Leave empty to enter guest details below.')->columnSpan(2),
                    Fieldset::make('Guest Details')
                        ->schema([

                            TextInput::make('guest_name')
                                ->required(fn(callable $get) => blank($get('patient_id')))
                                ->columnSpan([

                                    'sm' => 2,
                                    '2xl' => 1,
                                ]),
                            TextInput::make('guest_email')
                                ->email()->columnSpan([
                                    'sm' => 2,
                                    '2xl' => 1,
                                ]),






                            PhoneField::make(
                                name: 'guest_phone',
                                label: 'Guest phone',
                                required: fn(callable $get) => blank($get('patient_id')),
                            )->columnSpan([
                                'sm' => 2,
                                
                            ]),
                        ])
                        ->visible(fn(callable $get) => blank($get('patient_id'))),
                ]),






            ]);
    }
}
