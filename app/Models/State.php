<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['name'];
    public function Property(): HasMany
    {
        return $this->hasMany(Property::class);
    }
   
    
    public function cities()
    {
        return $this->hasMany(City::class );
    }
    
}
