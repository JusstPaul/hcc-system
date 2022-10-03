<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Classroom extends Model
{
  use HasFactory;

  protected $fillable = [
    'section',
    'day',
    'room',
    'time_start',
    'time_end',
    'instructor_id',
    'student_ids',
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

  public function school_year()
  {
    return $this->belongsTo(SchoolYear::class);
  }

  public function instructor()
  {
    return $this->belongsTo(User::class, '_id', 'instructor_id');
  }

  public function students()
  {
    return $this->hasMany(User::class, '_id', 'student_ids');
  }

  public function announcements()
  {
    return $this->hasMany(Announcement::class);
  }

  public function activities()
  {
    return $this->hasMany(Activities::class);
  }
}
