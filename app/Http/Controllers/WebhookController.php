<?php

namespace App\Http\Controllers;

use App\Services\Adapters\WebAdapter;
use App\Services\OrchestratorService;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Parsedown;

class WebhookController extends Controller
{
    /**
     * 
     * Meta Webhook Verification
     */
    public function verify(Request $request){
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verify_token = env('META_VERIFY_TOKEN');

        if(
            $mode === 'subscribe' &&
            $token === $verify_token
        ){
            Log::info("Webhook verified successfully");

            return response($challenge, 200);
        }

        Log::error("Webhook verification failed");
        return response("Verification failed", 403);
    }

    public function recieve(
        Request $request,
        WebAdapter $adapter,
        ValidationService $validator,
        OrchestratorService $orchestrator
    ){
        try {
            $payload = $request->all();

            Log::info("Incoming Meta Payload: ", $payload);

            if(isset($payload['entry'][0]['changes'][0]["value"]["statuses"])){
                Log::info("Ignoring status event");
                return response()->json(['status'=>'ignored']);
            }

            $validator->validate(
                $payload
            );

            $message = $adapter->normalize(
                $payload
            );

            Log::info("Normalized Message", [
                'channel' => $message->channel,
                'sender' => $message->sender,
                'message' => $message->message
            ]);

            $response = $orchestrator->handleMessage($message);

            Log::info("Orchestrator Message", $response);

            $parser = new Parsedown();
            $parsed_text = $parser->text($response['response']);

            return response()->json([
                'status' => 'success',
                'user_id'=>$message->sender,
                'response' => $parsed_text
            ]);
        } catch (\Throwable $th) {
            Log::error("Webhook error", [
                "message"=>$th->getMessage(),
                "trace"=>$th->getTraceAsString()
            ]);

            return response()->json([
                'status'=>"error",
                'message'=>$th->getMessage()
            ], 500);
        }
    }
}
