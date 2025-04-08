<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    function index()
    {
        $users = User::all(); // ดึงข้อมูลผู้ใช้ทั้งหมด
        return view('manageUser', compact('users'));
    }

    // Aninthita Prasoetsang 66160381
    public function create(Request $request)
    {
        // ตรวจสอบค่าที่รับมา
        $request->validate([
            'username' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'role' => 'required|string',
            'email' => 'nullable|email|max:255',
            'head' => 'nullable|exists:users,us_id', // ต้องมีเฉพาะบางตำแหน่ง
        ]);
        // สร้างผู้ใช้ใหม่ในฐานข้อมูล
        User::create([
            'us_fname' => $request->username,
            'us_lname' => $request->lastname,
            'us_email' => $request->email,
            'us_role' => $request->role,
            'us_head' => $request->head ?? null, // ถ้าไม่มีหัวหน้างาน ให้เป็น null
        ]);
        return redirect()->route('manage.user')->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
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

    public function add_user()
    {
        $allUser = User::where('us_role', 'Sales Supervisor')->get();
        return view('addUser', compact('allUser'));
    }

    /* --}}
    @title : ทำ Contorller ลบบัญชี
    @author : Yothin Sisaitham 66160088
    @create date : 04/04/2568
    --}} */
    public function delete_user(Request $req)
    {
        if ($req->has('ids')) { // เช็คว่ามี id หรือไม่
            User::whereIn('us_id', $req->ids)->delete(); // ใช้ whereIn เพื่อลบหลายรายการพร้อมกัน
        } else if ($req->has('id')) { // ถ้ามี id เดียว
            User::where('us_id', $req->id)->delete(); // ถ้ามี id เดียวให้ลบรายการนั้น
        }
        return redirect()->route('manage.user');
    }
    function Emp_GrowRate()
    {
        $salesCount = User::where('us_role', 'Sales')->count();
        $supervisorCount = User::where('us_role', 'Sales Supervisor')->count();
        $ceoCount = User::where('us_role', 'CEO')->count();
        $totalEmployees = User::count();
        $monthGrowrate = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', 2025)
            ->whereNotNull('created_at')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');

        $label = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $growthData = [];

        for ($i = 1; $i <= 12; $i++) {
            $growthData[] = $monthGrowrate[$i] ?? 0; // ถ้าเดือนไหนไม่มี ให้ใส่ 0
        }

        return view('EmployeeGrowthRate', compact(
            'salesCount',
            'supervisorCount',
            'ceoCount',
            'totalEmployees',
            'growthData'
        ));
    }
}
