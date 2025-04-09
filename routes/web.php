<?php


use App\Http\Controllers\ReportSalesSupervisorController;
use App\Http\Controllers\branchSalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesSupervisorController;
use App\Http\Controllers\NearbyController;
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
use SebastianBergmann\CodeCoverage\Report\Xml\Report;



// @author : Pakkapon Chomchoey 66160080

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/logout', function () {
    Session::forget('google_user');
    Session::flush();
    Auth::logout();
    return Redirect()->route('login');
})->name('logout');

Route::get('/cluster4/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('redirect.google');

Route::get('/cluster4/auth/google/callback', [GoogleLoginController::class, 'googleCallback'])->name('callback.google');

Route::middleware([CheckGoogleLogin::class])->group(
    function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('/report/sales-team', [SalesTeamController::class, 'index'])->name('salesTeamMyMap');

        Route::get('/report/sales-team/{id}', [SalesTeamController::class, 'detail'])->name('team.detail');


        Route::get('/manage-user', [UserController::class, 'index'])->name('manage.user');


        Route::get('/add-user', [UserController::class, 'add_user'])->name('add.user');
        Route::post('/add-user', [UserController::class, 'create'])->name('create.user');


        Route::delete('/delete-user', [UserController::class, 'delete_user'])->name('delete.user');


        Route::get('/edit-user/{id}', [UserController::class, 'edit_user'])->name('edit.user.id');

        Route::put('/edit-user', [UserController::class, 'edit_action'])->name('edit.user');

        //CEO
        Route::get('/branchMyMap', [BranchController::class, 'index'])->name('branchMyMap');


        //Sales Supervisor
        Route::get('/report/sales-supervisor', [ReportController::class, 'sales_supervisor'])->name('reportSalesSupervisor');

        Route::get('/branch-detail/{br_id}', [BranchController::class, 'branch_detail'])->name('branchDetail');

        //Sales Supervisor ดูสาขาลูกทีม
        Route::get('/report/sales-supervisor/team', [ReportController::class, 'reportSaleTeam'])->name('reportSalesTeam');

        // ดูสาขาฉัน role Sales
        Route::get('/branch-Sales', [branchSalesController::class, 'branchSales'])->name('branch-Sales');

        Route::get('/map', MapLocation::class)->name('map');
        

        // อันนี้คือไรไม่รู้มั้นมีอยู่ไม่กล้าลบทิ้ง
        // Route::get('/order-detail/{br_id}', [OrderController::class, 'order_detail']);

        // @author 66160381 Aninthita
        Route::get('/cluster4/branch-detail/{br_id}', [branchController::class, 'branch_detail'])->name('branchDetail');

        //order-detail
        Route::get('/cluster4/order-detail/{br_id}', [OrderController::class, 'order_detail']);
        //@author ryu
        Route::get('/editOrder/{od_id}', [OrderController::class, 'editOrder'])->name('edit.order');
        Route::put('/edit-order/{od_id}', [OrderController::class, 'update'])->name('update.order');
        Route::get('/order-detail/{br_id}', [OrderController::class, 'order_detail'])->name('order.detail');
        Route::post('/delete-order/{id}', [OrderController::class, 'delete_order_detail'])->name('delete.order');
        Route::get('/order-status', [OrderController::class, 'status'])->name('order.status');

        


        Route::get('/order', [OrderController::class, 'index'])->name('order');

        Route::get('/add-order', [OrderController::class, 'add_order']);

        Route::get('/nearby/{branchId}', [NearbyController::class, 'index'])->name('nearby');


        Route::get('/add-order/{br_id}/{month}', [OrderController::class, 'add_order'])->name('add.order');

        Route::post('/store-order', [OrderController::class, 'storeOrder'])->name('storeOrder');

        Route::get('/order-status', [OrderController::class, 'status'])->name('order.status');


        Route::get('/reportCEO', [ReportController::class, 'report_CEO'])->name('report_CEO');
        Route::get('/report/team/{id}', [SalesTeamController::class, 'detail']);

        Route::get('/report/SalesSup', [ReportSalesSupervisorController::class, 'reportSalesSupervisor1'])->name('report_SalesSupervisor');
        Route::get('/report/SaleSup/Team', [ReportSalesSupervisorController::class, 'index'])->name('reportSale_sup2');
    }
);
