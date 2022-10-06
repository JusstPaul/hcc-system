<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Profile  $profile
   * @return \Illuminate\Http\Response
   */
  public function show(Profile $profile)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Profile  $profile
   * @return \Illuminate\Http\Response
   */
  public function edit(Profile $profile)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Profile  $profile
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, String $id)
  {

    $user = User::get();

    $request->validate([
      'avatar' => 'nullable',
      'avatar.file' => 'required_with:avatar,image',
      'lName' => 'required|string',
      'mName' => 'nullable|string',
      'fName' => 'required|string',
      'changePassword' => 'required|boolean',
      'oPassword' => 'nullable|required_if:changePassword,true|current_password|string',
      'nPassword' => 'nullable|required_if:changePassword,true|string',
      'details' => [
        Rule::requiredIf(fn () => !$user->hasRole('admin')),
        'nullable'
      ],
    ]);

    // TODO: Accomodate other roles
    if ($request->changePassword) {
      $user->update([
        'password' => Hash::make($request->nPassword)
      ]);
    }

    $avatar = null;
    if (!is_null($request->avatar)) {
      $avatar = storeAvatar($request->avatar['file']);
    }

    if ($user->hasRole('admin')) {
      $user->profile->update([
        'avatar' => $avatar,
        'l_name' => strtoupper($request->lName),
        'm_name' => strtoupper($request->mName),
        'f_name' => strtoupper($request->fName),
        'details' => null,
      ]);
    } else {
      $details = $request->details;
      $details['contactPerson'] = strtoupper($details['contactPerson']);
      $details['email'] = strtoupper($details['email']);

      $user->profile->update([
        'avatar' => $avatar,
        'l_name' => strtoupper($request->lName),
        'm_name' => strtoupper($request->mName),
        'f_name' => strtoupper($request->fName),
        'details' => $details,
      ]);
    }

    return redirect()->back();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Profile  $profile
   * @return \Illuminate\Http\Response
   */
  public function destroy(Profile $profile)
  {
    //
  }
}
