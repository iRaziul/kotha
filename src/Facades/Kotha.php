<?php

declare(strict_types=1);

namespace Larament\Kotha\Facades;

use Illuminate\Support\Facades\Facade;
use Larament\Kotha\KothaManager;

/**
 * @method static self to(string $number)
 * @method static self message(string $message)
 * @method static \Larament\Kotha\Data\ResponseData send()
 * @method static self driver(?string $driver)
 *
 * @see KothaManager
 */
class Kotha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return KothaManager::class;
    }
}
