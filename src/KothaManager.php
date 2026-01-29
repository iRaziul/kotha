<?php

declare(strict_types=1);

namespace Larament\Kotha;

use Illuminate\Support\Manager;
use Larament\Kotha\Drivers\EsmsDriver;
use Larament\Kotha\Drivers\LogDriver;
use Larament\Kotha\Drivers\MimsmsDriver;

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
}
