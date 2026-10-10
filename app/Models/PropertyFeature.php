<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;



class PropertyFeature extends Model
{
    use HasFactory , SoftDeletes;

    // The table has no "id" column: Property_id is the primary key.
    protected $primaryKey = 'Property_id';
    public $incrementing = false;
    protected $fillable =[
        'Bedrooms',
        'Bathrooms',
        'Kitchen',
        'Rooms',
        'Area',
        'Heating',
        'Air_Conditioner',
        'Parking',
      ];
      public function Property():BelongsTo{
        return $this->BelongsTo(Property::class,'Property_id');
      }
}
