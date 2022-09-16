<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|max:32',
            'password' => 'required',
            'remember' => 'required|boolean',
        ]);

        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials, $request->remember)) {
            dd('Login Successful');
            $request->session()->regenerate();
        }

        return back()->withErrors([
            'username' => 'Invalid user credentials'
        ]);
    }
}
