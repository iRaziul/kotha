<?php

declare(strict_types=1);

use Larament\Barta\Exceptions\BartaException;
use Larament\Barta\Helpers\Util;

it('formats valid bangladeshi numbers with 88 prefix', function (string $input, string $expected) {
    expect(Util::formatPhoneNumber($input))->toBe($expected);
})->with([
    '01700000000' => ['01700000000', '8801700000000'],
    '8801700000000' => ['8801700000000', '8801700000000'],
    '+8801700000000' => ['+8801700000000', '8801700000000'],
    '  01700000000  ' => ['  01700000000  ', '8801700000000'], // With spaces
    '017-0000-0000' => ['017-0000-0000', '8801700000000'], // With hyphens
]);

it('throws exception for invalid bangladeshi numbers', function (string $input) {
    Util::formatPhoneNumber($input);
})->throws(BartaException::class)->with([
    'too short' => ['12345'],
    'invalid prefix' => ['01200000000'],
    'too long' => ['017000000001234'],
]);

it('throws exception for invalid number format', function () {
    Util::formatPhoneNumber('12345');
})->throws(BartaException::class);
