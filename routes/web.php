<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OneloginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('auth/onelogin', [OneloginController::class, 'oneloginRedirect']);
Route::get('auth/onelogin/callback', [OneloginController::class, 'callbackOnelogin']);
