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
            'questions.*' => 'required',
            'questions.*.*' => 'required|array:id,type,value,answer,instruction',
            'target.*' => 'required|array:type,value',
        ]);

        $questions = array_map(function ($item) use ($classroom_id) {
            return array_map(function ($child) use ($classroom_id) {
                if (strcmp($child['type'], 'Handwriting Comparator') == 0) {
                    $files = array_map(fn ($file) => storeFile($file, "classroom/$classroom_id/activities"), $child['value']);
                    $child['value'] = $files;
                }

                return $child;
            }, $item);
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
