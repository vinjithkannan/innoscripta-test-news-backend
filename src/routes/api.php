<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\NewsFeedController;
use App\Models\Source;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/check', [AuthController::class, 'check']);
Route::get('/authors', function() {
    return response()->json(
        Author::orderBy('name', 'asc')->get()
    );
});
Route::get('/categories', function() {
    return response()->json(
        Category::orderBy('title', 'asc')->get()
    );
});
Route::get('/sources', function() {
    return response()->json(
        Source::orderBy('title', 'asc')->get()
    );
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/news', [NewsFeedController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user-preference', [UserPreferenceController::class, 'store']);

    Route::post('/news', [NewsFeedController::class, 'index']);
});
