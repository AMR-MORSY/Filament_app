<?php

namespace App\Filament\Resources\Doctors\RelationManagers;


use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('day_of_week')
                ->options(\App\Models\DoctorSchedule::DAYS)
                ->required(),
            TimePicker::make('start_time')
                ->seconds(false)
                ->required(),
            TimePicker::make('end_time')
                ->seconds(false)
                ->required()
                ->after('start_time'),
           TextInput::make('slot_duration')
                ->numeric()
                ->default(30)
                ->suffix('minutes')
                ->required(),
           Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->formatStateUsing(fn (int $state) => \App\Models\DoctorSchedule::DAYS[$state]),
                TextColumn::make('start_time')->time('H:i'),
                TextColumn::make('end_time')->time('H:i'),
                TextColumn::make('slot_duration')->suffix(' min'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('day_of_week')
            ->headerActions([CreateAction::make()])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
