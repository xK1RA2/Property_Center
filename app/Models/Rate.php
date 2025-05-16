<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'Property_id', 'Rate', 'created_at', 'updated_at'
    ];
    protected $table = 'Rate';
    //Relation With Property
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id'); 
    }
    public function Property():belongsTo
    {
         return $this->belongsTo(Property::class,'user_id','property_id');
    }
   
}