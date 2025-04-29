<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'id',
        'user_id',
        'Property_id',
        'status',
        'price'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id'); 
    }
    public function Property():BelongsTo
    {
        return $this->belongsTo(Property::class,'property_id'); 
    }
    

}
