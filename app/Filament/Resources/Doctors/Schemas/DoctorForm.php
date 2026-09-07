<?php

namespace App\Filament\Resources\Doctors\Schemas;

use App\Filament\Forms\Components\PhoneField;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->collection('main_image')
                        ->multiple(false)
                        ->maxFiles(1),
                TextInput::make('specialty')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bio')
                    ->required()
                    ->maxLength(255),
                PhoneField::make('phone'),
                TextInput::make('email')
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
