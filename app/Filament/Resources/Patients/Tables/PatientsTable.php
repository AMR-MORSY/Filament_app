<?php

namespace App\Filament\Resources\Patients\Tables;

use App\Support\PhoneNumber;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientsTable
{
    /**
     * The list is an index for finding who to book for, not a patient database
     * to browse. Date of birth, gender, address and verification status are
     * deliberately absent: staff never need them to schedule a visit.
     *
     * Name and phone remain because booking for the wrong person is the worst
     * outcome here — reception has to confirm they have the right patient, and
     * has to be able to call when a slot moves.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): ?string => PhoneNumber::forDisplay($state))
                    ->copyable(),

                TextColumn::make('appointments_count')
                    ->label('Visits')
                    ->counts('appointments')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->recordActions([
                // Not an edit in any real sense: it opens the appointments for
                // this patient. Labelled for what it actually does.
                Action::make('appointments')
                    ->label('Appointments')
                    ->icon('heroicon-o-calendar-days')
                    ->url(fn ($record): string => \App\Filament\Resources\Patients\PatientResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                // No bulk delete: removing a patient would orphan the clinical
                // history attached to their past appointments.
            ]);
    }
}
