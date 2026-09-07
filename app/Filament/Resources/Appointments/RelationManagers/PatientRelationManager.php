<?php

namespace App\Filament\Resources\Appointments\RelationManagers;

use App\Support\PhoneNumber;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientRelationManager extends RelationManager
{
    protected static string $relationship = 'patient';

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             TextInput::make('name')
    //                 ->required()
    //                 ->maxLength(255),
    //         ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                    TextColumn::make('phone')
                    ->formatStateUsing(fn (?string $state): ?string => PhoneNumber::forDisplay($state))
                    ->searchable(),
                    TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
           ;
    }
}
