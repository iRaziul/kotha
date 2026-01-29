<?php

declare(strict_types=1);

use Larament\Kotha\Drivers\EsmsDriver;
use Larament\Kotha\Drivers\LogDriver;
use Larament\Kotha\Drivers\MimsmsDriver;
use Larament\Kotha\KothaManager;

it('can resolve the manager from the container', function () {
    $manager = app(KothaManager::class);

    expect($manager)->toBeInstanceOf(KothaManager::class);
});

it('can create a log driver', function () {
    $manager = app(KothaManager::class);

    $driver = $manager->driver('log');

    expect($driver)->toBeInstanceOf(LogDriver::class);
});

it('can create an esms driver', function () {
    config()->set('kotha.drivers.esms.api_token', 'test_token');
    config()->set('kotha.drivers.esms.sender_id', 'test_sender_id');

    $manager = app(KothaManager::class);

    $driver = $manager->driver('esms');

    expect($driver)->toBeInstanceOf(EsmsDriver::class);
});

it('can create a mimsms driver', function () {
    config()->set('kotha.drivers.mimsms.username', 'test_user');
    config()->set('kotha.drivers.mimsms.api_key', 'test_key');
    config()->set('kotha.drivers.mimsms.sender_id', 'test_sender_id');

    $manager = app(KothaManager::class);

    $driver = $manager->driver('mimsms');

    expect($driver)->toBeInstanceOf(MimsmsDriver::class);
});

it('returns the default driver', function () {
    config()->set('kotha.default', 'log');

    $manager = app(KothaManager::class);

    $driver = $manager->driver();

    expect($driver)->toBeInstanceOf(LogDriver::class);
});
