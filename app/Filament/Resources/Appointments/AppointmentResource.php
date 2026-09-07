<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages\CalendarAppointments;
use App\Filament\Resources\Appointments\Pages\CreateAppointments;
use App\Filament\Resources\Appointments\Pages\EditAppointments;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\RelationManagers\ClinicRelationManager;
use App\Filament\Resources\Appointments\RelationManagers\CreatorRelationManager;
use App\Filament\Resources\Appointments\RelationManagers\DoctorRelationManager;
use App\Filament\Resources\Appointments\RelationManagers\PatientRelationManager;
use App\Filament\Resources\Appointments\Schemas\AppointmentsForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return AppointmentsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PatientRelationManager::class,
            DoctorRelationManager::class,
            CreatorRelationManager::class,
            ClinicRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'calendar' => CalendarAppointments::route('/calendar'),
            'create' => CreateAppointments::route('/create'),
            'edit' => EditAppointments::route('/{record}/edit'),
          
        ];
    }
}
