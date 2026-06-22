<?php

namespace App\Services;

use App\Models\Message;
use App\Models\UserClient;
use App\Models\UserConversations;
use App\Models\UserMessages;
use Illuminate\Support\Facades\Log;

class ConversationService{
    public function extractPastConversation(Message $message, UserClient $user){
        $messages = null;
        if(!$message){
            Log::info("Past conversations unable to load. Missing Message Model. Skipping...");
            return $messages;
        }

        //Load User Conversation (using sliding window for Token minimization)
        $conversation = UserConversations::where('user_id', $user->id)
                                          ->where('status', 'active')
                                          ->orderBy('created_at', 'desc')
                                          ->first();

        if(isset($conversation)){
            $messages = $conversation->user_messages()->latest()->take(10)->get(['identity', 'message'])->reverse();
            return $messages;
        }

        $new_conversation = UserConversations::firstOrCreate(
            [
            'user_id'=>$user->id,
            'status'=>'active'
            ],[
            'title'=>$message->message,
            'channel'=>$message->channel,
        ]);

        return $messages;
    }

    public function saveConversation(UserClient $user, $message, $role){
        $conversation = UserConversations::where('user_id', $user->id)
                                    ->where('status', 'active')
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        
        if(!($conversation)){
            $conversation = UserConversations::create([
                'user_id'=>$user->id,
                'title'=>$message->message,
                'channel'=>$message->channel,
            ]);
        }

        UserMessages::create([
            'conversation_id' => $conversation->id,
            'identity' => $role,
            'message' => $message->message ?? $message,
        ]);
    }
}