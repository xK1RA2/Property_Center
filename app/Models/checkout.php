<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class checkout extends Model
{
    protected $table = 'checkout';
 protected $fillable = [
  'user_id','property_id','price','card_number','expDate','from','to'
  ,'cvv','address','date','dealer_id'
 ];
 public function Property():BelongsTo{
    return $this->belongsTo(Property::class , 'id');
  }
  public function User():BelongsTo{
    return $this->belongsTo(User::class,'user_id');
  }
}

