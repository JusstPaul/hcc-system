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
            'deadline' => 'required',
            'lockAfterEnd' => 'required|boolean',
            'generalDirections' => 'required|string',
            'questions.*' => 'required|array:id,type,value,answer',
            'target.*' => 'required|array:type,value',
        ]);

        $questions = array_map(function ($item) use ($classroom_id) {
            if (strcmp($item['type'], 'Handwriting Comparator') == 0) {
                $files = array_map(function ($file) use ($classroom_id) {
                    return Storage::disk('public')
                        ->put("classroom/$classroom_id/activities", new File($file['file']));
                }, $item['value']);

                $item['value'] = $files;
            }

            return $item;
        }, $request->questions);

        Activities::create([
            'title' => $request->title,
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
