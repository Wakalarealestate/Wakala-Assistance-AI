<?php

namespace App\Handlers;

use App\ContextBuilders\ProjectContextBuilder;
use App\Handlers\IntentHandler;
use App\Services\ConversationService;
use App\Services\IntelService;

class ProjectInquiryHandler implements IntentHandler{
    protected $context_builder;
    protected $intel_service;
    protected $conversation_service;

    public function __construct(){
        $this->context_builder = new ProjectContextBuilder;
        $this->intel_service = new IntelService;
        $this->conversation_service = new ConversationService;
    }

    public function handle($message, $user){
        $preload_context = $this->context_builder->constructContext();

        $final_prompt = config('system_info.system_identity_prompt').config('system_prompts.handler_prompt').$preload_context.$message->message.$this->conversation_service->extractPastConversation($message, $user);

        $this->conversation_service->saveConversation($user, $message, 'client');

        $response = $this->intel_service->generateGeminiResponse($final_prompt);

        $this->conversation_service->saveConversation($user, $response, 'assistant');

        return [
            'intent' => 'project_inquiry',
            'response' => $response
        ];
    }
}