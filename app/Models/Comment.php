<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Comment extends Model
{
    //Relation With Property
    public function Property():HasMany
    {
         return $this->HasMany(Property::class,'user_id','property_id');
    }

    public function CommentType():HasOne
    {
         return $this->HasOne(CommentType::class);
    }
    

}
