<?php

namespace App\Filament\Resources\Doctors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class DoctorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                SpatieMediaLibraryImageColumn::make('avatar')
                ->collection('main_image')
                    ->conversion('thumb'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('specialty')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bio')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
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
