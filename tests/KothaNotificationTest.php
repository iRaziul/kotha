<?php

declare(strict_types=1);

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Larament\Kotha\Notifications\KothaChannel;
use Larament\Kotha\Notifications\KothaMessage;

class TestNotifiable
{
    use Notifiable;

    public string $phone = '8801712345678';

    public function routeNotificationForKotha(): string
    {
        return $this->phone;
    }
}

class TestNotification extends Notification
{
    public function toKotha(): KothaMessage
    {
        return new KothaMessage('This is a test notification message.');
    }
}

class TestNotificationWithCustomDriver extends Notification
{
    public function toKotha(): KothaMessage
    {
        return new KothaMessage('Custom driver notification.');
    }

    public function kothaDriver(): string
    {
        return 'log';
    }
}

it('can send a notification via Kotha channel', function () {
    Http::fake([
        'https://login.esms.com.bd/*' => Http::response(['status' => 'success', 'message' => 'SMS Sent'], 200),
    ]);

    config()->set('kotha.default', 'esms');
    config()->set('kotha.drivers.esms.api_token', 'test_token');
    config()->set('kotha.drivers.esms.sender_id', 'test_sender_id');

    $notifiable = new TestNotifiable;
    $notification = new TestNotification;

    (new KothaChannel)->send($notifiable, $notification);

    Http::assertSent(function ($request) use ($notifiable) {
        return str_contains($request->url(), 'esms.com.bd') &&
               $request->method() === 'POST' &&
               $request->hasHeader('Authorization', 'Bearer test_token') &&
               $request['recipient'] === $notifiable->phone &&
               $request['sender_id'] === 'test_sender_id' &&
               $request['message'] === 'This is a test notification message.';
    });
});

it('does not send notification if route is empty', function () {
    Http::fake();

    config()->set('kotha.default', 'esms');
    config()->set('kotha.drivers.esms.api_token', 'test_token');
    config()->set('kotha.drivers.esms.sender_id', 'test_sender_id');

    $notifiable = new class
    {
        use Notifiable;

        public function routeNotificationForKotha(): ?string
        {
            return null;
        }
    };

    $notification = new TestNotification;

    (new KothaChannel)->send($notifiable, $notification);

    Http::assertNothingSent();
});

it('can send notification with custom driver specified via kothaDriver method', function () {
    config()->set('kotha.default', 'esms');

    $notifiable = new TestNotifiable;
    $notification = new TestNotificationWithCustomDriver;

    // Should use log driver instead of esms because kothaDriver() returns 'log'
    (new KothaChannel)->send($notifiable, $notification);

    // No HTTP request should be made since log driver is used
    Http::assertNothingSent();
});

it('can send notification through Laravel notification system', function () {
    Http::fake([
        'https://login.esms.com.bd/*' => Http::response(['status' => 'success', 'message' => 'SMS Sent'], 200),
    ]);

    config()->set('kotha.default', 'esms');
    config()->set('kotha.drivers.esms.api_token', 'test_token');
    config()->set('kotha.drivers.esms.sender_id', 'test_sender_id');

    $notifiable = new TestNotifiable;

    // Create a notification that uses the 'kotha' channel via Laravel's system
    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return ['kotha'];
        }

        public function toKotha($notifiable): KothaMessage
        {
            return new KothaMessage('Laravel notification system test');
        }
    };

    $notifiable->notify($notification);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'esms.com.bd') &&
               $request['message'] === 'Laravel notification system test';
    });
});
