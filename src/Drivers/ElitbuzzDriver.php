<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

final class ElitbuzzDriver extends AbstractDriver
{
    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::timeout($this->timeout)
            ->retry($this->retry, $this->retryDelay)
            ->asForm()
            ->post($this->config['url'].'/smsapi', [
                'api_key' => $this->config['api_key'],
                'type' => $this->config['type'] ?? 'text',
                'senderid' => $this->config['sender_id'],
                'contacts' => implode(',', $this->recipients),
                'msg' => $this->message,
            ]);

        $body = $response->body();

        if (str_contains(mb_strtolower($body), 'error') || str_contains(mb_strtolower($body), 'fail')) {
            throw new KothaException($body ?: 'ElitBuzz API error');
        }

        return new ResponseData(
            success: true,
            data: ['response' => $body],
        );
    }

    protected function validate(): void
    {
        parent::validate();

        if (empty($this->config['url'])) {
            throw new KothaException('Please set url for ElitBuzz in config/kotha.php.');
        }

        if (empty($this->config['api_key'])) {
            throw new KothaException('Please set api_key for ElitBuzz in config/kotha.php.');
        }

        if (empty($this->config['sender_id'])) {
            throw new KothaException('Please set sender_id for ElitBuzz in config/kotha.php.');
        }
    }
}
