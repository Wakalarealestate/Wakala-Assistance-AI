<?php

namespace App\Handlers;

use App\Models\Message;
use App\Models\UserClient;

interface IntentHandler{
    public function handle(Message $message, UserClient $user);
}