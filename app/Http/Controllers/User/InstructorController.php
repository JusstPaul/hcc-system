<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activities;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
  public function index()
  {
    // FIXME: Fix classrooms query relationship.
    return Inertia::render('Auth/Instructor/Index', [
      'classrooms' => fn () => Classroom::where('instructor_id', User::get()->_id)->get(),
    ]);
  }

  public function classroom_page(String $classroom_id)
  {
    return Inertia::render('Auth/Instructor/Classroom', [
      'classroom_id' => $classroom_id,
    ]);
  }

  public function students_page(String $classroom_id)
  {
    return Inertia::render('Auth/Instructor/Students', [
      'classroom_id' => $classroom_id,
      'students' => fn () => User::where('classroom_joined_id', $classroom_id)
        ->project([
          '_id' => 1,
          'username' => 1,
          'profile' => 1,
        ])
        ->get()
    ]);
  }

  public function create_activity_page(String $classroom_id)
  {
    return Inertia::render('Auth/Instructor/CreateActivity', [
      'classroom_id' => $classroom_id,
    ]);
  }

  public function create_activity_store(Request $request, String $classroom_id)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'start' => 'required|numeric',
      'deadline' => 'required|numeric|gt:start',
      'lockAfterEnd' => 'required|boolean',
      'generalDirections' => 'required|string',
      'questions' => 'required',
      'questions.*' => 'required|array:id,type,values,instruction',
      'questions.*.values.*' => 'required|array:id,instruction,content,answer',
      'target.*' => 'required|array:type,value',
    ]);

    $questions = array_map(function ($item) use ($classroom_id) {
      if (strcmp($item['type'], 'Handwriting Comparator') == 0) {
        $values =  array_map(function ($child) use ($classroom_id) {
          $files = array_map(fn ($file) => storeFile($file['file'], "classroom/$classroom_id/activities"), $child['content']);
          $child['content'] = $files;

          return $child;
        }, $item['values']);

        $item['values'] = $values;
      }
      return $item;
    }, $request->questions);

    Activities::create([
      'title' => $request->title,
      'start' => $request->start,
      'deadline' => $request->deadline,
      'lock_after_end' => $request->lockAfterEnd,
      'general_directions' => $request->generalDirections,
      'questions' => $questions,
      'target' => $request->target,
    ]);

    return redirect()->route('instructor.classroom', [
      'classroom_id' => $classroom_id,
    ]);
  }
}
