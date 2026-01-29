<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class SslDriver extends AbstractDriver
{
    private string $baseUrl = 'https://smsplus.sslwireless.com/api/v3';

    public function send(): ResponseData
    {
        $this->validate();

        $endpoint = count($this->recipients) > 1 ? '/send-sms/bulk' : '/send-sms';

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->acceptJson()
            ->asJson()
            ->post($endpoint, [
                'api_token' => $this->config['api_token'],
                'sid' => $this->config['sender_id'],
                'msisdn' => implode(',', $this->recipients),
                'sms' => $this->message,
                'csms_id' => $this->config['csms_id'] ?? uniqid('kotha_'),
            ])
            ->json();

        if (($response['status'] ?? '') === 'FAILED' || isset($response['error'])) {
            throw new KothaException($response['error'] ?? $response['status_message'] ?? 'SSL Wireless API error');
        }

        return new ResponseData(
            success: ($response['status'] ?? '') === 'SUCCESS',
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['api_token'])) {
            throw new KothaException('Please set api_token for SSL Wireless in config/kotha.php.');
        }

        if (empty($this->config['sender_id'])) {
            throw new KothaException('Please set sender_id for SSL Wireless in config/kotha.php.');
        }
    }
}
