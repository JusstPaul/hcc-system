<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'deadline',
        'lock_after_end',
        'general_directions',
        'questions',
        'target',
    ];

    protected $casts = [];
}
