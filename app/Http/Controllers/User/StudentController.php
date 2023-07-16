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
      'activities' => function () use ($user) {
        $activites = Activities::raw(function ($collection) use ($user) {
          return $collection->aggregate([
            [
              '$match' => [
                'classroom_id' => $user->classroom_joined_id,
              ],
            ],
            [
              '$project' => [
                'title' => 1,
                'created_at' => 1,
                'start' => 1,
                'lock_after_end' => 1,
                'answers' => [
                  '_id' => 1,
                  'student_id' => 1,
                  'checks' => 1,
                ],
              ],
            ],
          ]);
        });

        if ($activites->isEmpty()) {
          return [];
        }

        return $activites;
      },
    ]);
  }

  public function profile_page(String $student_id)
  {
    return Inertia::render('Auth/Student/Profile', [
      'profile' => User::get()->profile,
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
        if (is_array($question['value']) && array_key_exists('snapshots', $question['value'])) {
          $question['value']['snapshots'] = array_map(function ($snap) use ($classroom_id, $activity_id) {
            $snap['fileContent'] = storeAnswer($snap['fileContent'], $classroom_id, $activity_id);
            return $snap;
          }, $question['value']['snapshots']);
        }

        if (is_array($question['value']) && is_array($question['value'])) {
            // Check first element if file upload
            if (array_key_exists('file', $question['value'][0])) {
                $question['value'] = array_map(function ($file) use ($classroom_id, $activity_id) {
                    return storeAnswer($file['file'], $classroom_id, $activity_id);
                }, $question['value']);
            }
        }

        return $question;
      }, $section['values']);

      return $section;
    }, $request->answers);

    $activity->answers()->save(new Answer([
      'student_id' => $student_id,
      'answers' => $answers,
    ]));

    return redirect()->route('student.index', [
      'student_id' => $student_id,
    ]);
  }

  public function check_page(String $student_id, String $activity_id)
  {
    $activity = Activities::find($activity_id);

    return Inertia::render('Auth/Student/Review', [
        'activity' => $activity,
        'answer' => fn() => $activity->answers()->where('student_id', $student_id)->first()
    ]);
  }
}
