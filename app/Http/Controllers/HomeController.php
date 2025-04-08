<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{
    function index()
    {
        //@auther : guitar
        $currentYear = Carbon::now()->year + 543;  // Convert to Thai year
        $currentMonth = Carbon::now()->month;

        $months = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม',
        ];

        $currentMonthName = $months[$currentMonth];

        $topBranch = DB::table('users as u')
            ->join('branch as b', 'u.us_id', '=', 'b.br_us_id')
            ->join('order as o', 'b.br_id', '=', 'o.od_br_id')
            ->where('o.od_year', '=', $currentYear)
            ->where('o.od_month', '=', $currentMonthName)
            ->select('b.br_id', 'b.br_code', 'u.us_image', 'u.us_fname', 'o.od_amount', 'o.created_at')
            ->whereIn('o.od_id', function ($query) use ($currentYear, $currentMonthName) {
                // Subquery เพื่อเลือกข้อมูลที่มียอดขายล่าสุดจากแต่ละสาขา
                $query->selectRaw('MAX(o.od_id)')
                    ->from('order as o')
                    ->join('branch as b', 'o.od_br_id', '=', 'b.br_id')
                    ->where('o.od_year', '=', $currentYear)
                    ->where('o.od_month', '=', $currentMonthName)
                    ->groupBy('o.od_br_id');
            })
            ->orderByDesc('o.od_amount')  // เรียงตามยอดขายจากมากไปน้อย
            ->take(5)  // เลือกแค่ 5 อันดับแรก
            ->get();

        //@auther : riw
        // หาพนักงานที่เพิ่มสาขามากที่สุด
        $topUsers = User::withCount('branch')  // ดึงจำนวนสาขา
            ->orderByDesc('branch_count')  // เรียงลำดับตามจำนวนสาขา
            ->take(5)  // ดึง 5 คน
            ->get();


        //@auther : boom
        $monthMap = [
            'มกราคม' => 1,
            'กุมภาพันธ์' => 2,
            'มีนาคม' => 3,
            'เมษายน' => 4,
            'พฤษภาคม' => 5,
            'มิถุนายน' => 6,
            'กรกฎาคม' => 7,
            'สิงหาคม' => 8,
            'กันยายน' => 9,
            'ตุลาคม' => 10,
            'พฤศจิกายน' => 11,
            'ธันวาคม' => 12
        ];



        // ยอดขายทั้งหมด
        $totalSales = Order::sum('od_amount');

        // ยอดขายเดือนก่อนหน้า
        $previousMonthSales = Order::where('od_month', date('m') - 1)->sum('od_amount');

        //หา % การเติบโต (ยอดขายเดือนปัจจุบัน - ยอดขายเดือนก่อน / ยอดขายเดือนก่อน )* 100
        $growthPercentage = $previousMonthSales > 0 ? (($totalSales - $previousMonthSales) / $previousMonthSales) * 100 : 0;

        //ค่าเฉลี่ย
        $averageSales = Order::avg('od_amount');

        // ดึงข้อมูลยอดขายรายเดือน
        $orderData = Order::where('od_year', 2568) // ปีล่าสุด
            ->selectRaw('od_month, SUM(od_amount) as total_sales')
            ->groupBy('od_month')
            ->orderByRaw("FIELD(od_month, 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม')")
            ->get();

        // เตรียมข้อมูลยอดขายรายเดือน
        $monthlySales = [];
        foreach ($orderData as $sale) {
            $monthNumber = $monthMap[$sale->od_month]; // แปลงชื่อเดือนเป็นหมายเลขเดือน
            $monthlySales[$monthNumber] = $sale->total_sales;
        }

        // กรณีที่บางเดือนไม่มีข้อมูล ยอดขายจะเป็น 0
        $monthlySales = array_replace(array_flip(range(1, 12)), $monthlySales);

        // ชื่อเดือน
        $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        return view('homePage', compact('topBranch', 'topUsers', 'totalSales', 'averageSales', 'growthPercentage', 'labels', 'monthlySales'));
    }
}
