<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Profile extends Model
{
  /**
   * Fillable attributes.
   *
   * @var array<string, string>
   */
  protected $fillable = [
    'avatar',
    'l_name',
    'm_name',
    'f_name',
    'details',
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
   * The attributes that should be cast to a specific type.
   */
  protected $casts = [
    'details' => 'array',
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
