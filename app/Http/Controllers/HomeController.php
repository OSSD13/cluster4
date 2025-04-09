<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function index()
    {
        $userRole = Auth::user()->us_role;

        if (Auth::check() && Auth::user()->us_role === 'CEO') {

            //Show top 5 branch
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


            //Show top 5 employee
            $topUsers = User::withCount([
                'branch' => function ($query) use ($currentMonth, $currentYear) {
                    $query->whereMonth('created_at', $currentMonth)  // กรองเฉพาะเดือนนี้
                        ->whereYear('created_at', $currentYear - 543);  // กรองเฉพาะปีนี้
                }
            ])
                ->whereNull('deleted_at')  // กรองเฉพาะผู้ใช้ที่ไม่ถูกลบ
                ->having('branch_count', '>', 0)
                ->orderByDesc('branch_count')  // เรียงลำดับตามจำนวนสาขาที่เพิ่มขึ้น
                ->take(5)  // ดึงแค่ 5 คนที่เพิ่มสาขามากที่สุด
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
            // $totalSales = Order::where('od_year', $thisYear)->sum('od_amount');
            $totalSales = DB::table(DB::raw("
            (
                SELECT *, ROW_NUMBER() OVER (
                    PARTITION BY od_month, od_br_id
                    ORDER BY od_id DESC
                ) as rn
                FROM `order`
                WHERE od_year = $thisYear
            ) as ranked
        "))
                ->where('rn', 1)
                ->sum('od_amount');

            // ยอดขายปีก่อนหน้า
            $previousYearSales = Order::where('od_year', $thisYear - 1)->sum('od_amount');

            //หา % การเติบโต (ยอดขายปีปัจจุบัน - ยอดขายปีก่อน / ยอดขายปีก่อน )* 100
            $growthPercentage = $previousYearSales > 0 ? (($totalSales - $previousYearSales) / $previousYearSales) * 100 : 0;

            //ค่าเฉลี่ย
            $averageSales = Order::avg('od_amount');

            if ($previousYearSales != 0) {
                $change = $totalSales - $previousYearSales;
                $absPercent = number_format((abs($change) / abs($previousYearSales)) * 100, 2);

                if ($change > 0) {
                    $percent = $absPercent;
                } elseif ($change < 0) {
                    $percent = $absPercent;
                } else {
                    $percent = 0;
                }
            } else {
                $percent = 100;
            }

            $months = $monthMap;


            $monthsStr = implode("','", $months);

            $orders = DB::table(DB::raw("
            (
                SELECT *, ROW_NUMBER() OVER (
                    PARTITION BY od_month, od_br_id
                    ORDER BY od_id DESC
                ) as rn
                FROM `order`
                WHERE od_year = $thisYear AND od_month IN ('$monthsStr')
            ) as ranked
            "))
                ->where('rn', 1)
                ->select('od_month', 'od_amount')
                ->orderByRaw("FIELD(od_month, '" . $monthsStr . "')")
                ->get();

            $monthlyData = [];

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
                    $monthlyMedian[$month] = 0;
                }
            }



            $monthlySales = [];
            foreach ($months as $month) {
                $monthlySales[$month] = 0;
            }



            // รวมยอดขายแต่ละเดือน
            foreach ($orders as $order) {
                $monthlySales[$order->od_month] += $order->od_amount;
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
                    $monthlyPlus2SD[$month] = 0; // ไม่มีข้อมูลในเดือนนั้น
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
                    $monthlyMinus2SD[$month] = 0; // ถ้าไม่มีข้อมูลเดือนนี้
                }
            }

            // ชื่อเดือน
            $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

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
            $maxSales = max($monthlySales);
            $minSales = min($monthlySales);


            // Show all employee
            $salesCount = User::where('us_role', 'Sales')->count();
            $supervisorCount = User::where('us_role', 'Sales Supervisor')->count();
            $ceoCount = User::where('us_role', 'CEO')->count();
            $totalEmployees = User::count();



            //Show all branch
            $totalBranches = Branch::whereNull('deleted_at')->count();

            $branchGrowth = Branch::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', $currentYear - 543)
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


            //กราฟสะสม
            $thaiMonthMap = [
                'มกราคม' => 'ม.ค.',
                'กุมภาพันธ์' => 'ก.พ.',
                'มีนาคม' => 'มี.ค.',
                'เมษายน' => 'เม.ย.',
                'พฤษภาคม' => 'พ.ค.',
                'มิถุนายน' => 'มิ.ย.',
                'กรกฎาคม' => 'ก.ค.',
                'สิงหาคม' => 'ส.ค.',
                'กันยายน' => 'ก.ย.',
                'ตุลาคม' => 'ต.ค.',
                'พฤศจิกายน' => 'พ.ย.',
                'ธันวาคม' => 'ธ.ค.',
            ];

            // รายชื่อเดือนแบบย่อ
            $thaiMonths = [
                1 => 'ม.ค.',
                2 => 'ก.พ.',
                3 => 'มี.ค.',
                4 => 'เม.ย.',
                5 => 'พ.ค.',
                6 => 'มิ.ย.',
                7 => 'ก.ค.',
                8 => 'ส.ค.',
                9 => 'ก.ย.',
                10 => 'ต.ค.',
                11 => 'พ.ย.',
                12 => 'ธ.ค.',
            ];


            //พนักงาน
            // คำนวณยอดพนักงานปีนี้
            $currentYearEmployeeCount = DB::table('users')
                ->whereYear('created_at', $currentYear - 543)
                ->count();

            // คำนวณยอดพนักงานใหม่รายเดือน
            $employeesByMonth = DB::table('users')
                ->select(DB::raw("MONTH(created_at) as month"), DB::raw("COUNT(*) as count"))
                ->whereYear('created_at', $currentYear - 543)
                ->whereNull('deleted_at')
                ->groupBy(DB::raw("MONTH(created_at)"))
                ->pluck('count', 'month');

            $cumulativeEmployees = [];
            $totalEmployee = 0;
            $lastMonthWithNewEmployee = 0;

            for ($i = 1; $i <= 12; $i++) {
                // จำนวนพนักงานใหม่ในแต่ละเดือน
                $count = $employeesByMonth[$i] ?? 0;

                // คำนวณจำนวนพนักงานสะสม
                $totalEmployee += $count;

                // เก็บข้อมูลจำนวนพนักงานสะสมตามเดือน
                $cumulativeEmployees[] = [
                    'month' => $thaiMonths[$i],
                    'total_employees' => $totalEmployee
                ];

                // บันทึกเดือนสุดท้ายที่มีพนักงานใหม่
                if ($count > 0) {
                    $lastMonthWithNewEmployee = $i;
                }
            }



            // ยอดสาขาปีนี้
            $currentYearBranches = DB::table('branch')
                ->whereYear('created_at', $currentYear - 543)
                ->count();

            // คำนวณยอดสะสมสาขารายเดือน
            $branchesByMonth = DB::table('branch')
                ->select(DB::raw("MONTH(created_at) as month"), DB::raw("COUNT(*) as count"))
                ->whereYear('created_at', $currentYear - 543)
                ->whereNull('deleted_at')
                ->groupBy(DB::raw("MONTH(created_at)"))
                ->pluck('count', 'month');

            $cumulativeBranches = [];
            $totalBranch = 0;
            $lastMonthWithNewBranch = 0;

            for ($i = 1; $i <= 12; $i++) {
                $count = $branchesByMonth[$i] ?? 0;
                $totalBranch += $count;
                $cumulativeBranches[] = [
                    'month' => $thaiMonths[$i],
                    'total_branches' => $totalBranch
                ];
                if ($count > 0) {
                    $lastMonthWithNewBranch = $i;
                }
            }

            return view(
                'homePage',
                compact(
                    'currentYear',
                    'userRole',
                    'topBranch',
                    'topUsers',
                    'salesCount',
                    'supervisorCount',
                    'ceoCount',
                    'totalEmployees',
                    'totalBranches',
                    'growthRates',
                    'growthPercentage',
                    'totalSales',
                    'averageSales',
                    'growthPercentage',
                    'labels',
                    'monthlySales',
                    'maxSales',
                    'minSales',
                    'monthlyData',
                    'monthlyMedian',
                    'monthlyPlus2SD',
                    'monthlyMinus2SD',
                    'maxY',
                    'percent'
                ),
                [
                    'currentYearBranches' => $currentYearBranches,
                    'cumulativeBranches' => $cumulativeBranches,
                    'lastMonthWithNewBranch' => $lastMonthWithNewBranch,
                    'currentYearEmployeeCount' => $currentYearEmployeeCount,
                    'cumulativeEmployees' => $cumulativeEmployees,
                    'lastMonthWithNewEmployee' => $lastMonthWithNewEmployee
                ]
            );
        } else if (Auth::check() && Auth::user()->us_role === 'Sales Supervisor') {

            $currentUserId = Auth::user()->us_id;

            // ดึงรายชื่อ user ที่มี us_head = คนที่ login
            $userIdsOrder = User::where('us_head', $currentUserId)
                ->pluck('us_id');

            $branchIds = Branch::whereIn('br_us_id', $userIdsOrder)
                ->pluck('br_id');



            //Show top 5 branch
            $currentYear = Carbon::now()->year + 543;
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
                ->where('u.us_head', '=', Auth::user()->us_id)
                ->where('u.us_role', '!=', 'Sales Supervisor')
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
            $topUsers = User::where('us_head', Auth::user()->us_id)
                ->where('us_role', '!=', 'Sales Supervisor')
                ->whereHas('branch', function ($query) use ($currentYear) {
                    $query->whereYear('created_at', $currentYear - 543);
                })
                ->withCount(['branch' => function ($query) use ($currentYear) {
                    $query->whereYear('created_at', $currentYear - 543);
                }])
                ->orderByDesc('branch_count')
                ->take(5)
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
            // $totalSales = Order::where('od_year', $thisYear)
            // ->whereIn('od_br_id',$branchIds)->sum('od_amount');
            $totalSales = Order::whereIn('od_br_id', $branchIds)->sum('od_amount');

            // ยอดขายปีก่อนหน้า
            $previousYearSales = Order::where('od_year', $thisYear - 1)->sum('od_amount');

            //หา % การเติบโต (ยอดขายปีปัจจุบัน - ยอดขายปีก่อน / ยอดขายปีก่อน )* 100
            $growthPercentage = $previousYearSales > 0 ? (($totalSales - $previousYearSales) / $previousYearSales) * 100 : 0;

            //ค่าเฉลี่ย
            $averageSales = Order::avg('od_amount');

            if ($previousYearSales != 0) {
                $change = $totalSales - $previousYearSales;
                $absPercent = number_format((abs($change) / abs($previousYearSales)) * 100, 2);

                if ($change > 0) {
                    $percent = $absPercent;
                } elseif ($change < 0) {
                    $percent = $absPercent;
                } else {
                    $percent = 0;
                }
            } else {
                $percent = 100;
            }

            $months = $monthMap;

            $orders = DB::table('order')
                ->select('od_month', 'od_amount')
                ->where('od_year', $thisYear)
                ->whereIn('od_month', $months)
                ->orderByRaw("FIELD(od_month, '" . implode("','", $months) . "')")
                ->get();

            $monthlyData = [];

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
                    $monthlyMedian[$month] = 0;
                }
            }

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
                    $monthlyPlus2SD[$month] = 0; // ไม่มีข้อมูลในเดือนนั้น
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
                    $monthlyMinus2SD[$month] = 0; // ถ้าไม่มีข้อมูลเดือนนี้
                }
            }

            // ชื่อเดือน
            $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

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

            $maxSales = max($monthlySales);
            $minSales = min($monthlySales);


            // ชื่อเดือน
            $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];



            // Show all employee
            $salesCount = User::where('us_role', 'Sales')
                ->where('us_head', $currentUserId)
                ->count();

            // ดึงรายชื่อ user ที่มี us_head = คนที่ login
            $userIds = User::where('us_head', $currentUserId)
                ->where('us_role', '!=', 'Sales Supervisor')
                ->pluck('us_id');

            // คำนวณจำนวนสาขาทั้งหมดที่ยังไม่ถูกลบ และมี us_head เท่ากับ $currentUserId
            $totalBranches = Branch::whereIn('br_us_id', $userIds)
                ->whereNull('deleted_at')
                ->count();

            // คำนวณยอดการเติบโตของสาขาในแต่ละเดือน โดยมี us_head เท่ากับ $currentUserId
            $branchGrowth = Branch::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereIn('br_us_id', $userIds)
                ->whereYear('created_at', $currentYear - 543)  // กรองเฉพาะปีที่กำหนด
                ->whereNull('deleted_at')  // กรองเฉพาะสาขาที่ไม่ได้ถูกลบ
                ->groupBy(DB::raw('MONTH(created_at)'))  // แยกข้อมูลตามเดือน
                ->pluck('total', 'month');  // สร้าง array จากเดือน => จำนวนสาขา

            // กำหนด labels ของเดือนในภาษาไทย
            $labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

            // เตรียมข้อมูลการเติบโตในแต่ละเดือน
            $growthRates = [];
            for ($i = 1; $i <= 12; $i++) {
                // หากไม่มีข้อมูลในเดือนนั้นให้กำหนดเป็น 0
                $growthRates[$labels[$i - 1]] = $branchGrowth[$i] ?? 0;
            }

            // คำนวณเปอร์เซ็นต์การเติบโตโดยการหารยอดรวมการเติบโตจากสาขาทั้งหมด
            $growthPercentage = $totalBranches > 0
                ? round(array_sum($growthRates) / $totalBranches * 100, 2)  // คำนวณเปอร์เซ็นต์การเติบโต
                : 0;  // ถ้าไม่มีสาขาก็ให้เปอร์เซ็นต์เป็น 0



            //กราฟสะสม
            $thaiMonthMap = [
                'มกราคม' => 'ม.ค.',
                'กุมภาพันธ์' => 'ก.พ.',
                'มีนาคม' => 'มี.ค.',
                'เมษายน' => 'เม.ย.',
                'พฤษภาคม' => 'พ.ค.',
                'มิถุนายน' => 'มิ.ย.',
                'กรกฎาคม' => 'ก.ค.',
                'สิงหาคม' => 'ส.ค.',
                'กันยายน' => 'ก.ย.',
                'ตุลาคม' => 'ต.ค.',
                'พฤศจิกายน' => 'พ.ย.',
                'ธันวาคม' => 'ธ.ค.',
            ];

            // รายชื่อเดือนแบบย่อ
            $thaiMonths = [
                1 => 'ม.ค.',
                2 => 'ก.พ.',
                3 => 'มี.ค.',
                4 => 'เม.ย.',
                5 => 'พ.ค.',
                6 => 'มิ.ย.',
                7 => 'ก.ค.',
                8 => 'ส.ค.',
                9 => 'ก.ย.',
                10 => 'ต.ค.',
                11 => 'พ.ย.',
                12 => 'ธ.ค.',
            ];


            //พนักงาน
            // คำนวณยอดพนักงานปีนี้
            $currentYearEmployeeCount = DB::table('users')
                ->where('us_role', '!=', 'Sales Supervisor')
                ->where('us_head', $currentUserId)
                ->whereYear('created_at', $currentYear - 543)
                ->count();

            // คำนวณยอดพนักงานใหม่รายเดือน
            $employeesByMonth = DB::table('users')
                ->select(DB::raw("MONTH(created_at) as month"), DB::raw("COUNT(*) as count"))
                ->where('us_role', '!=', 'Sales Supervisor')
                ->where('us_head', $currentUserId)
                ->whereYear('created_at', $currentYear - 543)
                ->whereNull('deleted_at')
                ->groupBy(DB::raw("MONTH(created_at)"))
                ->pluck('count', 'month');

            $cumulativeEmployees = [];
            $totalEmployee = 0;
            $lastMonthWithNewEmployee = 0;

            for ($i = 1; $i <= 12; $i++) {
                // จำนวนพนักงานใหม่ในแต่ละเดือน
                $count = $employeesByMonth[$i] ?? 0;

                // คำนวณจำนวนพนักงานสะสม
                $totalEmployee += $count;

                // เก็บข้อมูลจำนวนพนักงานสะสมตามเดือน
                $cumulativeEmployees[] = [
                    'month' => $thaiMonths[$i],
                    'total_employees' => $totalEmployee
                ];

                // บันทึกเดือนสุดท้ายที่มีพนักงานใหม่
                if ($count > 0) {
                    $lastMonthWithNewEmployee = $i;
                }
            }



            // ยอดสาขาปีนี้
            $currentYearBranches = DB::table('branch')
                ->whereIn('br_us_id', $userIds)
                ->whereYear('created_at', $currentYear - 543)
                ->count();

            // คำนวณยอดสะสมสาขารายเดือน
            $branchesByMonth = DB::table('branch')
                ->select(DB::raw("MONTH(created_at) as month"), DB::raw("COUNT(*) as count"))
                ->whereIn('br_us_id', $userIds)
                ->whereYear('created_at', $currentYear - 543)
                ->whereNull('deleted_at')
                ->groupBy(DB::raw("MONTH(created_at)"))
                ->pluck('count', 'month');

            $cumulativeBranches = [];
            $totalBranch = 0;
            $lastMonthWithNewBranch = 0;

            for ($i = 1; $i <= 12; $i++) {
                $count = $branchesByMonth[$i] ?? 0;
                $totalBranch += $count;
                $cumulativeBranches[] = [
                    'month' => $thaiMonths[$i],
                    'total_branches' => $totalBranch
                ];
                if ($count > 0) {
                    $lastMonthWithNewBranch = $i;
                }
            }

            return view(
                'homePage',
                compact(
                    'currentYear',
                    'userRole',
                    'topBranch',
                    'topUsers',
                    'salesCount',
                    'totalBranches',
                    'growthRates',
                    'growthPercentage',
                    'totalSales',
                    'averageSales',
                    'growthPercentage',
                    'labels',
                    'monthlySales',
                    'maxSales',
                    'minSales',
                    'monthlyData',
                    'monthlyMedian',
                    'monthlyPlus2SD',
                    'monthlyMinus2SD',
                    'maxY',
                    'percent'
                ),
                [
                    'currentYearBranches' => $currentYearBranches,
                    'cumulativeBranches' => $cumulativeBranches,
                    'lastMonthWithNewBranch' => $lastMonthWithNewBranch,
                    'currentYearEmployeeCount' => $currentYearEmployeeCount,
                    'cumulativeEmployees' => $cumulativeEmployees,
                    'lastMonthWithNewEmployee' => $lastMonthWithNewEmployee
                ]
            );
        }
    }
}
