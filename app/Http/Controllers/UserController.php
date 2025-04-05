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
        return view('editUser', ['users' => $data], compact('allUser'));
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
        return redirect()->route('manage.user');
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
    public function delete_user(Request $req){
    if ($req->has('ids')) { // เช็คว่ามี ids หรือไม่
        User::whereIn('us_id', $req->ids)->delete(); // ใช้ whereIn เพื่อลบหลายรายการพร้อมกัน
    } else if ($req->has('id')) { // ถ้ามี id เดียว
        User::where('us_id', $req->id)->delete(); // ถ้ามี id เดียวให้ลบรายการนั้น
    }

    return redirect()->route('manage.user');
    }
}
