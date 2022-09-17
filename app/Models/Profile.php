<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that should be cast to date.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * -------------------------------------------------------------------
     * Relationships
     * -------------------------------------------------------------------
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
