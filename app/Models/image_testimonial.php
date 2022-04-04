<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image_testimonial extends Model
{
    use HasFactory;

    public function testimonial()
    {
        return $this->belongsTo(event::class);
    }
}
