<?php

namespace App\Support;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * One place that turns "a country plus whatever the patient typed" into the
 * single E.164 string stored in the phone column.
 *
 * Kept separate from the validation rule so the registration form, the guest
 * booking form and the admin panel can all normalise the same way.
 */
class PhoneNumber
{
    /**
     * The international dial code for a region, e.g. 'EG' => '+20'.
     *
     * Read from libphonenumber rather than hardcoded, so the label beside the
     * country in a select can never disagree with how the number is parsed.
     */
    public static function dialCode(string $region): ?string
    {
        $code = PhoneNumberUtil::getInstance()->getCountryCodeForRegion($region);

        return $code ? '+'.$code : null;
    }

    /**
     * Normalise a national number to E.164, or null if it is not a valid number
     * for that region.
     *
     * The trunk prefix is handled by libphonenumber: Egypt's leading 0 is
     * dropped, Italy's is kept. A hand-rolled ltrim() gets the second case wrong.
     */
    public static function toE164(?string $number, ?string $region): ?string
    {
        if (blank($number) || blank($region)) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse($number, $region);
        } catch (NumberParseException) {
            return null;
        }

        if (! $util->isValidNumberForRegion($parsed, $region)) {
            return null;
        }

        return $util->format($parsed, PhoneNumberFormat::E164);
    }

    /**
     * The ISO region a stored E.164 number belongs to, e.g. '+201099988877' => 'EG'.
     *
     * Used to preselect the country when an existing record is opened for edit,
     * so the number is interpreted the same way it was when first saved.
     */
    public static function regionOf(?string $e164): ?string
    {
        if (blank($e164)) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            return $util->getRegionCodeForNumber($util->parse($e164, null));
        } catch (NumberParseException) {
            return null;
        }
    }

    /**
     * The national part of a stored number, without the dial code or trunk
     * prefix — what belongs in the text input beside the country select.
     */
    public static function nationalOf(?string $e164): ?string
    {
        if (blank($e164)) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            return $util->getNationalSignificantNumber($util->parse($e164, null));
        } catch (NumberParseException) {
            return null;
        }
    }

    /**
     * How a stored number should be shown to a human: +20 100 123 4567.
     */
    public static function forDisplay(?string $e164): ?string
    {
        if (blank($e164)) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            // A leading + makes the region unnecessary.
            return $util->format($util->parse($e164, null), PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException) {
            return $e164;
        }
    }
}
