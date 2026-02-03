<?php

declare(strict_types=1);

namespace Larament\Barta\Helpers;

use Illuminate\Support\Str;
use Larament\Barta\Exceptions\BartaException;

final class Util
{
    /**
     * Standardizes BD phone numbers to 8801XXXXXXXXX format.
     *
     * @throws BartaException
     */
    public static function formatPhoneNumber(string $number): string
    {
        $phone = Str::of($number)
            ->replaceMatches('/\D/', '')
            ->ltrim('88')
            ->ltrim('0')
            ->prepend('880')
            ->toString();

        if (! preg_match('/^8801[3-9][0-9]{8}$/', $phone)) {
            throw BartaException::invalidNumber($number);
        }

        return $phone;
    }
}
