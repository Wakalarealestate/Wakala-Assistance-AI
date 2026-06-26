<?php

namespace App\Services;

use App\Handlers\LeadQualificationHandler;
use App\Models\UserClient;
use App\Services\IntelService;
use Illuminate\Support\Facades\Log;

class OrchestratorService{
    protected $intel_service;
    protected $router;

    public function __construct(IntelService $intel_service, RouterService $router){
        $this->intel_service = $intel_service;
        $this->router = $router;
    }

    private function measure_intent($message) :string{
        $final_prompt = config('system_prompts.intent_prompt').$message;

        return $this->intel_service->generateGeminiResponse($final_prompt);
    }

    private function loadUser($message){
        $user = UserClient::where('channel', $message->channel)->where('identifier', $message->sender)->first();
        if(!$user){
            $user = UserClient::create([
                'channel'=>$message->channel,
                'identifier'=>$message->sender,
            ]);
        }

        // Log::info("Loaded User at Orchestator".$user);
    
        return $user;
    }

    public function handleMessage($message){
        $user = $this->loadUser($message);

        Log::info("Probe Orchestrator Message",[
            'channel' => $message->channel,
            'sender' => $message->sender,
            'message' => $message->message
        ]);

        if($user->stage === 'new'){
            return app(LeadQualificationHandler::class)->handle($message);
        }

        $intent = $this->measure_intent($message->message);

        Log::info('Intent', [
            'intent'=>$intent
        ]);

        $handler = $this->routeToService($intent);

        If(!$handler){
            return [
                'intent' => 'unknown',
                'response' => 'I could not understand your request'
            ];
        }

        $handlerClass = app($handler);

        if(isset($user)){
            return $handlerClass->handle($message);
        }
    }

    public function routeToService($intent){
        return $this->router->resolve($intent);
    }
}