<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use MongoDB\BSON\ObjectId;

class StudentController extends Controller
{
    public function index(String $student_id)
    {
        return Inertia::render('Auth/Student/Index', [
            'student_id' => $student_id,
            'activities' => fn () => User::raw(function ($collection) use ($student_id) {
                return $collection->aggregate([
                    [
                        '$match' => [
                            '_id' => [
                                '$eq' => new ObjectId('632c763790dc1985b8046dd7')
                            ],
                        ],
                    ],
                    [
                        '$lookup' => [
                            'from' => 'activities',
                            'localField' => 'classroom_joined_id',
                            'foreignField' => 'target.value',
                            'as' => 'activities'
                        ],
                    ],
                    [
                        '$match' => [
                            'activities' => [
                                '$elemMatch' => [
                                    'target' => [
                                        '$elemMatch' => [
                                            'type' => 'classroom'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        '$project' => [
                            'activities' => 1,
                            '_id' => 0
                        ],
                    ],
                ]);
            })->first()->activities,
        ]);
    }
}
