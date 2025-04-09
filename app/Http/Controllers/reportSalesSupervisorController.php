<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReportSalesSupervisorController extends Controller
{

    //By ริว
    public function index(Request $request)
    {
        $supervisor = Auth::user();

        // ปีที่เลือก หรือปีปัจจุบัน (พ.ศ.)
        $year = $request->input('year', now()->year + 543);

        // ลูกทีมของหัวหน้า
        $salesIds = User::where('us_head', $supervisor->us_id)
            ->where('us_role', 'Sales')
            ->pluck('us_id');

        // จำนวนลูกทีมที่มี
        $employeeCount = $salesIds->count();

        // สาขาที่ลูกทีมดูแล
        $branchIds = Branch::whereIn('br_us_id', $salesIds)->pluck('br_id');
        $branchCount = $branchIds->count();

        // ยอดขายรวมของปีนั้น ๆ
        $totalSales = Order::whereIn('od_br_id', $branchIds)
            ->where('od_year', $year)
            ->sum('od_amount');

        // ปีที่แล้ว
        $lastYear = $year - 1;

        // ดึงจำนวนสาขาของปีที่แล้ว
        $lastYearBranchIds = Branch::whereIn('br_us_id', $salesIds)->pluck('br_id');
        $branchCountLastYear = $lastYearBranchIds->count();

        // คำนวณเปอร์เซ็นต์การเพิ่มขึ้นของสาขา
        $branchGrowthPercent = 0;
        if ($branchCountLastYear > 0) {
            $branchGrowthPercent = number_format(
                (($branchCount - $branchCountLastYear) / $branchCountLastYear) * 100,
                2
            );
        }

        // ดึงออเดอร์ทั้งหมดของปีนั้น ๆ
        $orders = Order::whereIn('od_br_id', $branchIds)
            ->where('od_year', $year)
            ->get();

        $thaiMonths = [
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
        $monthMap = array_flip($thaiMonths);

        // ข้อมูลยอดขายรายไตรมาส
        $quarterSales = [
            'Q1' => 0,
            'Q2' => 0,
            'Q3' => 0,
            'Q4' => 0
        ];

        // ข้อมูลยอดขายรายเดือน
        $orderData = collect($orders)
            ->groupBy('od_month')
            ->map(function ($orders) {
                return $orders->sum('od_amount');
            });

        $completeOrderData = collect($thaiMonths)->mapWithKeys(function ($month) use ($orderData) {
            return [$month => $orderData->get($month, 0)];
        });

        // ค่ามัธยฐานยอดขายรายเดือน
        $medain = array_fill(0, 12, $completeOrderData->median());

        foreach ($orders as $order) {
            $monthIndex = ($monthMap[$order->od_month] ?? 0) + 1;

            if ($monthIndex >= 1 && $monthIndex <= 3) {
                $quarterSales['Q1'] += $order->od_amount;
            } elseif ($monthIndex >= 4 && $monthIndex <= 6) {
                $quarterSales['Q2'] += $order->od_amount;
            } elseif ($monthIndex >= 7 && $monthIndex <= 9) {
                $quarterSales['Q3'] += $order->od_amount;
            } elseif ($monthIndex >= 10 && $monthIndex <= 12) {
                $quarterSales['Q4'] += $order->od_amount;
            }
        }

        // ส่งข้อมูลไปยัง view
        return view('homeReportSalesSupervisorTeam', compact(
            'totalSales',
            'year',
            'employeeCount',
            'branchCount',
            'branchGrowthPercent',
            'quarterSales',
            'completeOrderData',
            'thaiMonths',
            'medain'
        ));
    }

    // By หญฺิง
    public function sales_supervisor(Request $request)
    {
        // รับค่าจากผู้ใช้งาน

        $sort = $request->get('sort', 'desc'); // sort เรียงยอดขายจากมากไปน้อยหรือน้อยไปมาก โดยที่ค่าเริ่มต้นคือมากไปน้อย
        $search = $request->input('search'); // ค้นหาจากที่ผู้ใช้งานกรอก
        $province = $request->get('province'); // จังหวัดที่ใช้กรอก
        $perPage = 5; // ต้องการโชว์แค่ 5 สาขาต่อหน้า
        $page = $request->input('page', 1); // หน้าปัจจุบันที่แสดงผล

        // ดึง id ของผู้ใช้งานที่เข้าถึง
        $currentUserId = auth::user()->us_id;

        // ตรวจสอบว่ามีการระบุรหัสสาขา
        if ($request->has('br_id')) {
            $branch = Branch::with('branch', 'manager')
                ->where('br_id', $request->get('br_id'))
                ->where('br_us_id', $currentUserId) // เพิ่มเงื่อนไขให้เป็นสาขาที่ผู้ใช้ปัจจุบันเป็นคนสร้าง
                ->first();

            if (!$branch) {
                return redirect()->back()->with('error', 'ไม่พบข้อมูลสาขานี้');
            }
        }

        // ดึงข้อมูลสาขา
        $branchesQuery = Branch::withTrashed() //withTrashed() โหลดข้อมูลแม้แต่สาขาที่ถูก soft delete
            ->with([
                'manager',
                'order',
                'image' => fn($query) => $query->latest()->limit(1)
            ])
            ->where('br_us_id', $currentUserId)
            // สามารค้นหาจากรหัสสาขา ชื่อสาขา หรือจังหวัด
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('br_code', 'LIKE', "{$search}%")
                        ->orWhere('br_name', 'LIKE', "{$search}%")
                        ->orWhere('br_province', 'LIKE', "{$search}%")
                        ->orWhereHas('manager', function ($mq) use ($search) {
                            $mq->where('us_fname', 'LIKE', "{$search}%")
                                ->orWhere('us_lname', 'LIKE', "{$search}%")
                                ->orWhereRaw("CONCAT(us_fname, ' ', us_lname) LIKE ?", ["{$search}%"]);
                        });
                });
            })
            //ถ้าผู้ใช้งานเลือกจังหวัดที่ต้องการดู ระบบจะแสดงแค่จังหวัดนั้น
            ->when($province, fn($query) => $query->where('br_province', $province));

        // คำนวณยอดขายและดึงรูปล่าสุดของผู้ใช้งาน
        $branches = $branchesQuery->get()->map(function ($branch) {
            $branch->total_sales = $branch->order()
                ->whereYear('created_at', now()->year)
                ->sum('od_amount');

            $branch->latest_image = $branch->image->first();
            return $branch;
        });

        // เรียงลำดับยอดขาย
        $branches = $sort === 'asc'
            ? $branches->sortBy('total_sales')->values()
            : $branches->sortByDesc('total_sales')->values();

        // นับจำนวนสาขาทั้งหมดจากการค้นหาหรือตัวกรองที่ผู้ใช้งานเลือก
        $totalBranches = $branches->count();
        $totalPages = ceil($totalBranches / $perPage);

        // ตรวจสอบความถูกต้องของหน้า
        if ($page < 1) {
            $page = 1;
        } else if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        // แสดงเฉพาะข้อมูลในหน้าปัจจุบัน
        $offset = ($page - 1) * $perPage;
        $paginatedBranches = $branches->slice($offset, $perPage);

        // กำหนดลำดับเลขหน้าของแต่ละสาขา
        foreach ($paginatedBranches as $index => $branch) {
            $branch->branch_number = $offset + $index + 1;
        }

        return view('reportSalesSupervisor', compact('paginatedBranches', 'sort', 'province', 'totalPages', 'page', 'search'));
    }


    //By เวฟ
    public function reportSalesSupervisor1(Request $request)
    {
        $user = Auth::user();  // ดึงข้อมูลของผู้ใช้ที่ล็อกอิน
        $selectedSupYear = (int) $request->input('year', Carbon::now()->year + 543);

        // คิวรีสำหรับยอดขายรายเดือนตามผู้ดูแล (supervisor)
        $query = DB::table('order')
            ->join('branch', 'order.od_br_id', '=', 'branch.br_id')
            ->join('users', 'order.od_us_id', '=', 'users.us_id')
            ->select('order.od_month', 'branch.br_province', 'users.us_fname', DB::raw('SUM(order.od_amount) as total_sales'))
            ->where('order.od_year', $selectedSupYear)
            ->where('order.od_us_id', $user->us_id)
            ->groupBy('order.od_month', 'branch.br_province', 'users.us_fname')
            ->get();

        // คิวรีสำหรับดึงปีทั้งหมดที่มีในตาราง order
        $allYears = DB::table('order')
            ->select('od_year')
            ->distinct()
            ->orderBy('od_year', 'desc')
            ->pluck('od_year');

        // คำนวณยอดขายรวมทั้งหมดในปีที่เลือก
        $totalSalesSup = DB::table('order')
            ->where('order.od_year', $selectedSupYear)
            ->where('order.od_us_id', $user->us_id)  // แก้ไขจาก $user->id เป็น $user->us_id
            ->sum('order.od_amount');  // ใช้ SUM() เพื่อคำนวณยอดขายทั้งหมดในปีที่เลือก

        // คำนวณยอดขายปีที่ผ่านมา
        $previousYear = $selectedSupYear - 1;  // ปีที่ผ่านมา
        $previousYearSales = DB::table('order')
            ->where('order.od_year', $previousYear)
            ->where('order.od_us_id', $user->us_id)  // แก้ไขจาก $user->id เป็น $user->us_id
            ->sum('order.od_amount');  // ใช้ SUM() เพื่อคำนวณยอดขายปีที่ผ่านมา

        // คำนวณเปอร์เซ็นต์การเติบโต
        if ($previousYearSales > 0) {
            $growthPercentage = (($totalSalesSup - $previousYearSales) / $previousYearSales) * 100;
        } else {
            $growthPercentage = $totalSalesSup > 0 ? 100 : 0; // ถ้ายอดขายปีที่ผ่านมาเป็น 0
        }

        // คำนวณค่าเฉลี่ยยอดขายรายเดือนในปีที่เลือก
        $totalMonths = DB::table('order')
            ->where('order.od_year', $selectedSupYear)
            ->where('order.od_us_id', $user->us_id)  // แก้ไขจาก $user->id เป็น $user->us_id
            ->distinct()
            ->count('od_month'); // จำนวนเดือนที่มีการขายในปีที่เลือก

        $averageSales = $totalMonths > 0 ? $totalSalesSup / $totalMonths : 0; // ค่าเฉลี่ยยอดขายรายเดือน

        // แผนที่เดือนในภาษาไทย
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
            'ธันวาคม',
        ];

        $orders = DB::table('order')
            ->select('od_month', 'od_amount')
            ->where('od_year', $selectedSupYear)
            ->where('od_us_id', $user->us_id) // กรองตามผู้ใช้งานที่ล็อกอิน
            ->whereIn('od_month', $monthMap)
            ->orderByRaw("FIELD(od_month, '" . implode("','", $monthMap) . "')")
            ->get();

        // จัดกลุ่มข้อมูลตามเดือน
        $monthlyData = [];
        foreach ($orders as $order) {
            $month = $order->od_month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [];
            }
            $monthlyData[$month][] = $order->od_amount;
        }


        // คำนวณยอดขายรวมในแต่ละเดือน
        $monthlyTotal = [];
        foreach ($monthMap as $month) {
            if (!empty($monthlyData[$month])) {
                $amounts = $monthlyData[$month];
                $total = array_sum($amounts);
                $monthlyTotal[$month] = $total;
            } else {
                $monthlyTotal[$month] = 0;
            }
        }



        // คำนวณค่าเฉลี่ย (Median) ของยอดขายในแต่ละเดือน
        $monthlyMedian = [];
        foreach ($monthMap as $month) {
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
        // dd($monthlyTotal);

        $currentUserId = Auth::user()->us_id;

        // ดึงรายชื่อ user ที่มี us_head = คนที่ login

        $branchIds = Branch::where('br_us_id', $currentUserId)
            ->whereNull('deleted_at')
            ->pluck('br_id');

        $branchCount = DB::table('branch')
            ->whereIn('br_id', $branchIds)
            ->whereYear('created_at', $selectedSupYear - 543)
            ->count('br_id');

        $branchCountPrevion = DB::table('branch')
            ->whereIn('br_id', $branchIds)
            ->whereYear('created_at', $selectedSupYear - 1 - 543)
            ->count('br_id');


        $branchPercen = 0;
        if ($branchCountPrevion > 0) {
            $branchPercen = (($branchCount - $branchCountPrevion) / $branchCountPrevion) * 100;
        }

        $branchesRank = DB::table('branch')
            ->join('order', 'branch.br_id', '=', 'order.od_br_id')
            ->select('branch.br_id', 'branch.br_name', DB::raw('SUM(order.od_amount) as total_sales'))
            ->where('order.od_year', $selectedSupYear)  // กรองปีที่เลือก
            ->where('order.od_us_id', $user->us_id)  // กรองตามผู้ใช้งานที่ล็อกอิน
            ->groupBy('branch.br_id', 'branch.br_name')
            ->orderByDesc('total_sales')  // จัดอันดับยอดขายจากมากไปน้อย
            ->get();


        foreach ($branchesRank as $branch) {
            if (isset($previousYearSales[$branch->br_id])) {
                $previousSales = $previousYearSales[$branch->br_id]->total_sales;
                if ($previousSales > 0) {
                    $branch->growth_percentage = (($branch->total_sales - $previousSales) / $previousSales) * 100;
                } else {
                    $branch->growth_percentage = $branch->total_sales > 0 ? 100 : 0;
                }
            } else {
                $branch->growth_percentage = 0;
            }
        }


        // ส่งข้อมูลไปยัง view
        return view('homeReportSalesSupervisor', [
            'selectedSupYear' => $selectedSupYear,
            'allYears' => $allYears,
            'query' => $query,  // ส่งผลลัพธ์ของยอดขายรายเดือนตามผู้ดูแล
            'totalSalesSup' => $totalSalesSup, // ส่งยอดขายรวมทั้งหมดในปีที่เลือก
            'growthPercentage' => $growthPercentage, // ส่งเปอร์เซ็นต์การเติบโต
            'averageSales' => $averageSales, // ส่งค่าเฉลี่ยยอดขายรายเดือน
            'monthlyTotal' => $monthlyTotal, // ส่งยอดขายรวมรายเดือน
            'monthlyMedian' => $monthlyMedian, // ส่งค่าเฉลี่ย (Median) ของยอดขายรายเดือน
            'branchCount' => $branchCount,
            'branchPercen' => $branchPercen,
            'branchesRank' => $branchesRank,
            'monthMap' => $monthMap,

        ]);
    }
}
