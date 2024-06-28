<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('rate',function(){
    return view('rate');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('registerMatch',[MatchController::class,'moveToRegisterMatch'])->name('moveToRegisterMatch')->middleware('auth');
Route::post('registerMatch',[MatchController::class,'registerMatchFanc'])->name('registerMatch')->middleware('auth');
Route::get('rate', [MatchController::class, 'moveToRate'])->name('moveToRate')->middleware('auth');
Route::post('get-character-win-rate', [MatchController::class, 'calcWinRate'])->name('calcWinRate')->middleware('auth');
Route::post('resetMatch',[MatchController::class,'resetMatch'])->name('resetMatch')->middleware('auth');

