<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;

    /**
     * No create action: patients register themselves on the public site, and
     * PatientResource::canCreate() would refuse anyway.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
