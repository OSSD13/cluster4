<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleLoginController;
use App\Livewire\MapLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\OrderController;

Route::get('/', [UserController::class, 'index']);

Route::get('/manage-user', [UserController::class, 'index'])->name('manage.user');

Route::get('/add-user', [UserController::class, 'add_user'])->name('add.user');

Route::delete('/delete-user', [UserController::class, 'delete_user'])->name('delete.user');

Route::get('/edit-user/{id}', [UserController::class, 'edit_user']);

Route::put('/edit-user', [UserController::class, 'edit_action'])->name('edit.user');

// เส้นทางแสดงยอดขาย
Route::get('/manage-order', [OrderController::class, 'index'])->name('manage.order');

// เส้นทางแก้ไขยิดขาย
Route::get('/edit-order/{id}', [OrderController::class, 'edit'])->name('edit-order');

// เส้นทางอัปเดตยอดขาย
Route::put('/edit-order/{od_id}', [OrderController::class, 'update'])->name('update-order');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/logout', function () {
    Session::forget('google_user');
    Session::flush();
    Auth::logout();
    return Redirect('/login');
})->name('logout');

Route::get('/map', MapLocation::class)->name('map');

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('redirect.google');

Route::get('auth/google/callback', [GoogleLoginController::class, 'googleCallback'])->name('callback.google');
