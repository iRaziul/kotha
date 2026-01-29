<?php

declare(strict_types=1);

namespace Larament\Kotha\Drivers;

use Illuminate\Support\Facades\Log;
use Larament\Kotha\Data\ResponseData;

class LogDriver extends AbstractDriver
{
    public function send(): ResponseData
    {
        $this->validate();

        Log::info('[KOTHA] Message sent', [
            'recipients' => $this->recipients,
            'message' => $this->message,
        ]);

        return new ResponseData(
            success: true,
            data: [
                'message' => 'Message sent successfully',
            ],
        );
    }
}
