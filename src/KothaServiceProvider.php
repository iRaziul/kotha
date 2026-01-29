<?php

declare(strict_types=1);

namespace Larament\Kotha;

use Illuminate\Contracts\Container\Container;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Larament\Kotha\Commands\InstallKothaCommand;
use Larament\Kotha\Notifications\KothaChannel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class KothaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('kotha')
            ->hasConfigFile()
            ->hasCommand(InstallKothaCommand::class);

        $this->app->singleton(
            KothaManager::class,
            fn (Container $container) => new KothaManager($container)
        );

        Notification::resolved(function (ChannelManager $channel): void {
            $channel->extend('kotha', fn ($app) => $app->make(KothaChannel::class));
        });
    }
}
