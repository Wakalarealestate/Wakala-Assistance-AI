<?php

namespace App\Services\Adapters;

use App\Models\Message;

class WebAdapter extends Adapter{
    public function normalize(
        array $payload
    ){
        return new Message(
            channel: 'web',
            sender: $payload['sender'] ?? $this->createSender(),
            message: $payload['message']
        );
    }
}