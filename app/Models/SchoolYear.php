<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'start',
        'end'
    ];

    /**
     * The attributes that should be cast to date.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
