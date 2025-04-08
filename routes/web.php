<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\branchController;
use App\Http\Controllers\HomeController;

use App\Livewire\MapLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesTeamController;
use App\Http\Middleware\CheckGoogleLogin;
use Doctrine\DBAL\Driver\Middleware;
use PHPUnit\Runner\HookMethod;

// @author : Pakkapon Chomchoey 66160080
Route::get('/cluster4/login', function () {
    return view('login');
})->name('login');

Route::get('/cluster4/logout', function () {
    Session::forget('google_user');
    Session::flush();
    Auth::logout();
    return Redirect()->route('login');
})->name('logout');

Route::get('/cluster4/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('redirect.google');

Route::get('/cluster4/auth/google/callback', [GoogleLoginController::class, 'googleCallback'])->name('callback.google');

Route::middleware([CheckGoogleLogin::class])->group(
    function () {
        Route::get('/', [UserController::class, 'index'])->name('home');

        Route::get('/cluster4/home', [HomeController::class, 'index'])->name('home');


        Route::get('/cluster4/manage-user', [UserController::class, 'index'])->name('manage.user');


        Route::get('/cluster4/add-user', [UserController::class, 'add_user'])->name('add.user');
        Route::post('/cluster4/add-user', [UserController::class, 'create'])->name('create.user');


        Route::delete('/cluster4/delete-user', [UserController::class, 'delete_user'])->name('delete.user');

        Route::get('/cluster4/edit-user/{id}', [UserController::class, 'edit_user']);

        Route::put('/cluster4/edit-user', [UserController::class, 'edit_action'])->name('edit.user');

        Route::get('/cluster4/branchMyMap', [branchController::class, 'index'])->name('branchMyMap');

        Route::get('/cluster4/map', MapLocation::class)->name('map');

        // Aninthita 66160381
        Route::get('/cluster4/order-detail/{br_id}', [OrderController::class, 'order_detail']);


        Route::get('/cluster4/order', [OrderController::class, 'index'])->name('order');

        Route::get('/cluster4/add-order', [OrderController::class, 'add_order']);

        Route::get('/cluster4/order-status', [OrderController::class, 'status'])->name('order.status');


Route::get('/cluster4/order-status', [OrderController::class, 'status'])->name('order.status');

Route::get('/cluster4/report/team/{id}', [SalesTeamController::class, 'detail']);





    }
);

