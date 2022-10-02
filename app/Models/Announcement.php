<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Announcement extends Model
{
  use HasFactory;

  protected $fillable = [
    'content',
    'fileContents',
  ];

  protected $casts = [
    'content' => 'array',
    'fileContents' => 'array',
  ];

  protected $dates = [
    'created_at',
    'updated_at'
  ];

  public function classroom()
  {
    return $this->belongsTo(Classroom::class);
  }
}
