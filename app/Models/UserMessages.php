<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessages extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function conversation(){
        return $this->belongsTo(UserConversations::class, 'conversation_id', 'id');
    }
}
