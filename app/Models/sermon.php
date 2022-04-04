<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sermon extends Model
{
    use HasFactory;

    public function admin()
    {
        return $this->belongsTo(user::class,'user_id');
    }
}
