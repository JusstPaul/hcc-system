<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mimey\MimeTypes;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// TODO
Route::group(['middleware' => ['auth:sanctum']], function () {
});

Route::get('/file', function (Request $request) {
  if (fileExists($request->key)) {
    $dot   = explode('.', $request->key);
    $slash = explode('/', $request->key);

    $file = getFile($request->key);
    $mime = new MimeTypes;

    return response()->make($file, 200, [
      'Content-Type' => $mime->getMimeType(end($dot)),
      'Content-Disposition' => 'attachment; filename="' . end($slash) . '"',
    ]);
  } else {
    return response()->json([
      'file' => 'missing!',
    ], 500);
  }
})->name('api.file');

Route::get('/file_preview', function (Request $request) {
  if (fileExists($request->key)) {
    return  response()->json([
      'file' => urlFile($request->key)
    ], 200);
  } else {
    return response()->json([
      'file' => 'missing!'
    ], 500);
  }
})->name('api.file_preview');
