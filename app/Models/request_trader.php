<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class request_trader extends Model
{
   
    protected $fillable = [
        'user_id'
    ];
    protected $table = 'request_trader';
    public function user():BelongsTo
    {
        return $this->BelongsTo(User::class,'user_id'); 
    }
    public function Property():BelongsTo
    {
        return $this->BelongsTo(Property::class); 
    }
}
