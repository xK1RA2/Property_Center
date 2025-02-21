<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPreview extends Model
{
 
      public function Property():HasOne{
        return $this->HasOne(Property::class , 'Property_id');
      }
      public function User():HasMany{
        return $this->HasMany(User::class);
      }
}
