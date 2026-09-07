<?php

namespace App\Filament\Resources\Patients\RelationManagers;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    public function form(Schema $schema): Schema
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
                    ->live(),
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
                Textarea::make('notes')
                    ->label('Notes'),
                Textarea::make('admin_notes')
                    ->label('Admin Notes'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                        'completed' => 'Completed',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('appointment_date')->date()->sortable(),
                TextColumn::make('start_time')->time('H:i'),
                TextColumn::make('doctor.name'),
                TextColumn::make('clinic.name')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled', 'no_show' => 'danger',
                        'completed' => 'gray',
                    }),
            ])
            ->defaultSort('appointment_date', 'desc')
            ->actions([
                EditAction::make(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
