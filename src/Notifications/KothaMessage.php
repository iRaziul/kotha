<?php

declare(strict_types=1);

namespace Larament\Kotha\Notifications;

final readonly class KothaMessage
{
    /**
     * Create a new message instance.
     */
    public function __construct(public string $content = '') {}
}
