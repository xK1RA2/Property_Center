<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentType extends Model
{
    protected $table ='Comment_Type';
    public function Comment():HasMany
    {
         return $this->HasMany(Comment::class);
    }
}
