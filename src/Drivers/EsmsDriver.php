<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class EsmsDriver extends AbstractDriver
{
    private string $baseUrl = 'https://login.esms.com.bd/api/v3';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->withToken($this->config['api_token'])
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->acceptJson()
            ->post('/sms/send', [
                'recipient' => implode(',', $this->recipients),
                'sender_id' => $this->config['sender_id'],
                'type' => 'plain',
                'message' => $this->message,
            ])
            ->json();

        if ($response['status'] === 'error') {
            throw new KothaException($response['message']);
        }

        return new ResponseData(
            success: $response['status'] === 'success',
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (! $this->config['sender_id']) {
            throw new KothaException('Please set sender_id for ESMS in config/kotha.php.');
        }

        if (! $this->config['api_token']) {
            throw new KothaException('Please set api_token for ESMS in config/kotha.php.');
        }
    }
}
