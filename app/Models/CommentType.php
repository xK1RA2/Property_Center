<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentType extends Model
{
    protected $table = 'comment_types';
    protected $fillable = ['name'];
    public function Comment():HasMany
    {
         return $this->HasMany(Comment::class);
    }
}
