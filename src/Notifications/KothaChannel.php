<?php

declare(strict_types=1);

namespace Larament\Kotha\Notifications;

use Illuminate\Notifications\Notification;
use Larament\Kotha\Facades\Kotha;

class KothaChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $to = $notifiable->routeNotificationFor('kotha', $notification)) {
            return;
        }

        /** @phpstan-ignore method.notFound */
        $message = $notification->toKotha($notifiable);

        $driver = method_exists($notification, 'kothaDriver')
            ? $notification->kothaDriver()
            : null;

        Kotha::driver($driver)->to($to)->message($message->content)->send();
    }
}
