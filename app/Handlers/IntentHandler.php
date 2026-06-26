<?php

namespace App\Handlers;

use App\Models\Message;

interface IntentHandler{
    public function handle(Message $message);
}