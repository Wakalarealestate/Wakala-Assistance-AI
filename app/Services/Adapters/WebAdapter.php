<?php

namespace App\Services\Adapters;

use App\Models\Message;

class WebAdapter extends Adapter{
    public function normalize(
        array $payload
    ){
        return new Message(
            channel: 'web',
            sender: !empty($payload['sender_id']) ? $payload['sender_id'] : $this->createSender(),
            message: $payload['message']
        );
    }
}