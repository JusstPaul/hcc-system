<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Maklad\Permission\Models\Role;
use MongoDB\BSON\ObjectId;

use function PHPSTORM_META\map;

class AdminController extends Controller
{
  public function index()
  {
    $user_id = User::get()->id();

    // $collection = User::raw(function ($collection) use ($user_id) {
    //     return $collection->aggregate([
    //       [
    //         '$match' => [
    //           '_id' => [
    //             // NOTE: This is not an error.
    //             '$ne' => new ObjectId($user_id)
    //           ]
    //         ]
    //       ],
    //       [
    //         '$project' => [
    //           '_id' => 1,
    //           'username' => 1,
    //           'profile.l_name' => 1,
    //           'profile.f_name' => 1,
    //           'profile.m_name' => 1,
    //           'profile.details' => 1,
    //         ],
    //       ],
    //     ]);
    //   });

    return Inertia::render('Auth/Admin/Index', [
      //'users' => fn () => $collection->toQuery()->paginate(10, ['*'], 'page', 1),
      'users' => fn() => User::raw(function ($collection) use ($user_id) {
        return $collection->aggregate([
          [
            '$match' => [
              '_id' => [
                // NOTE: This is not an error.
                '$ne' => new ObjectId($user_id)
              ]
            ]
          ],
          [
            '$project' => [
              '_id' => 1,
              'username' => 1,
              'role_ids' => 1,
              'profile.l_name' => 1,
              'profile.f_name' => 1,
              'profile.m_name' => 1,
              'profile.details' => 1,
            ],
          ],
        ]);
      }),
      'roles' => fn () => Role::all()
    ]);
  }

  public function create_user_page()
  {
    return Inertia::render('Auth/Admin/CreateUser', [
      'roles' => fn() => Role::all()->map(fn ($value) => $value->name)
    ]);
  }

  public function edit_user_page(String $user_id)
  {
    return Inertia::render('Auth/Admin/EditUser', [
      'user_edit' => function() use ($user_id) {
        $user_match = User::raw(function ($collection) use ($user_id) {
          return $collection->aggregate([
            [
              '$match' => [
                '_id' => new ObjectId($user_id)
              ],
            ],
            [
              '$addFields' => [
                'str_role' => [
                  '$toObjectId' => [
                    '$first' => '$role_ids'
                  ],
                ],
              ],
            ],
            [
              '$lookup' => [
                'from' => 'roles',
                'localField' => 'str_role',
                'foreignField' => '_id',
                'as' => 'role_raw'
              ],
            ],
            [
              '$set' => [
                'role' => [
                  '$first' => '$role_raw'
                ],
              ],
            ],
            [
              '$set' => [
                'role' => '$role.name'
              ],
            ],
            [
              '$project' => [
                '_id' => 1,
                'username' => 1,
                'role' => 1,
                'profile' => 1
              ],
            ],
          ]);
        });

        return $user_match->first();
      },
      'roles' => fn() => Role::all()->map(fn ($value) => $value->name),
    ]);
  }

  public function update_user(Request $request, String $user_id)
  {
    //! HACK: role is specified manually
    $request->validate([
      'username' => 'required',
      'role' => 'required|in:admin,instructor,student',
      'lName' => 'required|string',
      'mName' => 'nullable|string',
      'fName' => 'required|string',
      'details' => 'required_unless:role,admin|nullable',
      'details.contact' => 'required_unless:role,admin|nullable|regex:/09[0-9]{9}+/',
      'details.email' => 'required_unless:role,admin|nullable|email',
      'details.contactPerson' => 'required_if:role,student|nullable|string',
      'details.contactPersonContact' => 'required_if:role,student|nullable|regex:/09[0-9]{9}+/',
    ]);

    $user = User::find($user_id);
    $user->update([
      'username' => $request->username,
    ]);
    $user->assignRole($request->role);

    $l_name = strtoupper($request->lName);
    $m_name = strlen($request->mName) === 0 ? null : strtoupper($request->mName);
    $f_name = strtoupper($request->fName);

    if ($request->role === 'admin') {
      $user->profile->update([
        'l_name' => $l_name,
        'm_name' => $m_name,
        'f_name' => $f_name,
        'details' => null,
      ]);
    } else {
      $details = $request->details;
      $details['contactPerson'] = strtoupper($details['contactPerson']);
      $details['email'] = strtoupper($details['email']);

      $user->profile->update([
        'l_name' => $l_name,
        'm_name' => $m_name,
        'f_name' => $f_name,
        'details' => $details,
      ]);
    }

    return redirect()->route('admin.index');
  }

