<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\SslDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.ssl.api_token', 'test_token');
    config()->set('kotha.drivers.ssl.sender_id', 'test_sender');
});

it('can instantiate the ssl driver', function () {
    $driver = new SslDriver(config('kotha.drivers.ssl'));
    expect($driver)->toBeInstanceOf(SslDriver::class);
});

it('sends sms successfully with ssl driver', function () {
    Http::fake([
        'https://smsplus.sslwireless.com/*' => Http::response(['status' => 'SUCCESS', 'status_message' => 'Sent'], 200),
    ]);

    $driver = new SslDriver(config('kotha.drivers.ssl'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'sslwireless.com') &&
               $request['api_token'] === 'test_token' &&
               $request['sid'] === 'test_sender';
    });
});

it('sends bulk sms with ssl driver', function () {
    Http::fake([
        'https://smsplus.sslwireless.com/*' => Http::response(['status' => 'SUCCESS'], 200),
    ]);

    $driver = new SslDriver(config('kotha.drivers.ssl'));
    $response = $driver->to(['8801700000000', '8801800000000'])->message('Bulk test')->send();

    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'bulk') &&
               $request['msisdn'] === '8801700000000,8801800000000';
    });
});

it('throws exception on ssl api error', function () {
    Http::fake([
        '*' => Http::response(['status' => 'FAILED', 'error' => 'Invalid token'], 200),
    ]);

    $driver = new SslDriver(config('kotha.drivers.ssl'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'Invalid token');

it('throws exception if api_token missing', function () {
    config()->set('kotha.drivers.ssl.api_token', null);

    $driver = new SslDriver(config('kotha.drivers.ssl'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'api_token');

it('throws exception if sender_id missing', function () {
    config()->set('kotha.drivers.ssl.sender_id', null);

    $driver = new SslDriver(config('kotha.drivers.ssl'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'sender_id');
