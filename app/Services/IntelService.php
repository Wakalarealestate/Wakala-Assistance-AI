<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class IntelService{
    protected $api_key;
    protected $api_url;
    public $http_client;

    public function __construct(){
        $this->api_key = env('GEMINI_API_KEY');
        $this->api_url = env('GEMINI_API_URL').$this->api_key;
        $this->http_client = new Client();
    }    

    public function generateGeminiResponse($prompt){
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 400,
            ]
        ];

        try {
            $response = $this->http_client->post($this->api_url, [
                'body' => json_encode($data),
                'headers' => [
                    'Content-Type'=>'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if(isset($result['candidates']) && is_array($result['candidates']) && !empty($result['candidates'])){
                foreach($result['candidates'] as $candidate){
                    if(isset($candidate['content']['parts']) && is_array($candidate['content']['parts']) && !empty($candidate['content']['parts'])){
                        foreach($candidate['content']['parts'] as $part){
                            if(isset($part['text'])){
                                return $part['text'];
                            }
                        }
                    }
                }
            }
            return 'No response text found in response';
        } catch (\Throwable $th) {
            throw new Exception("Error communicating with Gemini API: ".$th->getMessage());
        }
    }
}