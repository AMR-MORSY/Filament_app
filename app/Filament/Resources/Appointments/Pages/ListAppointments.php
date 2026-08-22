<?php

namespace App\Filament\Resources\Appointments\Pages;


use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calendarView')
                ->label('Calendar View')
                ->icon('heroicon-o-calendar')
                ->url(fn () => AppointmentResource::getUrl('calendar')),
            CreateAction::make(),
        ];
    }
}
