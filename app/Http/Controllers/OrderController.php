<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller{

    public function index(){
        $orders = Order::all();
        $users = User::all();

        // เพิ่ม field thai_month และ thai_year ให้กับแต่ละรายการ
        foreach ($orders as $order) {
            $carbonDate = Carbon::parse($order->od_month)->locale('th');
            $order->thai_month = $carbonDate->translatedFormat('F'); // เดือนแบบไทย
            $order->thai_year = $carbonDate->year + 543; // ปีพุทธศักราช
        }
        return view('manageOrder', compact('orders', 'users'));
    }

    public function edit($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return redirect()->route('manage.order')->with('error', 'ไม่พบคำสั่งซื้อที่มี ID นี้');
        }

        $users = User::all(); // ถ้าจะใช้ dropdown
        return view('editOrder', compact('order', 'users'));
    }

    public function update(Request $req){
    // ตรวจสอบว่ามีคำสั่งซื้อหรือไม่
    $order = Order::find($req->od_id);

    if (!$order) {
        return redirect()->route('manage.order')->with('error', 'ไม่พบคำสั่งซื้อที่มี ID นี้');
    }
    // อัปเดตข้อมูล
    $order->od_amount = $req->od_amount;
    $order->od_month = $req->od_month;
    $order->od_year = $req->od_year;
    $order->od_br_id = $req->od_br_id;
    $order->od_us_id = $req->od_us_id;
    $order->save();

    // ส่งกลับพร้อมข้อความสำเร็จ
    return redirect('/manage-order')->with('success', 'อัปเดตคำสั่งซื้อสำเร็จ');
    }
}
