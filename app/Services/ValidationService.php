<?php

namespace App\Services;

use Exception;

class ValidationService{
    public function validate($message){

        if($message['message'] === null){
            throw new Exception("Message missing");
        }
        
        if(empty($message['message'])){
            throw new Exception("Message cannot be empty");
        }

        if(strlen($message['message']) > 1000){
            throw new Exception("Message too long");
        }

        return true;
    }
}