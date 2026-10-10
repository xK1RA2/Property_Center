<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Booking extends Model
{
    protected $table = 'bookings';
 protected $fillable = [
  'user_id','property_id','date','status','description','dealer_id'
 ];
      public function Property():BelongsTo{
        return $this->belongsTo(Property::class, 'property_id');
      }
      public function propertyRef():BelongsTo{
        return $this->belongsTo(Property::class, 'property_id');
      }
      public function User():BelongsTo{
        return $this->belongsTo(User::class,'user_id');
      }
}
