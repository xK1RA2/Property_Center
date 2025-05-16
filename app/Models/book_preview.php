<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class book_preview extends Model
{
    protected $table = 'book_preview';
 protected $fillable = [
  'user_id','property_id','date','status','description','dealer_id'
 ];
      public function Property():HasOne{
        return $this->HasOne(Property::class , 'id');
      }
      public function User():BelongsTo{
        return $this->belongsTo(User::class,'user_id');
      }
}
