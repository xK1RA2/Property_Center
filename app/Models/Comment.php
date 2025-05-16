<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    //Relation With Property
    protected $fillable = [
    
     'user_id',
     'Property_id',
     'comment_type_id',
     'Description'
 ];
    public function Property():HasMany
    {
         return $this->HasMany(Property::class,'user_id','property_id');
    }
    public function user():belongsTo
    {
         return $this->belongsTo(User::class,'user_id');
    }     
    public function Replay():HasMany{
     return $this->HasMany(Replay::class);
    }

    public function CommentType():HasOne
    {
         return $this->HasOne(CommentType::class);
    }
    

}
