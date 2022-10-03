<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Answer extends Model
{
  use HasFactory;

  protected $fillable = [
    'student_id',
    'answers'
  ];

  protected $dates = [
    'created_at',
    'updated_at'
  ];

  public function activity()
  {
    return $this->belongsToMany(Activities::class);
  }
}
