<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Activities;
use App\Models\Announcement;
use App\Models\Answer;
use MongoDB\BSON\ObjectId;
use Illuminate\Support\Facades\DB;

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
    $classroom = Classroom::find($classroom_id);

    return Inertia::render('Auth/Instructor/Classroom', [
      'classroom_id' => $classroom_id,
      'announcements' => fn () => $classroom->announcements,
      'activities' => fn () => $classroom->activities,
    ]);
  }

  public function profile_page()
  {
    return Inertia::render('Auth/Instructor/Profile', [
      'profile' => User::get()->profile,
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
        ->get(),
    ]);
  }

  public function create_announcement_store(Request $request, String $classroom_id)
  {
    $request->validate([
      'content' => 'required',
      'fileContents' => 'nullable',
      'fileContents.*' => 'required_if:files,!=,null'
    ]);

    $files = array_map(function ($item) use ($classroom_id) {
      return storeAnnouncement($item['file'], $classroom_id);
    }, $request->fileContents);

    Classroom::find($classroom_id)->announcements()->save(
      new Announcement([
        'content' => $request->content,
        'fileContents' => $files,
      ])
    );

    return redirect()->back();
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
      'generalDirections' => 'required',
      'questions' => 'required',
      'questions.*' => 'required|array:id,type,values,instruction',
      'questions.*.values.*' => 'required|array:id,instruction,content,answer,score',
      'target.*' => 'required|array:type,value',
    ]);

    $questions = array_map(function ($item) use ($classroom_id) {
      if (strcmp($item['type'], 'Handwriting Comparator') == 0) {
        $values = array_map(function ($child) use ($classroom_id) {

          $questioned = storeToClassroomActivities($child['content']['questioned']['file'], $classroom_id);
          $samples = array_map(function ($file) use ($classroom_id) {
            return storeToClassroomActivities($file['file'], $classroom_id);
          }, $child['content']['samples']);

          $child['content'] = [
            'questioned' => $questioned,
            'samples' => $samples,
          ];

          return $child;
        }, $item['values']);

        $item['values'] = $values;
      }

      return $item;
    }, $request->questions);

    Classroom::find($classroom_id)->activities()->create([
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

  public function submits_page(String $classroom_id, String $activity_id)
  {
    return Inertia::render('Auth/Instructor/Submits', [
      'classroom_id' => fn () => $classroom_id,
      'activity' => fn () => Activities::where('_id', $activity_id)
        ->project([
          'title' => 1,
          'start' => 1,
          'deadline' => 1,
          'lock_after_end' => 1,
          'created_at' => 1,
        ])->first(),
      'submits' => fn () => Activities::raw(function ($collection) use ($activity_id) {
        return $collection->aggregate([
          [
            '$match' => [
              '_id' => new ObjectId($activity_id)
            ],
          ],
          [
            '$unwind' => [
              'path' => '$answers'
            ],
          ],
          [
            '$addFields' => [
              'student_id' => [
                '$toObjectId' => '$answers.student_id'
              ],
              'answer_id' => '$answers._id',
            ],
          ],
          [
            '$lookup' => [
              'from' => 'users',
              'localField' => 'student_id',
              'foreignField' => '_id',
              'as' => 'student',
              'pipeline' => [
                [
                  '$project' => [
                    '_id' => 1,
                    'username' => 1,
                    'profile' => 1
                  ],
                ],
              ],
            ],
          ],
          [
            '$project' => [
              'student' => [
                '$first' => '$student'
              ],
              'created_at' => 1,
              'answers' => [
                'checks' => 1,
              ],
              'answer_id' => 1,
            ],
          ]
        ]);
      }),
    ]);
  }

  public function answer_page(String $classroom_id, String $activity_id, String $answer_id)
  {
    return Inertia::render('Auth/Instructor/Answer', [
      'classroom_id' => $classroom_id,
      'activity' => fn () => Activities::raw(function ($collection) use ($activity_id, $answer_id) {
        return $collection->aggregate([
          [
            '$match' => [
              '_id' => new ObjectId($activity_id)
            ],
          ],
          [
            '$unwind' => [
              'path' => '$answers'
            ],
          ],
          [
            '$addFields' => [
              'student_id' => [
                '$toObjectId' => '$answers.student_id'
              ],
              'answer_id' => '$answers._id'
            ],
          ],
          [
            '$match' => [
              'answer_id' => new ObjectId($answer_id)
            ],
          ],
          [
            '$lookup' => [
              'from' => 'users',
              'localField' => 'student_id',
              'foreignField' => '_id',
              'as' => 'student',
              'pipeline' => [
                [
                  '$project' => [
                    '_id' => 1,
                    'username' => 1,
                    'profile' => [
                      '_id' => 1,
                      'l_name' => 1,
                      'f_name' => 1,
                      'm_name' => 1
                    ],
                  ],
                ],
              ],
            ],
          ],
          [
            '$project' => [
              'answers' => [
                '_id' => 1,
                'created_at' => 1,
                'answers' => 1,
                'checks' => 1,
              ],
              'student' => [
                '$first' => '$student'
              ],
              'created_at' => 1,
              'title' => 1,
              'deadline' => 1,
              'questions' => 1,
              'general_directions' => 1,
            ]
          ]
        ]);
      })->first(),
    ]);
  }

  public function answer_store(Request $request, String $classroom_id, String $activity_id, String $answer_id)
  {
    $request->validate([
      'checks.*' => 'required',
      'checks.*.*' => 'required',
      'checks.*.*.id' => 'required|string',
      'checks.*.*.score' => 'required|numeric',
      'checks.*.*.total' => 'required|numeric',
      'checks.*.*.comment' => 'nullable',
    ]);

    $answer = Activities::where('_id', $activity_id)->first()->answers()->where('_id', $answer_id)->first();
    $answer->checks = $request->checks;
    $answer->save();

    return redirect()->route('instructor.activity.submits', [
      'classroom_id' => $classroom_id,
      'activity_id' => $activity_id,
    ]);
  }
}
