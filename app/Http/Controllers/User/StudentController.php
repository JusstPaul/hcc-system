<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activities;
use App\Models\Answer;
use App\Models\User;
use App\Models\Classroom;
use MongoDB\BSON\ObjectId;

class StudentController extends Controller
{
  public function index(String $student_id)
  {
    $user = User::get();
    $classroom = Classroom::find($user->classroom_joined_id);

    return Inertia::render('Auth/Student/Index', [
      'student_id' => $student_id,
      'joined_class' => fn () => !is_null($classroom),
      'announcements' => function () use ($classroom) {
        if (!is_null($classroom)) {
          return $classroom->announcements;
        }
      },
      'activities' => function () use ($student_id) {
        $activites = User::raw(function ($collection) use ($student_id) {
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
        });

        if ($activites->isEmpty()) {
          return [];
        }

        return $activites->first()->activities;
      },
    ]);
  }

  public function students_page(String $student_id)
  {
    return Inertia::render('Auth/Student/Students', [
      'students' => fn () => User::where('classroom_joined_id', User::get()->classroom_joined_id)
        ->project([
          '_id' => 1,
          'username' => 1,
          'profile' => 1,
        ])
        ->get(),
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

  public function activity_store(Request $request, String $student_id, String $activity_id)
  {
    $request->validate([
      'answers.*' => 'required'
    ]);


    $activity = Activities::find($activity_id);
    $classroom_id = $activity->classroom->_id;

    $answers = array_map(function ($section) use ($classroom_id, $activity_id) {
      $section['values'] = array_map(function ($question) use ($classroom_id, $activity_id) {
        if (is_array($question['value'])) {
          $question['value'] = array_map(function ($snap) use ($classroom_id, $activity_id) {
            $snap['file'] = storeAnswer($snap['file'], $classroom_id, $activity_id);
            return $snap;
          }, $question['value']);
        }

        return $question;
      }, $section['values']);

      return $section;
    }, $request->answers);

    $activity->answers()->create([
      'student_id' => $student_id,
      'answers' => $answers,
    ]);

    return redirect()->route('student.index', [
      'student_id' => $student_id,
    ]);
  }
}
