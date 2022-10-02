<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;

/**
 * -----------------------------------------------------------------------
 * Collection of general helper functions
 * -----------------------------------------------------------------------
 */


function storeFile($file, String $parent = '')
{
  return Storage::disk(env('STORAGE', 'public'))
    ->put($parent, new File($file));
}

function storeToClassroomActivities($file, String $classroom_id)
{
  return storeFile($file, "classroom/$classroom_id/activities");
}

function storeAnnouncement($file, String $classroom_id)
{
  $original = $file->getClientOriginalName();
  $hash = Hash::make(strval(time()));
  return Storage::disk(env('STORAGE', 'public'))
    ->put("classroom/$classroom_id/announcements/$hash/$original", $file);
}

function fileExists(String $key)
{
  return Storage::disk(env('STORAGE', 'public'))->exists($key);
}

function getFile(String $key)
{
  return Storage::disk(env('STORAGE', 'public'))->get($key);
}
