<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesTeamController extends Controller
{
    public function index(Request $request)
    {
        $userRole = Auth::user()->us_role;

        if (Auth::check() && Auth::user()->us_role === 'CEO') {
            $role = $request->query('role');

            $query = User::query();
            if ($role && $role !== 'all') {
                $query->where('us_role', $role);
            }

            $users = $query->paginate(10);

            // นับจำนวน role ต่าง ๆ
            $countAll = User::count();
            $countSales = User::where('us_role', 'Sales')->count();
            $countSupervisor = User::where('us_role', 'Sales Supervisor')->count();
            $countCEO = User::where('us_role', 'CEO')->count();

            return view('salesTeamMyMap', compact('users', 'countAll', 'countSales', 'countSupervisor', 'countCEO', 'userRole'));
        } else if (Auth::check() && Auth::user()->us_role === 'Sales Supervisor') {
            $role = $request->query('role');

            // ดึง ID ของ Supervisor
            $supervisorId = Auth::id();

            $query = User::query();

            // แสดงเฉพาะผู้ใช้ที่มี us_role เป็น 'Sales' และ us_head เป็น Supervisor คนนี้
            $query->where('us_role', 'Sales')
                ->where('us_head', $supervisorId);

            $users = $query->paginate(10);

            // นับเฉพาะ Sales ที่อยู่ในสังกัด
            $countAll = $query->count(); // ทั้งหมดคือ Sales ใต้สังกัดเขา
            $countSales = $countAll; // เพราะกรองเฉพาะ Sales แล้ว
            $countSupervisor = 0; // ไม่ให้ดู Supervisor คนอื่น
            $countCEO = 0; // ไม่ให้ดู CEO

            return view('salesTeamMyMap', compact('users', 'countAll', 'countSales', 'countSupervisor', 'countCEO', 'userRole'));
        }
    }

    public function detail($id)
    {
        $userRole = Auth::user()->us_role;

        if (Auth::check() && Auth::user()->us_role === 'CEO') {
            $user = DB::table('users')->where('us_id', $id)->first();

            $branches = DB::table('branch')
                ->where('br_us_id', $id)
                ->paginate(5);

            return view('detailSalesTeamMyMap', compact('user', 'branches', 'userRole'));
        } else if (Auth::check() && Auth::user()->us_role === 'Sales Supervisor') {
            $user = DB::table('users')->where('us_id', $id)->first();

            $branches = DB::table('branch')
                ->where('br_us_id', $id)
                ->paginate(5);

            return view('detailSalesTeamMyMap', compact('user', 'branches', 'userRole'));
        }
    }
}
