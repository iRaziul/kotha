<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class AdnsmsDriver extends AbstractDriver
{
    private string $baseUrl = 'https://portal.adnsms.com/api/v1/secure';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay, throw: false)
            ->acceptJson()
            ->asForm()
            ->post('/send-sms', [
                'api_key' => $this->config['api_key'],
                'api_secret' => $this->config['api_secret'],
                'request_type' => $this->config['request_type'] ?? 'SINGLE_SMS',
                'message_type' => $this->config['message_type'] ?? 'TEXT',
                'senderid' => $this->config['sender_id'] ?? null,
                'mobile' => implode(',', $this->recipients),
                'message_body' => $this->message,
            ])
            ->json();

        if (($response['api_response_code'] ?? 0) !== 200) {
            throw new KothaException($response['api_response_message'] ?? 'ADN SMS API error');
        }

        return new ResponseData(
            success: true,
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['api_key'])) {
            throw new KothaException('Please set api_key for ADN in config/kotha.php.');
        }

        if (empty($this->config['api_secret'])) {
            throw new KothaException('Please set api_secret for ADN in config/kotha.php.');
        }
    }
}
