<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Facades\Kotha;
use Larament\Kotha\KothaManager;

it('can resolve the Kotha facade', function () {
    expect(app(KothaManager::class))->toBeInstanceOf(KothaManager::class);
    expect(Kotha::getFacadeRoot())->toBeInstanceOf(KothaManager::class);
});

it('can send sms using the Kotha facade with log driver', function () {
    config()->set('kotha.default', 'log');

    $response = Kotha::to('8801700000000')->message('Facade Test Message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();
});

it('can send sms using the Kotha facade with esms driver', function () {
    Http::fake([
        'https://login.esms.com.bd/*' => Http::response(['status' => 'success', 'message' => 'SMS Sent'], 200),
    ]);

    config()->set('kotha.default', 'esms');
    config()->set('kotha.drivers.esms.api_token', 'test_token');
    config()->set('kotha.drivers.esms.sender_id', 'test_sender_id');

    $response = Kotha::to('8801700000000')->message('Facade Test Message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'esms.com.bd') &&
               $request->method() === 'POST' &&
               $request->hasHeader('Authorization', 'Bearer test_token') &&
               $request['recipient'] === '8801700000000' &&
               $request['sender_id'] === 'test_sender_id' &&
               $request['message'] === 'Facade Test Message';
    });
});
