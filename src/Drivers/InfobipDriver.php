<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class InfobipDriver extends AbstractDriver
{
    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->config['base_url'])
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->withBasicAuth($this->config['username'], $this->config['password'])
            ->acceptJson()
            ->asJson()
            ->post('/sms/2/text/advanced', [
                'messages' => [
                    [
                        'from' => $this->config['sender_id'],
                        'destinations' => array_map(
                            fn (string $number) => ['to' => $number],
                            $this->recipients
                        ),
                        'text' => $this->message,
                    ],
                ],
            ])
            ->json();

        $status = $response['messages'][0]['status']['groupName'] ?? null;

        if ($status === 'REJECTED' || isset($response['requestError'])) {
            $error = $response['requestError']['serviceException']['text']
                ?? $response['messages'][0]['status']['description']
                ?? 'Infobip API error';
            throw new KothaException($error);
        }

        return new ResponseData(
            success: in_array($status, ['PENDING', 'SENT', 'DELIVERED']),
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['base_url'])) {
            throw new KothaException('Please set base_url for Infobip in config/kotha.php.');
        }

        if (empty($this->config['username'])) {
            throw new KothaException('Please set username for Infobip in config/kotha.php.');
        }

        if (empty($this->config['password'])) {
            throw new KothaException('Please set password for Infobip in config/kotha.php.');
        }

        if (empty($this->config['sender_id'])) {
            throw new KothaException('Please set sender_id for Infobip in config/kotha.php.');
        }
    }
}
