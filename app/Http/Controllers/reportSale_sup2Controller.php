<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Branch;
use App\Models\Order;

class reportSale_sup2Controller extends Controller
{
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
        return view('reportSale_sup2', compact(
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
}
