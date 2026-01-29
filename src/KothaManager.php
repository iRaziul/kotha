<?php

declare(strict_types=1);

namespace Larament\Kotha;

use Illuminate\Support\Manager;
use Larament\Kotha\Drivers\AdnsmsDriver;
use Larament\Kotha\Drivers\AlphasmsDriver;
use Larament\Kotha\Drivers\BanglalinkDriver;
use Larament\Kotha\Drivers\BulksmsDriver;
use Larament\Kotha\Drivers\ElitbuzzDriver;
use Larament\Kotha\Drivers\EsmsDriver;
use Larament\Kotha\Drivers\GrameenphoneDriver;
use Larament\Kotha\Drivers\GreenwebDriver;
use Larament\Kotha\Drivers\InfobipDriver;
use Larament\Kotha\Drivers\LogDriver;
use Larament\Kotha\Drivers\MimsmsDriver;
use Larament\Kotha\Drivers\RobiDriver;
use Larament\Kotha\Drivers\SslDriver;

class KothaManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('kotha.default');
    }

    protected function createLogDriver(): LogDriver
    {
        return new LogDriver;
    }

    protected function createEsmsDriver(): EsmsDriver
    {
        return new EsmsDriver($this->config->get('kotha.drivers.esms'));
    }

    protected function createMimsmsDriver(): MimsmsDriver
    {
        return new MimsmsDriver($this->config->get('kotha.drivers.mimsms'));
    }

    protected function createSslDriver(): SslDriver
    {
        return new SslDriver($this->config->get('kotha.drivers.ssl'));
    }

    protected function createGrameenphoneDriver(): GrameenphoneDriver
    {
        return new GrameenphoneDriver($this->config->get('kotha.drivers.grameenphone'));
    }

    protected function createBanglalinkDriver(): BanglalinkDriver
    {
        return new BanglalinkDriver($this->config->get('kotha.drivers.banglalink'));
    }

    protected function createRobiDriver(): RobiDriver
    {
        return new RobiDriver($this->config->get('kotha.drivers.robi'));
    }

    protected function createInfobipDriver(): InfobipDriver
    {
        return new InfobipDriver($this->config->get('kotha.drivers.infobip'));
    }

    protected function createAdnsmsDriver(): AdnsmsDriver
    {
        return new AdnsmsDriver($this->config->get('kotha.drivers.adnsms'));
    }

    protected function createAlphasmsDriver(): AlphasmsDriver
    {
        return new AlphasmsDriver($this->config->get('kotha.drivers.alphasms'));
    }

    protected function createGreenwebDriver(): GreenwebDriver
    {
        return new GreenwebDriver($this->config->get('kotha.drivers.greenweb'));
    }

    protected function createBulksmsDriver(): BulksmsDriver
    {
        return new BulksmsDriver($this->config->get('kotha.drivers.bulksms'));
    }

    protected function createElitbuzzDriver(): ElitbuzzDriver
    {
        return new ElitbuzzDriver($this->config->get('kotha.drivers.elitbuzz'));
    }
}
