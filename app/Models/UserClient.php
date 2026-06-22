<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClient extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function conversation(){
        return $this->hasOne(UserConversations::class, 'user_id', 'id');
    }
}
