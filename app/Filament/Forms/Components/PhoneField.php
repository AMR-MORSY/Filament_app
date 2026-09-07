<?php

namespace App\Filament\Forms\Components;

use App\Rules\ValidPhoneNumber;
use App\Support\PhoneNumber;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;

/**
 * The registration form's phone control, for Filament schemas.
 *
 * A country selector fused to a national number under a single "Phone" label,
 * validated by libphonenumber against that country and stored as one E.164
 * string — so a number typed in the admin panel is indistinguishable from one
 * a patient typed themselves.
 *
 * FusedGroup is what joins the two into one bordered control rather than two
 * separate boxes; the selector is never persisted, only the phone column is.
 *
 * Usage:
 *
 *     ->components([
 *         PhoneField::make('phone'),
 *     ])
 */
class PhoneField
{
    /**
     * @param  string  $name  The column holding the E.164 value.
     */
    public static function make(
        string $name = 'phone',
        string $label = 'Phone',
        bool|Closure $required = true,
    ): FusedGroup {
        $countryKey = $name . '_country';

        return FusedGroup::make([
            Select::make($countryKey)
                ->hiddenLabel()
                ->placeholder('Country')
                ->options(fn(): array => collect(config('clinic.countries', []))
                    ->map(fn(string $country, string $iso): string => PhoneNumber::dialCode($iso) . ' · ' . $country)
                    ->all())
                ->default(config('clinic.default_country'))
                ->selectablePlaceholder(false)
                ->searchable()
                ->native(false)
                ->required($required)
                // Not a column on any table: it only tells us how to read the
                // number typed beside it.
                ->dehydrated(false)
                // On edit, recover the country from the stored number so the
                // value round-trips instead of falling back to the default.
                ->afterStateHydrated(function (Select $component, ?Model $record) use ($name): void {
                    $component->state(
                        PhoneNumber::regionOf($record?->getAttribute($name))
                            ?? config('clinic.default_country')
                    );
                })
                ->columnSpan(2),

            TextInput::make($name)
                ->hiddenLabel()
                ->placeholder('1099988877')
                ->tel()
                ->required($required)
                ->rule(fn(Get $get): ValidPhoneNumber => new ValidPhoneNumber($get($countryKey)))
                // Show the national part while editing...
                ->formatStateUsing(fn(?string $state): ?string => PhoneNumber::nationalOf($state) ?? $state)
                // ...and write back the full E.164 value.
                ->dehydrateStateUsing(fn(?string $state, Get $get): ?string => PhoneNumber::toE164($state, $get($countryKey)))
                ->columnSpan(2),
        ])
            ->label($label)
            // FusedGroup only draws the asterisk; the real rules live on the
            // two fields inside it.
            ->markAsRequired($required)
            ->columns(4)
            ->helperText('Digits only — the country code comes from the selector.');
    }
}
