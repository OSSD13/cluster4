<?php



namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function order_detail($br_id)
    {
        $branch = Branch::with('order', 'manager')->where('br_id', $br_id)->first();

        if (!$branch) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลสาขานี้');
        }

        // รวมยอดขายทั้งปี
        $totalSales = $branch->order()
            ->whereYear('created_at', date('Y'))
            ->sum('od_amount');

        // ยอดขายรายเดือน
        $monthlySales = $branch->order()
            ->selectRaw('MONTH(created_at) as month, SUM(od_amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->toArray();

        // ข้อมูล 12 เดือน
        $growthRates = [];
        for ($i = 1; $i <= 12; $i++) {
            $growthRates[Carbon::create()->month($i)->locale('th')->translatedFormat('F')] = $monthlySales[$i] ?? 0;
        }

        return view('orderDetail', compact('branch', 'totalSales', 'growthRates'));
    }

    function index()
    {
        $o = User::join('branch as b', 'users.us_id', '=', 'b.br_us_id') // ดึงข้อมูลผู้ใช้ทั้งหมด
            ->join('order as o', 'b.br_id', '=', 'o.od_br_id')
            ->select('b.br_id', 'b.br_code', 'users.us_image', 'users.us_email', 'o.od_amount')
            ->get();
        return view('order', compact('orders'));
    }

    function add_order()
    {
        return view('addOrder');
    }
}
