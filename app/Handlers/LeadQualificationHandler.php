<?php

namespace App\Handlers;

use App\Handlers\IntentHandler;
use App\Models\UserClient;
use App\Services\ConversationService;
use App\Services\IntelService;
use Illuminate\Support\Facades\Log;


class LeadQualificationHandler implements IntentHandler{
    public $intel_service;
    public $conversation_service;

    public function __construct(){
        $this->intel_service = new IntelService;
        $this->conversation_service = new ConversationService;
    }

    public function handle($message){

        $user = $this->loadUser($message);

        $history = $this->conversation_service
                    ->extractPastConversation($message, $user)
                    ?->toJson() ?? '';

        $final_prompt = json_encode(config('system_info.company_info')).config('system_info.system_identity_prompt').config('system_prompts.lead_qualification_prompt')."\n Conversation History \n".$history."\n Current History \n".$message->message;

        Log::info("Loaded User at Handler".$user);
        Log::info("Conversation History".$this->conversation_service->extractPastConversation($message, $user));
        
        
        $response = json_decode($this->intel_service->generateGeminiResponse($final_prompt), true);

        Log::info("Lead Qualification Response: ",[
            'response'=>$response
        ]);

        $this->updateUser($response['lead_profile'], $user);
        
        $this->conversation_service->saveConversation($user, $message, 'client');
        $this->conversation_service->saveConversation($user, $response['assistant_response'], 'assistant');
        return [
            'intent' => 'customer_inquiry',
            'response' => $response['assistant_response']
        ];
    }

    private function loadUser($message){
        $user = UserClient::where('identifier', $message->sender)->first();

        return $user;
    }

    private function updateUser($lead_profile, $user){
        foreach($lead_profile as $key => $entry){
            $user->update([$key=>$entry]);
        }

        if(isset($user->first_name) && isset($user->last_name) && isset($user->contact_sources)){
            $user->update(['stage'=>'identified']);
        }
    }
}