<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Maklad\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Fillable attributes.
     *
     * @var array<string, string>
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * Hide secret attributes.
     *
     * @var array<string, string>
     */
    protected $hidden = [
        'password'
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

    /**
     * -------------------------------------------------------------------
     * Static methods
     * -------------------------------------------------------------------
     */

    /**
     * Casts \Illuminate\Contracts\Auth\Authenticable|null to own User class.
     *
     * @return User
     */
    public static function get(): ?User
    {
        return auth()->user();
    }

    /**
     * Returns authenticated user id.
     *
     * @return ?string
     */
    public static function id(): ?string
    {
        $user = User::get();
        if ($user) {
            return $user->_id;
        }
        return null;
    }

    /**
     * -------------------------------------------------------------------
     * Relationships
     * -------------------------------------------------------------------
     */
    public function profile()
    {
        return $this->embedsOne(Profile::class);
    }

    public function classroom_handled()
    {
        return $this->hasMany(Classroom::class, '_id', 'classroom_handled_ids');
    }

    public function classroom_joined()
    {
        return $this->belongsTo(Classroom::class, '_id', 'classroom_joined_id');
    }
}
