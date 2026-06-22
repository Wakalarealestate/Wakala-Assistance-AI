<?php

namespace App\Handlers;

use App\Handlers\IntentHandler;
use App\Services\ConversationService;
use App\Services\IntelService;

class CustomerInquiryHandler implements IntentHandler{
    protected $context_builder;
    protected $intel_service;
    protected $conversation_service;

    public function __construct(){
        $this->intel_service = new IntelService;
        $this->conversation_service = new ConversationService;
    }

    public function handle($message, $user){

        $final_prompt = json_encode(config('system_info.company_info')).config('system_info.system_identity_prompt').config('system_prompts.customer_inquiry').$message->message.$this->conversation_service->extractPastConversation($message, $user);

        $this->conversation_service->saveConversation($user, $message, 'client');

        $response = $this->intel_service->generateGeminiResponse($final_prompt);

        $this->conversation_service->saveConversation($user, $response, 'assistant');

        return [
            'intent' => 'customer_inquiry',
            'response' => $response
        ];
    }
}