<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Maklad\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Admin/Index', [
            'users' => fn () => User::where('id', '!=', User::id()),
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
        // HACK role is specified manually
        $request->validate([
            'username' => 'required|unique:users,username',
            'role' => 'required|in:admin,instructor,student',
            'lName' => 'required|string',
            'mName' => 'nullable|string',
            'fName' => 'required|string',
            'details' => 'json',
            'details.contact' => 'number',
            'details.email' => 'email',
            'details.contactPerson' => 'string',
            'details.contactPersonContact' => 'number',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->username),
        ]);
        $user->assignRole($request->role);

        if ($request->role === 'admin') {
            $user->profile()->create([
                'l_name' => $request->lName,
                'm_name' => strlen($request->mName) === 0 ? null : $request->mName,
                'f_name' => $request->fName,
                'details' => null,
            ]);
        } else {
            $user->profile()->create([
                'l_name' => $request->lName,
                'm_name' => strlen($request->mName) === 0 ? null : $request->mName,
                'f_name' => $request->fName,
                'details' => $request->details,
            ]);
        }

        return redirect()->route('admin.index');
    }

    public function classroom_page()
    {
        return Inertia::render('Auth/Admin/Classrooms');
    }

    public function profile_page()
    {
        return Inertia::render('Auth/Admin/Profile', [
            'profile' =>  User::get()->profile,
        ]);
    }
}
