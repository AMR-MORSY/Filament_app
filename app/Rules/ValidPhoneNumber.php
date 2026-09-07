<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

/**
 * Validates a national phone number against the country the patient chose.
 *
 * A length-and-digits regex cannot do this: "999999" is six digits and looks
 * fine, but is not a real Egyptian number. libphonenumber carries Google's
 * per-country metadata, so the check is specific to the selected region.
 */
class ValidPhoneNumber implements ValidationRule
{
    public function __construct(
        protected ?string $region,
        protected bool $mobileOnly = false,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($this->region)) {
            $fail('Choose the country this number belongs to.');

            return;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse((string) $value, $this->region);
        } catch (NumberParseException) {
            $fail('That does not look like a phone number. Enter it in digits.');

            return;
        }

        if (! $util->isValidNumberForRegion($parsed, $this->region)) {
            $fail('That is not a valid number for the country you selected.');

            return;
        }

        if ($this->mobileOnly && ! in_array(
            $util->getNumberType($parsed),
            [PhoneNumberType::MOBILE, PhoneNumberType::FIXED_LINE_OR_MOBILE],
            true,
        )) {
            $fail('Enter a mobile number, so the clinic can text you about your visit.');
        }
    }
}