  public function create_user_store(Request $request)
  {
    //! HACK: role is specified manually
    $request->validate([
      'username' => 'required|unique:users,username',
      'role' => 'required|in:admin,instructor,student',
      'lName' => 'required|string',
      'mName' => 'nullable|string',
      'fName' => 'required|string',
      'details' => 'required_unless:role,admin|nullable',
      'details.contact' => 'required_unless:role,admin|nullable|regex:/09[0-9]{9}+/',
      'details.email' => 'required_unless:role,admin|nullable|email',
      'details.contactPerson' => 'required_if:role,student|nullable|string',
      'details.contactPersonContact' => 'required_if:role,student|nullable|regex:/09[0-9]{9}+/',
    ]);

    $user = User::create([
      'username' => $request->username,
      'password' => Hash::make($request->username),
    ]);
    $user->assignRole($request->role);

    $l_name = strtoupper($request->lName);
    $m_name = strlen($request->mName) === 0 ? null : strtoupper($request->mName);
    $f_name = strtoupper($request->fName);

    if ($request->role === 'admin') {
      $user->profile()->create([
        'l_name' => $l_name,
        'm_name' => $m_name,
        'f_name' => $f_name,
        'details' => null,
      ]);
    } else {
      $details = $request->details;
      $details['contactPerson'] = strtoupper($details['contactPerson']);
      $details['email'] = strtoupper($details['email']);

      $user->profile()->create([
        'l_name' => $l_name,
        'm_name' => $m_name,
        'f_name' => $f_name,
        'details' => $details,
      ]);
    }

    return redirect()->route('admin.index');
  }

  public function user_destroy(String $user_id)
  {
    User::destroy($user_id);
    return redirect()->route('admin.index');
  }

  //! TODO: Handle archiving for new school year to start
  public function school_year_store()
  {
    SchoolYear::create([
      'start' => date('Y'),
      'end' => date('Y', strtotime('+1 year'))
    ]);

    return redirect()->back();
  }

  public function classroom_page()
  {
    //! TODO: Prompt to create school year first.
    $school_year = SchoolYear::latest()->first();

    return Inertia::render('Auth/Admin/Classrooms', [
      'school_year' => fn () => $school_year,
      'classrooms' => fn () => is_null($school_year) ? [] : $school_year->classrooms()
        ->raw(function ($collection) use ($school_year) {
          $id = $school_year['id'];

          return $collection->aggregate([
            [
              '$addFields' => [
                'str_id' => [
                  '$toString' => '$_id'
                ],
              ],
            ],
            [
              '$lookup' => [
                'from' => 'users',
                'localField' => 'str_id',
                'foreignField' => 'classroom_handled_ids',
                'as' => 'instructor_raw'
              ],
            ],
            [
              '$set' => [
                'instructor' => [
                  '$first' => '$instructor_raw'
                ],
              ],
            ],
            [
                '$match' => [
                    'school_year_id' => "$id"
                ]
            ],
            [
              '$project' => [
                '_id' => 1,
                'section' => 1,
                'room' => 1,
                'time' => 1,
                'day' => 1,
                'time_start' => 1,
                'time_end' => 1,
                'instructor' => 1
              ],
            ],
          ]);
        }),
      'has_instructors' => fn () => !(User::role('instructor')->get()->isEmpty()),
    ]);
  }

  public function create_classroom_page()
  {
    return Inertia::render('Auth/Admin/CreateClassroom', [
      'school_year' => fn () => SchoolYear::latest()->first(),
      'instructors' => fn () => User::role('instructor')->get(),
      'students' => fn () => User::role('student')
        ->whereNull('classroom_joined_id')
        ->get()
    ]);
  }

