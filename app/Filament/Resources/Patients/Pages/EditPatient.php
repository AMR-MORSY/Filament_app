<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Despite the class name, this page edits nothing about the patient.
 *
 * PatientForm renders no patient fields, so what is left is the appointments
 * relation manager: the page exists so staff can book, reschedule or cancel a
 * visit on someone's behalf.
 *
 * The delete action is gone too — removing a patient would take their
 * appointment history with them.
 */
class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    public function getTitle(): string
    {
        return 'Appointments for '.$this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Nothing to persist, so the save button would only ever be a no-op.
     */
    protected function getFormActions(): array
    {
        return [];
    }
}
