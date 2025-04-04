<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index()
    {
        $users = User::all(); // ดึงข้อมูลผู้ใช้ทั้งหมด
        return view('manageUser', compact('users'));
    }

    function create(Request $req)
    {
        $muser = new User();
        $muser->fname = $req->input('fname');
        $muser->lname = $req->input('lname');
        $muser->role = $req->role;
        $muser->email = $req->email;
        $muser->save();
        return redirect('/users');
    }

    function edit_user($id)
    {
        $user = User::find($id);
        $data = $user;
        $allUser = User::all();
        return view('edit', ['users' => $data], compact('allUser'));
    }

    function edit_action(Request $req)
    {
        $muser = User::find($req->id);
        $muser->us_fname = $req->fname;
        $muser->us_lname = $req->lname;
        $muser->us_role = $req->role;
        $muser->us_head = $req->head;
        $muser->us_email = $req->email;
        $muser->save();
        return redirect('/manage-user');
    }

    function add_user()
    {
        return view('addUser');
    }

    /* --}}
    @title : ทำ Contorller ลบบัญชี
    @author : Yothin Sisaitham 66160088
    @create date : 04/04/2568
    --}} */
    public function delete_user(Request $req)
{
    // ตรวจสอบว่ามี ID ถูกส่งมาหรือไม่
    if ($req->has('ids')) {
        $ids = $req->input('ids'); // รับค่า array ของ ID ที่จะลบ

        // ลบผู้ใช้หลายคน
        User::whereIn('id', $ids)->delete();

        return redirect('/manageUser')->with('success', 'ลบผู้ใช้สำเร็จ');
    }

    // ถ้าไม่มี ID → แสดงรายการผู้ใช้ทั้งหมด
    $users = User::all();
    return view('manageUser', compact('users'));
    }
}
