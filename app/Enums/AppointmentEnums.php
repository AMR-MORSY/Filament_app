<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AppointmentEnums :  string implements HasLabel //this will automatically shows the labels in filament "select" HTML element and returns enum's cases as the value of the select HTML element
{

    case pending = "Pending";
    case confirmed = "Confirmed";
    case cancelled = "Cancelled";
    case completed = "Completed";
    case no_show = "No Show";
    public function getLabel(): string
    {
        return match ($this) {
            self::pending   => 'Pending',
            self::confirmed => 'Confirmed',
            self::cancelled => 'Cancelled',
            self::completed => 'Completed',
            self::no_show => "No Show"
        };
    }
}
