<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText(fn (string $operation): ?string => $operation === 'create'
                        ? 'The invitation to set a password is sent here.'
                        : null),

                // A password is only ever set by its owner. Hidden on create (the
                // invitee sets theirs from the invitation email) and hidden when
                // editing somebody else, so an administrator never learns or
                // chooses a colleague's credentials — they send an invitation
                // instead. Visible only when you are editing your own account.
                TextInput::make('password')
                    ->label('New password')
                    ->password()
                    ->revealable()
                    ->rule(PasswordRule::min(8)->letters()->numbers()->mixedCase()->symbols())
                    ->visible(fn (string $operation, ?User $record): bool => $operation === 'edit'
                        && $record?->is(Auth::user()) === true)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText('Leave blank to keep your current password.'),

                // Shown in place of the field above when editing a colleague, so
                // the absence reads as a deliberate rule rather than a missing form.
                Placeholder::make('password_note')
                    ->label('Password')
                    ->content('Only this person can set their own password. Use "Resend invitation" on the users list to email them a link.')
                    ->visible(fn (string $operation, ?User $record): bool => $operation === 'edit'
                        && $record?->is(Auth::user()) !== true),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->helperText('Determines which panel(s) this user can access and what they can do there.'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Turn off to revoke panel access without deleting the account, so their past records keep a valid author.'),
            ]);
    }
}
