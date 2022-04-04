<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image_event extends Model
{
    use HasFactory;

    public function event()
    {
        return $this->belongsTo(event::class);
    }
}
