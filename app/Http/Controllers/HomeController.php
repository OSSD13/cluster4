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


        //@auther : ryu
        // หาพนักงานที่เพิ่มสาขามากที่สุด
        $topUsers = User::withCount('branch')  // ดึงจำนวนสาขา
            ->orderByDesc('branch_count')  // เรียงลำดับตามจำนวนสาขา
            ->take(5)  // ดึง 5 คน
            ->get();


        //@auther : boom
        $monthMap = [
            'มกราคม',
            'กุมภาพันธ์',
            'มีนาคม',
            'เมษายน',
            'พฤษภาคม',
            'มิถุนายน',
            'กรกฎาคม',
            'สิงหาคม',
            'กันยายน',
            'ตุลาคม',
            'พฤศจิกายน',
            'ธันวาคม'
        ];


        $thisYear = Carbon::now()->year + 543;
        // ยอดขายทั้งหมดปีนี้
        $totalSales = Order::where('od_year', $thisYear)->sum('od_amount');

        // ยอดขายปีก่อนหน้า
        $previousYearSales = Order::where('od_year', $thisYear - 1)->sum('od_amount');

        //หา % การเติบโต (ยอดขายปีปัจจุบัน - ยอดขายปีก่อน / ยอดขายปีก่อน )* 100
        $growthPercentage = $previousYearSales > 0 ? (($totalSales - $previousYearSales) / $previousYearSales) * 100 : 0;

        //ค่าเฉลี่ย
        $averageSales = Order::avg('od_amount');




        $months = $monthMap;

        $orders = DB::table('order')
            ->select('od_month', 'od_amount')
            ->where('od_year', $thisYear)
            ->whereIn('od_month', $months)
            ->orderByRaw("FIELD(od_month, '" . implode("','", $months) . "')")
            ->get();

        $monthlyData = [];

        // จัดกลุ่มข้อมูลตามเดือน
        // จัดกลุ่มข้อมูลตามเดือน
        foreach ($orders as $order) {
            $month = $order->od_month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [];
            }
            $monthlyData[$month][] = $order->od_amount;
        }

        // คำนวณค่ามัธยฐานของแต่ละเดือน
        $monthlyMedian = [];

        foreach ($months as $month) {
            if (!empty($monthlyData[$month])) {
                $amounts = $monthlyData[$month];
                sort($amounts);
                $count = count($amounts);
                $middle = floor($count / 2);

                if ($count % 2) {
                    $median = $amounts[$middle];
                } else {
                    $median = ($amounts[$middle - 1] + $amounts[$middle]) / 2;
                }

                $monthlyMedian[$month] = $median;
            } else {
                $monthlyMedian[$month] = 1;
            }
        }












        // $thisYear = Carbon::now()->year + 543;
        // //ดึงข้อมูลยอดขายรายเดือน
        // $salesData = Order::where('od_year', $thisYear) // ปีล่าสุด
        //     ->selectRaw('od_month, SUM(od_amount) as total_sales')
        //     ->groupBy('od_month')
        //     ->orderByRaw("FIELD(od_month, 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม')")
        //     ->get();

        $salesData = Order::where('od_year', $thisYear)
            ->selectRaw('od_month, SUM(od_amount) as total_sales')
            ->groupBy('od_month')
            ->orderByRaw("FIELD(od_month, '" . implode("','", $months) . "')")
            ->get()
            ->keyBy('od_month'); // แปลงให้เข้าถึงตามชื่อเดือน

        $monthlySales = [];
        foreach ($months as $month) {
            $monthlySales[$month] = isset($salesData[$month]) ? $salesData[$month]->total_sales : 0;
        }

        // คำนวณ median + 2SD สำหรับแต่ละเดือน
        $monthlyPlus2SD = [];

        foreach ($months as $month) {
            $amounts = $monthlyData[$month] ?? [];

            if (count($amounts) > 0) {
                sort($amounts);
                $count = count($amounts);
                $middle = floor($count / 2);

                // มัธยฐาน
                $median = ($count % 2)
                    ? $amounts[$middle]
                    : ($amounts[$middle - 1] + $amounts[$middle]) / 2;

                // ค่าเฉลี่ย
                $mean = array_sum($amounts) / $count;

                // SD = sqrt(sum((x - mean)^2) / n)
                $variance = array_reduce($amounts, function ($carry, $item) use ($mean) {
                    return $carry + pow($item - $mean, 2);
                }, 0) / $count;

                $sd = sqrt($variance);

                // ผลลัพธ์: median + 2*SD
                $monthlyPlus2SD[$month] = $median + (2 * $sd);
            } else {
                $monthlyPlus2SD[$month] = 1; // ไม่มีข้อมูลในเดือนนั้น
            }
        }


        // คำนวณ median - 2SD สำหรับแต่ละเดือน
        $monthlyMinus2SD = [];

        foreach ($months as $month) {
            if (isset($monthlyData[$month]) && count($monthlyData[$month]) > 0) {
                $amounts = $monthlyData[$month];
                sort($amounts);
                $count = count($amounts);
                $middle = floor($count / 2);

                $median = $count % 2
                    ? $amounts[$middle]
                    : ($amounts[$middle - 1] + $amounts[$middle]) / 2;

                $mean = array_sum($amounts) / $count;
                $squaredDiffs = array_map(fn($x) => pow($x - $mean, 2), $amounts);
                $sd = sqrt(array_sum($squaredDiffs) / $count);

                $minus2SD = max(0, $median - 2 * $sd);
                $monthlyMinus2SD[$month] = $minus2SD;
            } else {
                $monthlyMinus2SD[$month] = 1; // ถ้าไม่มีข้อมูลเดือนนี้
            }
        }






        // dd($salesData);

        // $salesData = DB::table('order')
        // ->select('od_month', 'od_amount')
        // ->where('od_year', $thisYear)
        // ->orderByRaw("FIELD(od_month, 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        //               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม')")


        // ->get();
        // dd($salesData);


        // เตรียมข้อมูลยอดขายรายเดือน
        // $monthlySales = [];
        // foreach ($salesData as $sale) {
        //     $monthNumber = $monthMap[$sale->od_month]; // แปลงชื่อเดือนเป็นหมายเลขเดือน
        //     $monthlySales[$monthNumber] = $sale->total_sales;
        // }

        // กรณีที่บางเดือนไม่มีข้อมูล ยอดขายจะเป็น 0
        // $monthlySales = array_replace(array_flip(range(1, 12)), $monthlySales);

        // ชื่อเดือน
        $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

        // $monthlySales = array_fill(1, 12, 0);

        // foreach ($salesData as $sale) {
        //     // ตรวจสอบชื่อเดือนก่อนว่าอยู่ใน map หรือไม่
        //     if (isset($monthMap[$sale->od_month])) {
        //         $monthNumber = $monthMap[$sale->od_month];
        //         $monthlySales[$monthNumber] = $sale->total_sales;
        //     }
        // }

        foreach ($months as $month) {
            if (!isset($monthlyMedian[$month])) {
                $monthlyMedian[$month] = null; // หรือ 0, ขึ้นอยู่กับ use case
            }
        }

        $monthlySalesOnly = array_values($monthlySales);

        // Controller (คำนวณค่า max)
        $maxY = max(array_merge($monthlySalesOnly, array_values($monthlyMedian), array_values($monthlyPlus2SD)));
        // ปัดขึ้นไปใกล้ค่าที่ต้องการ
        $maxY = ceil($maxY / 10000) * 10000; // ปัดขึ้นไปเป็น 10000, 100000 หรือใกล้เคียง






        // wave
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

        $maxSales = max($monthlySales);
        $minSales = min($monthlySales);

        //$medianOrder = $this->calculateMedian($monthlySales);



        //Mork
        $currentYear = Carbon::now()->year;

        $totalBranches = Branch::whereNull('deleted_at')->count();

        $branchGrowth = Branch::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');

        $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $growthRates = [];

        for ($i = 1; $i <= 12; $i++) {
            $growthRates[$labels[$i - 1]] = $branchGrowth[$i] ?? 0;
        }

        $growthPercentage = $totalBranches > 0
            ? round(array_sum($growthRates) / $totalBranches * 100, 2)
            : 0;

        return view('homePage', compact(
            'topBranch',
            'topUsers',
            'totalSales',
            'averageSales',
            'growthPercentage',
            'labels',
            'monthlySales',
            'salesCount',
            'supervisorCount',
            'ceoCount',
            'totalEmployees',
            'growthData',
            'totalBranches',
            'growthRates',
            'growthPercentage',
            'maxSales',
            'minSales',
            'monthlyData',
            'monthlyMedian',
            'monthlyPlus2SD',
            'monthlyMinus2SD',
            'maxY'

        ));

    }


    // private function calculateMedian(array $monthlySales){ // Remove zero values if you don't want to include them
    //     sort($monthlySales);
    //     $count = count($monthlySales);

    //     if ($count === 0) {
    //         return 0;
    //     }

    //     $middle = (int) floor(($count - 1) / 2);

    //     if ($count % 2) {
    //         return $monthlySales[$middle];
    //     }

    //     return ($monthlySales[$middle] + $monthlySales[$middle + 1]) / 2;
    // }


}
