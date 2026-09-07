<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class PatientForm
{
    /**
     * Deliberately empty of patient data.
     *
     * A patient owns their own record: they set their name, contact details,
     * date of birth and address when registering, and change them from their
     * own account. Staff have no reason to read or rewrite any of it, so none
     * of it is rendered here.
     *
     * What remains of this page is the appointments relation manager below it,
     * which is the one thing staff legitimately do on a patient's behalf.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('privacy_note')
                    ->hiddenLabel()
                    ->content('Personal details belong to the patient and are not shown here. Use the appointments below to book, reschedule or cancel a visit for them.'),
            ]);
    }
}
