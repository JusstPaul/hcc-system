<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

function storeAnswer($file, String $classroom_id, String $activity_id)
{
  return Storage::disk(env('STORAGE', 'public'))
    ->putFileAs("classroom/$classroom_id/answers/$activity_id", $file, $file->getClientOriginalName());
}

function storeAvatar($file)
{
  $user_id = User::get()->_id;
  return storeFile($file, "avatars/$user_id");
}

function fileExists(String $key)
{
  return Storage::disk(env('STORAGE', 'public'))->exists($key);
}

function getFile(String $key)
{
  return Storage::disk(env('STORAGE', 'public'))->get($key);
}

function urlFile(String $key)
{
  return Storage::disk(env('STORAGE', 'public'))
    ->temporaryUrl($key, now()->addHour(2));
}
