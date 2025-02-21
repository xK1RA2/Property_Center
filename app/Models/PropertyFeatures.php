<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;



class PropertyFeatures extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable =[
        'Bedrooms',
        'Bathrooms',
        'Kitchen',
        'Rooms',
        'Area',
        'Heating',
        'Ait_Conditioner',
        'Parking',
      ];
      public function Property():BelongsTo{
        return $this->BelongsTo(Property::class,'Property_id');
      }
}
