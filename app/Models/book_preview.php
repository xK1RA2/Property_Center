<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class book_preview extends Model
{
    protected $table = 'book_preview';
 
      public function Property():HasOne{
        return $this->HasOne(Property::class , 'Property_id');
      }
      public function User():HasMany{
        return $this->HasMany(User::class);
      }
}
