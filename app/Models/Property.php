<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PropertyFeatures;
use App\Models\PropertyImage;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\Storage;
use Carbon\Carbon;


class Property extends Model
{
    use HasFactory , SoftDeletes;
    protected $table ='properties';
          protected $fillable =[
            'Dealer_id',
            'PurchaseType',
            'Property_type_id',
            'city_id',
            'year',
            'price',
            'address',
            'description',
            'status',
          ];
          //Relation With Property_Type
       
public function propertyType():BelongsTo 
{
    return $this->belongsTo(PropertyType::class,'property_type_id');
}

          //Relation With Book_Preview
          public function Book_Preview():HasMany{
            return $this->HasMany(Book_Preview::class);
          }
          //Relation With Property Features
          public function Features():HasOne{
            return $this->HasOne(PropertyFeatures::class,'Property_id');
          }
         
          //Relation With Dealer
          public function Dealer():BelongsTo
          {
               return $this->belongsTo(User::class, 'Dealer_id');
          }
          //Relation With City
          public function City():BelongsTo
          {
               return $this->belongsTo(City::class);
          }
          //Relation With Comment
          public function Comment():HasMany
          {
               return $this->HasMany(Comment::class);
          }
           //Relation With Rate
          public function PropertyRate():HasMany
          {
               return $this->HasMany(Rate::class,'Property_id');
          }
          //Relation With Property Images to get the primary image
          public function PrimaryImage(): HasOne 
          { 
               return $this->hasOne(PropertyImages::class,'property_id')
               ->orderBy('position', 'asc')
               ->withTrashed();
               
          }
          //Relation With Property Images to get all images
          public function PropertyImages(): HasMany 
          { 
               return $this->hasMany(PropertyImages::class);
              
          }
          //Relation With Orders
          public function Orders():HasMany 
          {
                    return $this->hasMany(Order::class);
          }
          
          //Relation With users to get the Favourtie Property
          public function favouriteProperties():BelongsToMany
          {
               return $this->belongsToMany(User::class, 'faivorate_property', 'property_id', 'user_id');
          }    

           public function getCreateDate():string
          {
               return (new Carbon($this->created_at))->format('Y-m-d');
          }     

          public function getTitle()
          {
               return $this->year .' - '.$this->Property->dealer_name;
          }
          public function location()
{
    return $this->hasOne(Location::class);
}


        
}
