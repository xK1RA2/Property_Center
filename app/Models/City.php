<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';
        public $timestamps = false;
  



    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function Property(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
