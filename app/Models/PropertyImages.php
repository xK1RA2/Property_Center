<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Support\Facades\Storage;

class PropertyImages extends Model
{
    use HasFactory , SoftDeletes;
    public $timestamps = false;
    protected $table='Property_images';
    protected $fillable =[
        'image_path',
        'position'
    ];

    public function Property():BelongsTo {
        return $this->BelongsTo(Property::class,'property_id');
    }
  
    
    public function getUrl()
    {
        if(str_starts_with($this->image_path,'http'))
        {
            return $this->image_path; 
        }
        return Storage::url($this->image_path);
    }
}
