<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Replay extends Model
{
    public function Replay():BelongsTo{
        return $this->belongsTo(Replay::class);
       }
}
