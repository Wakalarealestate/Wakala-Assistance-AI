<?php

namespace App\Handlers;

use App\Handlers\IntentHandler;
use App\Models\UserClient;
use App\Services\ConversationService;
use App\Services\IntelService;
use Illuminate\Support\Facades\Log;

class CustomerInquiryHandler implements IntentHandler{
    protected $context_builder;
    protected $intel_service;
    protected $conversation_service;

    public function __construct(){
        $this->intel_service = new IntelService;
        $this->conversation_service = new ConversationService;
    }

    public function handle($message){

        $user = $this->loadUser($message);

        $history = $this->conversation_service
                    ->extractPastConversation($message, $user)
                    ?->toJson() ?? '';

        $final_prompt = json_encode(config('system_info.company_info')).config('system_info.system_identity_prompt').config('system_prompts.customer_inquiry')."\n Conversation History \n".$history."\n Current History \n".$message->message;
        
        // Log::info("Loaded User at Handler".$user);
        // Log::info("Conversation History".$this->conversation_service->extractPastConversation($message, $user));


        $this->conversation_service->saveConversation($user, $message, 'client');

        $response = $this->intel_service->generateGeminiResponse($final_prompt);

        $this->conversation_service->saveConversation($user, $response, 'assistant');

        return [
            'intent' => 'customer_inquiry',
            'response' => $response
        ];
    }

    private function loadUser($message){
        $user = UserClient::where('identifier', $message->sender)->first();

        return $user;
    }
}