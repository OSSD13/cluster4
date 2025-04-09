<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        // รับค่าจากผู้ใช้ใช้งาน

        $sort = $request->get('sort', 'desc'); // sort เรียงยอดขายจากมากไปน้อยหรือน้อยไปมาก โดยที่ค่าเริ่มต้นคือมากไปน้อย
        $search = $request->input('search'); // ค้นหาจากที่ผู้ใช้งานกรอก
        $province = $request->get('province'); // จังหวัดที่ใช้กรอก
        $perPage = 5; // ต้องการโชว์แค่ 5 สาขาต่อหน้า
        $page = $request->input('page', 1); // หน้าปัจจุบันที่แสดงผล

        // ตรวจสอบว่ามีการระบุรหัสสาขา
        if ($request->has('br_id')) {
            $branch = Branch::with('branch', 'manager')->where('br_id', $request->get('br_id'))->first();

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

        return view('branchMyMap', compact('paginatedBranches', 'sort', 'province', 'totalPages', 'page', 'search'));
    }

    private array $thaiMonths = [
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

    private array $monthMap = [
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
        'ธันวาคม' => 12,
    ];



    public function showSupervisor($id)
    {
        $user = User::with('head')->find($id);
        return view('branchMyMap', ['branch' => $user]);
    }

    //@author : Aninthita Prasoetsang 66160381
    public function branch_detail($br_id)
    {
        $thaiYear = Carbon::now()->year + 543;
        $branch = Branch::findOrFail($br_id);
        $user = User::findOrFail($branch->br_us_id);


        $monthlyOrders = $this->getMonthlyOrder($br_id, $thaiYear);
        $orderData = $this->formatOrderData($monthlyOrders);
        $median = $this->monthlyMedianOrder($thaiYear);

        //หายอดรวมทั้งปีของสาขานี้
        $totalSales = $this->totalSales($br_id, $thaiYear);
        return view('branchDetail', [
            'branch'     => $branch,
            'user'       => $user,
            'orderData'  => $orderData,
            'month'      => $this->thaiMonths,
            'monthMap'   => $this->monthMap,
            'thisyear'   => $thaiYear,
            'median'     => $median,
            'totalSales' => $totalSales,
        ]);
    }


    private function getMonthlyOrder($br_id, $thisYear)
    {
        return DB::table('order as o')
            ->join('branch as b', 'o.od_br_id', '=', 'b.br_id')
            ->join('users as u', 'b.br_us_id', '=', 'u.us_id')
            ->where('o.od_year', $thisYear)
            ->where('o.od_br_id', $br_id)
            ->whereIn('o.od_month', $this->thaiMonths)
            ->whereIn('o.od_id', function ($query) use ($thisYear, $br_id) {
                $query->selectRaw('MAX(od_id) as od_id')
                    ->from('order')
                    ->where('od_year', $thisYear)
                    ->where('od_br_id', $br_id)
                    ->whereIn('od_month', $this->thaiMonths)
                    ->groupBy('od_month');
            })
            ->select(
                'o.od_month',
                'o.od_id',
                'o.od_amount',
                'b.br_id',
                'b.br_code',
                'u.us_fname',
                'u.us_image'
            )
            ->orderByRaw("FIELD(o.od_month, '" . implode("','", $this->thaiMonths) . "')")
            ->get();
    }

    private function formatOrderData($monthlyOrders): array
    {
        $data = [];


        foreach (range(1, 12) as $month) {
            $data[$month] = 0;
        }


        foreach ($monthlyOrders as $order) {
            $month = $this->monthMap[$order->od_month] ?? null;
            if ($month) {
                $data[$month] = $order->od_amount;
            }
        }

        return $data;
    }


    private function monthlyMedianOrder($thaiYear)
    {
        $monthsStr = implode("','", $this->thaiMonths);
        $orders = DB::table(DB::raw("
        (
            SELECT *, ROW_NUMBER() OVER (
                PARTITION BY od_month, od_br_id
                ORDER BY od_id DESC
            ) as rn
            FROM `order`
            WHERE od_year = $thaiYear
            AND od_month IN ('$monthsStr')
        ) as ranked
        "))
            ->where('rn', 1)
            ->select('od_month', 'od_amount', 'od_br_id', 'od_id')
            ->get();

        // จัดกลุ่มยอดขายตามเดือน
        $monthlyData = [];
        foreach ($orders as $order) {
            $month = $order->od_month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [];
            }
            $monthlyData[$month][] = $order->od_amount;
        }

        $monthlyMedian = [];

        // คำนวณมัธยฐาน
        foreach ($this->thaiMonths as $month) {
            if (isset($monthlyData[$month])) {
                $amounts = $monthlyData[$month];
                sort($amounts);

                $count = count($amounts);
                $middle = floor($count / 2);

                if ($count % 2) {
                    // ถ้าจำนวนข้อมูลเป็นเลขคี่
                    $monthlyMedian[$month] = $amounts[$middle];
                } else {
                    // ถ้าจำนวนข้อมูลเป็นเลขคู่
                    $monthlyMedian[$month] = ($amounts[$middle - 1] + $amounts[$middle]) / 2;
                }
            } else {
                // ถ้าไม่มีข้อมูลยอดขายในเดือนนั้น
                $monthlyMedian[$month] = 0;
            }
        }

        return $monthlyMedian;
    }


    private function totalSales($br_id, $thaiYear)
    {
        $totalSales = DB::table('order')
            ->where('od_br_id', $br_id)
            ->where('od_year', $thaiYear)
            ->whereNull('deleted_at')
            ->select('od_month', DB::raw('MAX(od_id) as max_od_id'))
            ->groupBy('od_month')
            ->get();

        $totalSalesAmount = DB::table('order')
            ->where('od_br_id', $br_id)
            ->where('od_year', $thaiYear)
            ->whereNull('deleted_at')
            ->whereIn('od_id', $totalSales->pluck('max_od_id'))
            ->sum('od_amount');

        return $totalSalesAmount;
    }
}
