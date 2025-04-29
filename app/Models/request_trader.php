<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class request_trader extends Model
{
   
    protected $fillable = [
        'user_id'
    ];
    protected $table = 'request_trader';
    public function user():HasOne
    {
        return $this->HasOne(User::class,'id'); 
    }
}
