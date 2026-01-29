<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class GreenwebDriver extends AbstractDriver
{
    private string $baseUrl = 'https://api.greenweb.com.bd';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->acceptJson()
            ->get('/api.php', [
                'json' => '',
                'token' => $this->config['token'],
                'to' => implode(',', $this->recipients),
                'message' => $this->message,
            ])
            ->json();

        if (isset($response['error']) || (isset($response[0]) && str_contains($response[0], 'Error'))) {
            throw new KothaException($response['error'] ?? $response[0] ?? 'GreenWeb API error');
        }

        return new ResponseData(
            success: true,
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['token'])) {
            throw new KothaException('Please set token for GreenWeb in config/kotha.php.');
        }
    }
}
