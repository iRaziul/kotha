<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\BanglalinkDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.banglalink.user_id', 'test_user');
    config()->set('kotha.drivers.banglalink.password', 'test_pass');
    config()->set('kotha.drivers.banglalink.sender_id', 'test_sender');
});

it('can instantiate the banglalink driver', function () {
    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    expect($driver)->toBeInstanceOf(BanglalinkDriver::class);
});

it('sends sms successfully with banglalink driver', function () {
    Http::fake([
        'https://vas.banglalink.net/*' => Http::response('OK: Message Sent', 200),
    ]);

    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'banglalink.net') &&
               $request['userID'] === 'test_user' &&
               $request['sender'] === 'test_sender';
    });
});

it('throws exception on banglalink api error', function () {
    Http::fake([
        '*' => Http::response('Error: Invalid credentials', 200),
    ]);

    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class);

it('throws exception if user_id missing', function () {
    config()->set('kotha.drivers.banglalink.user_id', null);

    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'user_id');

it('throws exception if password missing', function () {
    config()->set('kotha.drivers.banglalink.password', null);

    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'password');

it('throws exception if sender_id missing', function () {
    config()->set('kotha.drivers.banglalink.sender_id', null);

    $driver = new BanglalinkDriver(config('kotha.drivers.banglalink'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'sender_id');
