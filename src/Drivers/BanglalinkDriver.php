<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class BanglalinkDriver extends AbstractDriver
{
    private string $baseUrl = 'https://vas.banglalink.net/sendSMS';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->asForm()
            ->post('/sendSMS', [
                'userID' => $this->config['user_id'],
                'passwd' => $this->config['password'],
                'sender' => $this->config['sender_id'],
                'msisdn' => implode(',', $this->recipients),
                'message' => $this->message,
            ]);

        $body = $response->body();

        // Banglalink returns XML/text response
        if (str_contains(mb_strtolower($body), 'error') || str_contains(mb_strtolower($body), 'fail')) {
            throw new KothaException($body ?: 'Banglalink API error');
        }

        return new ResponseData(
            success: true,
            data: ['response' => $body],
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['user_id'])) {
            throw new KothaException('Please set user_id for Banglalink in config/kotha.php.');
        }

        if (empty($this->config['password'])) {
            throw new KothaException('Please set password for Banglalink in config/kotha.php.');
        }

        if (empty($this->config['sender_id'])) {
            throw new KothaException('Please set sender_id for Banglalink in config/kotha.php.');
        }
    }
}
