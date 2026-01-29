<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\RobiDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.robi.username', 'test_user');
    config()->set('kotha.drivers.robi.password', 'test_pass');
});

it('can instantiate the robi driver', function () {
    $driver = new RobiDriver(config('kotha.drivers.robi'));
    expect($driver)->toBeInstanceOf(RobiDriver::class);
});

it('sends sms successfully with robi driver', function () {
    Http::fake([
        'https://bmpws.robi.com.bd/*' => Http::response('Success: Message Sent', 200),
    ]);

    $driver = new RobiDriver(config('kotha.drivers.robi'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'robi.com.bd') &&
               $request['username'] === 'test_user';
    });
});

it('throws exception on robi api error', function () {
    Http::fake([
        '*' => Http::response('Error: Authentication failed', 200),
    ]);

    $driver = new RobiDriver(config('kotha.drivers.robi'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class);

it('throws exception if username missing', function () {
    config()->set('kotha.drivers.robi.username', null);

    $driver = new RobiDriver(config('kotha.drivers.robi'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'username');

it('throws exception if password missing', function () {
    config()->set('kotha.drivers.robi.password', null);

    $driver = new RobiDriver(config('kotha.drivers.robi'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'password');
