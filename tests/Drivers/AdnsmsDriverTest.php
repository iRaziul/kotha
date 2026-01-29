<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Drivers\AdnsmsDriver;
use Larament\Kotha\Exceptions\KothaException;

beforeEach(function () {
    config()->set('kotha.drivers.adnsms.api_key', 'test_key');
    config()->set('kotha.drivers.adnsms.api_secret', 'test_secret');
});

it('can instantiate the adn driver', function () {
    $driver = new AdnsmsDriver(config('kotha.drivers.adnsms'));
    expect($driver)->toBeInstanceOf(AdnsmsDriver::class);
});

it('sends sms successfully with adn driver', function () {
    Http::fake([
        'https://portal.adnsms.com/*' => Http::response(['api_response_code' => 200, 'api_response_message' => 'Success'], 200),
    ]);

    $driver = new AdnsmsDriver(config('kotha.drivers.adnsms'));
    $response = $driver->to('8801700000000')->message('Test message')->send();

    expect($response)->toBeInstanceOf(ResponseData::class);
    expect($response->success)->toBeTrue();
});

it('throws exception on adn api error', function () {
    Http::fake([
        '*' => Http::response(['api_response_code' => 401, 'api_response_message' => 'Invalid API Key'], 200),
    ]);

    $driver = new AdnsmsDriver(config('kotha.drivers.adnsms'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'Invalid API Key');

it('throws exception if api_key missing', function () {
    config()->set('kotha.drivers.adnsms.api_key', null);

    $driver = new AdnsmsDriver(config('kotha.drivers.adnsms'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'api_key');

it('throws exception if api_secret missing', function () {
    config()->set('kotha.drivers.adnsms.api_secret', null);

    $driver = new AdnsmsDriver(config('kotha.drivers.adnsms'));
    $driver->to('8801700000000')->message('Test')->send();
})->throws(KothaException::class, 'api_secret');
