<?php

namespace App\Services;

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
        // $user = UserClient::where('channel', $message->channel)->where('identifier', $message->sender)->first();
        // if(!$user){
        //     $user = UserClient::create([
        //         'channel'=>$message->channel,
        //         'identifier'=>$message->sender,
        //     ]);
        // }
        $user = UserClient::firstOrCreate([
            'channel'=>$message->channel,
            'identifier'=>$message->sender
        ]);
    
        return $user;
    }

    public function handleMessage($message){
        $intent = $this->measure_intent($message->message);

        Log::info('Intent', [
            'intent'=>$intent
        ]);

        $handler = $this->routeToService($intent);

        If(!$handler){
            return [
                'intent' => 'unknown',
                'response' => 'I couldnt understand your request'
            ];
        }

        $handlerClass = app($handler);

        $user = $this->loadUser($message);

        return $handlerClass->handle($message, $user);
    }

    public function routeToService($intent){
        return $this->router->resolve($intent);
    }
}