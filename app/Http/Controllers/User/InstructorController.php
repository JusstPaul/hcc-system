<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        dd($request);
    }
}
