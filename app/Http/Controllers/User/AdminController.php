<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Maklad\Permission\Models\Role;
use MongoDB\BSON\ObjectId;


class AdminController extends Controller
{
    public function index()
    {
        $user_id = User::get()->id();

        return Inertia::render('Auth/Admin/Index', [
            'users' => fn () => User::raw(function ($collection) use ($user_id) {
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
                        '$addFields' => [
                            'role_ids' => [
                                '$toObjectId' => [
                                    '$first' => '$role_ids'
                                ],
                            ],
                        ],
                    ],
                    [
                        '$lookup' => [
                            'from' => 'roles',
                            'localField' => 'role_ids',
                            'foreignField' => '_id',
                            'as' => 'role_name'
                        ],
                    ],
                    [
                        '$set' => [
                            'role_name' => [
                                '$first' => '$role_name'
                            ],
                        ],
                    ],
                    [
                        '$set' => [
                            'role_name' => '$role_name.name'
                        ],
                    ],
                    [
                        '$project' => [
                            '_id' => 1,
                            'username' => 1,
                            'profile.l_name' => 1,
                            'profile.f_name' => 1,
                            'profile.m_name' => 1,
                            'profile.details' => 1,
                            'role_name' => 1,
                        ],
                    ],
                ]);
            })
        ]);
    }

    public function create_user_page()
    {
        return Inertia::render('Auth/Admin/CreateUser', [
            'roles' => Role::all()->map(fn ($value) => $value->name)
        ]);
    }

    public function create_user_store(Request $request)
    {
        // HACK: role is specified manually
        $request->validate([
            'username' => 'required|unique:users,username',
            'role' => 'required|in:admin,instructor,student',
            'lName' => 'required|string',
            'mName' => 'nullable|string',
            'fName' => 'required|string',
            'details' => 'required_unless:role,admin|nullable',
            'details.contact' => 'required_unless:role,admin|nullable|numeric',
            'details.email' => 'required_unless:role,admin|nullable|email',
            'details.contactPerson' => 'required_if:role,student|nullable|string',
            'details.contactPersonContact' => 'required_if:role,student|nullable|numeric',
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

    // TODO: Handle archiving for new school year to start
    public function school_year_store()
    {
        SchoolYear::create([
            'start' => date('Y'),
            'end' => date('Y', strtotime('+1 year'))
        ]);

        return redirect()->back(200);
    }

    public function classroom_page()
    {
        return Inertia::render('Auth/Admin/Classrooms', [
            'school_year' => fn () => SchoolYear::latest()->first()
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
        dd($request);
    }

    public function profile_page()
    {
        return Inertia::render('Auth/Admin/Profile', [
            'profile' =>  User::get()->profile,
        ]);
    }
}
