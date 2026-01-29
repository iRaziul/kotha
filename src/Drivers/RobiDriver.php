<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class RobiDriver extends AbstractDriver
{
    private string $baseUrl = 'https://bmpws.robi.com.bd/ApacheGearWS';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->asForm()
            ->post('/SendTextMessage', [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'To' => implode(',', $this->recipients),
                'Message' => $this->message,
            ]);

        $body = $response->body();

        if (str_contains(mb_strtolower($body), 'error') || str_contains(mb_strtolower($body), 'fail')) {
            throw new KothaException($body ?: 'Robi API error');
        }

        return new ResponseData(
            success: true,
            data: ['response' => $body],
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['username'])) {
            throw new KothaException('Please set username for Robi in config/kotha.php.');
        }

        if (empty($this->config['password'])) {
            throw new KothaException('Please set password for Robi in config/kotha.php.');
        }
    }
}