  public function create_classroom_store(Request $request)
  {
    $request->validate([
      'section' => 'required|string',
      'day' => 'required|in:mw,tth,fs',
      'room' => 'required|string',
      'timeStart' => 'required|date_format:h:i A',
      'timeEnd' => 'required|date_format:h:i A|after:timeStart',
      'instructor' => 'required|string',
      'students' => 'required|array|min:1',
      'students.*' => 'required|string|distinct',
    ]);

    // TODO: Lessen the ammount of requests to the database.
    $classroom = Classroom::create([
      'section' => $request->section,
      'day' => $request->day,
      'room' => $request->room,
      'time_start' => $request->timeStart,
      'time_end' => $request->timeEnd,
      'instructor_id' => $request->instructor,
      'student_ids' => $request->students,
    ]);
    $sy = SchoolYear::latest()->first();
    $sy->classrooms()->save($classroom);


    $instructor = User::find($classroom->instructor_id);
    $instructor->classroom_handled->push($classroom->id);
    DB::collection('users')
      ->where('_id', $classroom->instructor_id)
      ->push('classroom_handled_ids', $classroom->id);

    //? NOTE: Needs testing for relationships.
    DB::collection('users')
      ->whereIn('_id', $classroom->student_ids)
      ->update([
        'classroom_joined_id' => $classroom->id,
      ], [
        'upsert' => true,
      ]);

    return redirect()->route('admin.classrooms');
  }

  public function edit_classroom_page(String $classroom_id)
  {
    return Inertia::render('Auth/Admin/EditClassroom', [
      'classroom' => fn() => Classroom::find($classroom_id)->first(), //* HACK: Why does this return an array?
      'added_students' => fn() => User::where('classroom_joined_id', $classroom_id)
        ->project([
          '_id' => 1,
          'username' => 1,
          'profile' => 1,
        ])
        ->get(),
      'instructors' => fn () => User::role('instructor')->get(),
      'available_students' => fn() => User::role('student')
        ->whereNull('classroom_joined_id')
        ->get()
    ]);
  }

  public function update_classroom(Request $request, String $classroom_id)
  {
    $request->validate([
      'section' => 'required|string',
      'day' => 'required|in:mw,tth,fs',
      'room' => 'required|string',
      'timeStart' => 'required|date_format:h:i A',
      'timeEnd' => 'required|date_format:h:i A|after:timeStart',
      'instructor' => 'required|string',
      'studentsToRemove' => 'nullable|array|min:0',
      'studentsToRemove.*' => 'required|string|distinct',
      'studentsToAdd' => 'nullable|array|min:0',
      'studentsToAdd.*' => 'required|string|distinct',
    ]);

    //TODO Only make a single request to DB
    $classroom = Classroom::find($classroom_id);

    // Update instructor first
    User::find($classroom->instructor_id)->pull('classroom_handled_ids', $classroom_id);
    User::find($request->instructor)->push('classroom_handled_ids', $classroom_id);

    // Update joined students
    DB::collection('users')
      ->whereIn('_id', $request->studentsToRemove)
      ->unset('classroom_joined_id');

    // Add students
    DB::collection('users')
      ->whereIn('_id', $request->studentsToAdd)
      ->update([
        'classroom_joined_id' => $classroom_id,
      ], [ 'upsert' => true ]);

    // Finally update classroom details
    $classroom->update([
      'day' => $request->day,
      'room' => $request->room,
      'time_start' => $request->timeStart,
      'time_end' => $request->timeEnd,
      'instructor_id' => $request->instructor,
      'section' => $request->section,
    ]);
    $classroom->pull('student_ids', $request->studentsToRemove);
    $classroom->push('student_ids', [
        'student_ids' => [
            '$each' => $request->studentsToAdd
        ]
    ]);

    return redirect()->route('admin.classrooms');
  }

  public function classroom_destroy(String $classroom_id)
  {
    DB::collection('users')
      ->where('classroom_handled_ids', $classroom_id)
      ->pull('classroom_handled_ids', $classroom_id);
    DB::collection('users')
      ->where('classroom_joined_id', $classroom_id)
      ->unset('classroom_joined_id');
    Classroom::destroy($classroom_id);

    return redirect()->route('admin.classrooms');
  }

  public function archive_page()
  {
    return Inertia::render('Auth/Admin/Archives', [
        'school_years' => fn () => SchoolYear::all()
            ->sortBy('created_at', SORT_REGULAR, true)
            ->skip(1)
            ->toArray()
    ]);
  }

  public function profile_page()
  {
    return Inertia::render('Auth/Admin/Profile', [
      'profile' =>  User::get()->profile,
    ]);
  }
}
