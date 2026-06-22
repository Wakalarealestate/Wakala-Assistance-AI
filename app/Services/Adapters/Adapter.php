<?php

namespace App\Services\Adapters;

use Illuminate\Support\Str;

abstract class Adapter{
    public function createSender(){
        return Str::uuid()->toString();
    }
}