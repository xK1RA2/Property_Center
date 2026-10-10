<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyType extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = false;
    protected $table = 'property_types';
    protected $fillable = [
        'name',
    ];

public function property():HasMany
{
    return $this->HasMany(Property::class);
}

}
