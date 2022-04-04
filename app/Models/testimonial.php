<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class testimonial extends Model
{
    use HasFactory;

    public function images()
    {
        return $this->hasMany(image_testimonial::class);
    }

    public function user()
    {
        return $this->belongsTo(user::class);
    }
}
