<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\MimsmsDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.mimsms.username', 'test_user');
    config()->set('kotha.drivers.mimsms.api_key', 'test_key');
    config()->set('kotha.drivers.mimsms.sender_id', 'test_sender_id');
});

it('can instantiate the mimsms driver', function () {
    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    expect($driver)->toBeInstanceOf(MimsmsDriver::class);
});

it('can set recipient and message for mimsms driver', function () {
    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));

    expect($driver->to('8801700000000'))->toBeInstanceOf(MimsmsDriver::class);
    expect($driver->message('Test message'))->toBeInstanceOf(MimsmsDriver::class);
});

it('sends sms successfully with mimsms driver', function () {
    Http::fake([
        'https://api.mimsms.com/*' => Http::response(['statusCode' => 200, 'responseResult' => 'Success'], 200),
    ]);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();
    expect($response->data)->toEqual(['statusCode' => 200, 'responseResult' => 'Success']);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.mimsms.com') &&
               $request->method() === 'POST' &&
               $request['UserName'] === 'test_user' &&
               $request['ApiKey'] === 'test_key' &&
               $request['SenderName'] === 'test_sender_id' &&
               $request['MobileNumber'] === '8801700000000' &&
               $request['Message'] === 'Test message';
    });
});

it('sends bulk sms successfully with mimsms driver', function () {
    Http::fake([
        'https://api.mimsms.com/*' => Http::response(['statusCode' => 200, 'responseResult' => 'Success'], 200),
    ]);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $response = $driver->to(['8801700000000', '8801800000000'])->message('Bulk test')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request['MobileNumber'] === '8801700000000,8801800000000';
    });
});

it('throws KothaException on mimsms api error', function () {
    Http::fake([
        'https://api.mimsms.com/*' => Http::response(['statusCode' => 401, 'responseResult' => 'Invalid credentials'], 200),
    ]);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $driver->to('8801700000000')->message('Test message')->send();
})->throws(KothaException::class, 'Invalid credentials');

it('throws KothaException if username is missing for mimsms driver', function () {
    config()->set('kotha.drivers.mimsms.username', null);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $driver->to('8801700000000')->message('Test message')->send();
})->throws(KothaException::class, 'Please set username for Mimsms in config/kotha.php.');

it('throws KothaException if api_key is missing for mimsms driver', function () {
    config()->set('kotha.drivers.mimsms.api_key', null);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $driver->to('8801700000000')->message('Test message')->send();
})->throws(KothaException::class, 'Please set api_key for Mimsms in config/kotha.php.');

it('throws KothaException if sender_id is missing for mimsms driver', function () {
    config()->set('kotha.drivers.mimsms.sender_id', null);

    $driver = new MimsmsDriver(config('kotha.drivers.mimsms'));
    $driver->to('8801700000000')->message('Test message')->send();
})->throws(KothaException::class, 'Please set sender_id for Mimsms in config/kotha.php.');
