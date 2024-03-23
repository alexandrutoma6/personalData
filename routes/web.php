<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin');
});

Route::get('/google-calendar/get-code', [GoogleCalendarController::class, 'store'])
->name('google-calendar.store');

Route::get('/google-calendar/disconnect', [GoogleCalendarController::class, 'disconnect'])
->name('google-calendar.disconnect')
->middleware('auth');
