<?php

namespace App\Filament\Resources\Appointments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('clinic.name')
                    ->label('Clinic'),
                TextColumn::make('patient.name')
                    ->label('Registered Patient')
                    ->formatStateUsing(function ($state) {
                        return $state ?? 'Unregistered';
                    })
                    ->badge() // Converts to badge
                    ->color(function ($state, $record) {
                        return $record->patient ? 'success' : 'danger';
                    }),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
