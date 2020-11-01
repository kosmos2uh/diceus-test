<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/next', [IndexController::class, 'nextWeek'])->name('next');
Route::get('/resetleague', [IndexController::class, 'resetLeague'])->name('resetLeague');
Route::get('/playall', [IndexController::class, 'playAll'])->name('playall');
Route::resource('game', MatchController::class);

