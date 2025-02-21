<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // Add this import
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PropertyType extends Model // Change to PascalCase
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected  $table = 'Property_type';
    protected $fillable = [
        'name',
        'type',
        
    ];

  
public function property()
{
    return $this->belongsTo(Property::class);
}

}
