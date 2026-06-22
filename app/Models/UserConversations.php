<?php

namespace App\Models;

use App\Models\UserMessages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConversations extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user_messages(){
        return $this->hasMany(UserMessages::class, 'conversation_id', 'id');
    }
}
