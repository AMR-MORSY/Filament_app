<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Never seen by anyone, including the admin creating the account. It only
        // keeps the column non-null until the invitee sets their own password.
        // The model's 'hashed' cast hashes it on assignment.
        $data['password'] = Str::random(40);

        return $data;
    }

    protected function afterCreate(): void
    {
        $status = $this->record->sendStaffInvitation();

        if ($status === Password::RESET_LINK_SENT) {
            Notification::make()
                ->title('Invitation sent')
                ->body("{$this->record->name} can set a password using the link emailed to {$this->record->email}.")
                ->success()
                ->send();

            return;
        }

        // The account exists either way; say so, rather than implying it failed.
        Notification::make()
            ->title('Account created, but the invitation was not sent')
            ->body(__($status).' Use "Resend invitation" on the users list to try again.')
            ->warning()
            ->persistent()
            ->send();
    }
}
