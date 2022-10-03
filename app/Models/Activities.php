<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Activities extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'start',
    'deadline',
    'lock_after_end',
    'general_directions',
    'questions',
    'target',
  ];

  protected $casts = [];

  public function classroom()
  {
    return $this->belongsTo(Classroom::class);
  }

  public function answers()
  {
    return $this->embedsMany(Answer::class);
  }
}
