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

        $medain = collect($orders)
            ->groupBy('od_month')
            ->map(function ($ordersInMonth) {
                return $ordersInMonth->pluck('od_amount')->median();
            });

        $medain = collect($thaiMonths)->mapWithKeys(function ($month) use ($medain) {
            return [$month => $medain->get($month, 0)];
        });

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
        $branchesQuery = Branch::withoutTrashed() //withTrashed() โหลดข้อมูลแม้แต่สาขาที่ถูก soft delete
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
        $supervisor = Auth::user();

        // ปีที่เลือก หรือปีปัจจุบัน (พ.ศ.)
        $year = $request->input('year', now()->year + 543);

        // แทนที่จะดึงลูกทีม ดึงข้อมูลของตัวเอง (Supervisor)
        $salesId = $supervisor->us_id;

        // จำนวนพนักงาน (ในที่นี้คือ 1 เพราะเป็นตัวเอง)
        $employeeCount = 1;

        // สาขาที่ supervisor ดูแลโดยตรง
        $branchIds = Branch::where('br_us_id', $salesId)->pluck('br_id');
        $branchCount = $branchIds->count();

        // ยอดขายรวมของปีนั้น ๆ (เฉพาะสาขาที่ supervisor ดูแลโดยตรง)
        $totalSales = Order::whereIn('od_br_id', $branchIds)
            ->where('od_year', $year)
            ->sum('od_amount');

        // ปีที่แล้ว
        $lastYear = $year - 1;

        // ดึงจำนวนสาขาของปีที่แล้ว (ที่ supervisor ดูแลโดยตรง)
        $lastYearBranchIds = Branch::where('br_us_id', $salesId)->pluck('br_id');
        $branchCountLastYear = $lastYearBranchIds->count();

        // คำนวณเปอร์เซ็นต์การเพิ่มขึ้นของสาขา
        $branchGrowthPercent = 0;
        if ($branchCountLastYear > 0) {
            $branchGrowthPercent = number_format(
                (($branchCount - $branchCountLastYear) / $branchCountLastYear) * 100,
                2
            );
        }

        // ดึงออเดอร์ทั้งหมดของปีนั้น ๆ (เฉพาะสาขาที่ supervisor ดูแลโดยตรง)
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

        // ค่ามัธยฐานของยอดขายในแต่ละเดือน
        $medain = collect($orders)
            ->groupBy('od_month')
            ->map(function ($ordersInMonth) {
                return $ordersInMonth->pluck('od_amount')->median();
            });

        $medain = collect($thaiMonths)->mapWithKeys(function ($month) use ($medain) {
            return [$month => $medain->get($month, 0)];
        });

        // ยอดขายแต่ละสาขา
        $branchSales = [];
        foreach ($branchIds as $index => $branchId) {
            $branchSales[] = [
                'branch_name' => Branch::find($branchId)->br_name,
                'sales' => $orders->where('od_br_id', $branchId)->sum('od_amount'),
                'growth' => $this->calculateGrowth($branchId, $orders, $year)
            ];
        }

        // ส่งข้อมูลไปยัง view
        return view('HomereportSalesSupervisor', compact(
            'totalSales',
            'year',
            'employeeCount',
            'branchCount',
            'branchGrowthPercent',
            'quarterSales',
            'completeOrderData',
            'thaiMonths',
            'medain',
            'branchSales'
        ));
    }

    /**
     * คำนวณเปอร์เซ็นต์การเติบโตของยอดขายของแต่ละสาขา
     */
    private function calculateGrowth($branchId, $orders, $year)
    {
        // คำนวณยอดขายในปีนี้
        $thisYearSales = $orders->where('od_br_id', $branchId)
            ->where('od_year', $year)
            ->sum('od_amount');

        // คำนวณยอดขายในปีที่แล้ว
        $lastYearSales = $orders->where('od_br_id', $branchId)
            ->where('od_year', $year - 1)
            ->sum('od_amount');

        // คำนวณเปอร์เซ็นต์การเติบโต
        if ($lastYearSales > 0) {
            return number_format((($thisYearSales - $lastYearSales) / $lastYearSales) * 100, 2);
        }

        return 0;
    }
}
