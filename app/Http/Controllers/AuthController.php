<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //
    public function index()
    {
        $user = User::get();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.index');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|max:32',
            'password' => 'required',
            'remember' => 'required|boolean',
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('auth.index');
        }

        return back()->withErrors([
            'username' => 'Invalid user credentials'
        ]);
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('login');
    }
}
