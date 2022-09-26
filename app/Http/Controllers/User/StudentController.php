<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activities;
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
                                '$eq' => new ObjectId($student_id)
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

    public function activity_page(String $student_id, String $activity_id)
    {
        return Inertia::render('Auth/Student/Activity', [
            'student_id' => $student_id,
            'activity' => function () use ($activity_id) {
                $activity = Activities::find($activity_id)->toArray();
                $activity['questions'] = json_encode($activity['questions']);
                $activity['questions'] = json_decode($activity['questions'], true);

                return $activity;
            }
        ]);
    }
}
