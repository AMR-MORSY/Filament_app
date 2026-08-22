<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Filament\Resources\Appointments\Widgets\AppointmentCalendarWidget;
use Filament\Resources\Pages\Page;


class CalendarAppointments extends Page
{

    protected static string $resource = AppointmentResource::class;

   
    protected function getHeaderWidgets(): array
    {
        return [AppointmentCalendarWidget::class];
    }

   
}
