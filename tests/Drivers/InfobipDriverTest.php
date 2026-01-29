<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\InfobipDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.infobip.base_url', 'https://api.infobip.com');
    config()->set('kotha.drivers.infobip.username', 'test_user');
    config()->set('kotha.drivers.infobip.password', 'test_pass');
    config()->set('kotha.drivers.infobip.sender_id', 'test_sender');
});

it('can instantiate the infobip driver', function () {
    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    expect($driver)->toBeInstanceOf(InfobipDriver::class);
});

it('sends sms successfully with infobip driver', function () {
    Http::fake([
        'https://api.infobip.com/*' => Http::response([
            'messages' => [
                ['status' => ['groupName' => 'PENDING', 'description' => 'Message sent']],
            ],
        ], 200),
    ]);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'infobip.com') &&
               $request->hasHeader('Authorization');
    });
});

it('sends bulk sms with infobip driver', function () {
    Http::fake([
        '*' => Http::response([
            'messages' => [
                ['status' => ['groupName' => 'PENDING']],
            ],
        ], 200),
    ]);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $response = $driver->to(['8801700000000', '8801800000000'])->message('Bulk test')->send();

    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        $body = $request->data();

        return count($body['messages'][0]['destinations']) === 2;
    });
});

it('throws exception on infobip api error', function () {
    Http::fake([
        '*' => Http::response([
            'messages' => [
                ['status' => ['groupName' => 'REJECTED', 'description' => 'Invalid sender']],
            ],
        ], 200),
    ]);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'Invalid sender');

it('throws exception if base_url missing', function () {
    config()->set('kotha.drivers.infobip.base_url', null);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'base_url');

it('throws exception if username missing', function () {
    config()->set('kotha.drivers.infobip.username', null);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'username');

it('throws exception if password missing', function () {
    config()->set('kotha.drivers.infobip.password', null);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'password');

it('throws exception if sender_id missing', function () {
    config()->set('kotha.drivers.infobip.sender_id', null);

    $driver = new InfobipDriver(config('kotha.drivers.infobip'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'sender_id');
