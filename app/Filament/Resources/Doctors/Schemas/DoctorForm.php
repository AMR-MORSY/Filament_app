<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('specialty')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bio')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->maxLength(255),
                TextInput::make('photo')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->default(true),
                    Select::make('clinic_id')
                    ->required()
                    ->relationship('clinic', 'name')
                    ->label('Clinic')   
                    ->placeholder('Select Clinic'),
            ]);
    }
}
