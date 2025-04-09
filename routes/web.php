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
use App\Http\Middleware\CheckGoogleLogin;
use App\Models\Branch;
use PHPUnit\Runner\HookMethod;

// @author : Pakkapon Chomchoey 66160080
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/logout', function () {
    Session::forget('google_user');
    Session::flush();
    Auth::logout();
    return Redirect('/login');
})->name('logout');

Route::get('/cluster4/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('redirect.google');

Route::get('/cluster4/auth/google/callback', [GoogleLoginController::class, 'googleCallback'])->name('callback.google');


Route::get('/', [UserController::class, 'index']);

//kuy mork
Route::get('/dashboard', [DashboardController::class, 'branchGrowthRate'])->name('dashboard.branch.growth');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/manage-user', [UserController::class, 'index'])->name('manage.user');

Route::get('/add-user', [UserController::class, 'add_user'])->name('add.user');

Route::post('/add-user', [UserController::class, 'create'])->name('create.user');

Route::delete('/delete-user', [UserController::class, 'delete_user'])->name('delete.user');

Route::get('/edit-user/{id}', [UserController::class, 'edit_user']);

Route::put('/edit-user', [UserController::class, 'edit_action'])->name('edit.user');

// Route::get('/cluster4/branchMyMap', [branchController::class, 'index'])->name('branchMyMap');

Route::get('/map', MapLocation::class)->name('map');


Route::get('/order-detail/{br_id}', [OrderController::class, 'order_detail'])->name('orderDetail');
// Route::get('/cluster4/branch-detail/{br_id}', [BranchController::class, 'branch_detail'])->name('branchDetail');
// Route::get('/cluster4/edit-branch-detail/{br_id},{br_mount}', [BranchController::class, 'branch_detail'])->name('branchDetail');


Route::get('/order', [OrderController::class, 'index'])->name('order');

Route::get('/add-order', [OrderController::class, 'add_order']);

//@author Thakdanai
// หน้าแก้ไขคำสั่งซื้อ
Route::get('/editOrder/{od_id}', [OrderController::class, 'editOrder'])->name('edit.order');
// อัปเดตคำสั่งซื้อ
Route::put('/edit-order/{od_id}', [OrderController::class, 'update'])->name('update.order');
Route::get('/order-detail/{br_id}', [OrderController::class, 'order_detail'])->name('order.detail');

// ลบคำสั่งซื้อ (ใช้วิธีเปลี่ยนยอดขายเป็น 0)
Route::post('/delete-order/{id}', [OrderController::class, 'delete_order_detail'])->name('delete.order');

Route::get('/order-status', [OrderController::class, 'status'])->name('order.status');
