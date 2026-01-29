<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class MimsmsDriver extends AbstractDriver
{
    private string $baseUrl = 'https://api.mimsms.com/api/SmsSending';

    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->asJson()
            ->post('/Send', [
                'UserName' => $this->config['username'],
                'ApiKey' => $this->config['api_key'],
                'SenderName' => $this->config['sender_id'],
                'TransactionType' => 'T',
                'CampaignId' => 'null',
                'MobileNumber' => implode(',', $this->recipients),
                'Message' => $this->message,
            ])
            ->json();

        if ((int) $response['statusCode'] !== 200) {
            throw new KothaException($response['responseResult']);
        }

        return new ResponseData(
            success: true,
            data: $response,
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (! $this->config['username']) {
            throw new KothaException('Please set username for Mimsms in config/kotha.php.');
        }

        if (! $this->config['api_key']) {
            throw new KothaException('Please set api_key for Mimsms in config/kotha.php.');
        }

        if (! $this->config['sender_id']) {
            throw new KothaException('Please set sender_id for Mimsms in config/kotha.php.');
        }
    }
}
