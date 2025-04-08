<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\DB;


// @author : Aninthita Prasoetsang 66160381
class OrderController extends Controller
{

    private array $thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];
  
  public function add_order(){
        return view('addOrder');
    }

    public function editOrder($od_id) {
        $label = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $order = Order::find($od_id);
        $order = Order::with('branch')->find($od_id);

        if (!$order) {
            return redirect()->route('edit.order', ['od_id' => $od_id]);
        }
        $users = User::all();
        return view('editOrder', compact('order', 'users'));
    }

        public function update(Request $request) {
        $validated = $request->validate([
            'od_month' => 'required',
        ]);

        $order = Order::find($request->od_id);

        if (!$order) {
            return redirect()->route('order');
        }

        if ($request->action === 'delete') {
            $order->delete();
            return redirect()->route('order');
        }

        $order->od_amount = $request->od_amount;
        $order->od_month = $request->od_month;
        $order->od_year = $request->od_year;
        $order->od_br_id = $request->od_br_id;
        $order->od_us_id = $request->od_us_id;
        $order->save();

        return redirect()->route('order.detail', ['br_id' => $order->od_br_id]); // กลับไปที่หน้ารายละเอียด
    }

    private array $monthMap = [
        'มกราคม' => 1, 'กุมภาพันธ์' => 2, 'มีนาคม' => 3, 'เมษายน' => 4,
        'พฤษภาคม' => 5, 'มิถุนายน' => 6, 'กรกฎาคม' => 7, 'สิงหาคม' => 8,
        'กันยายน' => 9, 'ตุลาคม' => 10, 'พฤศจิกายน' => 11, 'ธันวาคม' => 12,
    ];

    public function order_detail($br_id)
    {
        $thaiYear = Carbon::now()->year + 543;  // ปีปัจจุบัน (พ.ศ.)
        $branch = Branch::findOrFail($br_id);
        $user = User::findOrFail($branch->br_us_id);

        $monthlyOrders = $this->getMonthlyOrder($br_id, $thaiYear);
        $orderData = $this->formatOrderData($monthlyOrders);
        $medain = $this->monthlyMedianOrder($thaiYear);
        $growthRate = $this-> growthRateCalculate($br_id, $thaiYear);
        
        return view('orderDetail', [
            'branch'     => $branch,
            'user'       => $user,
            'orderData'  => $orderData,
            'month'      => $this->thaiMonths,
            'monthMap'   => $this->monthMap,
            'thisyear'   => $thaiYear,
            'medain'     => $medain,
            'growthRate' => $growthRate, 
            // dd($growthRate),
        ]);
    }
    private function getMonthlyOrder(int $branchId, int $year)
    {
        return DB::table('order as o')
            ->join('branch as b', 'o.od_br_id', '=', 'b.br_id')
            ->join('users as u', 'b.br_us_id', '=', 'u.us_id')
            ->where('o.od_year', $year)
            ->where('o.od_br_id', $branchId)
            ->whereIn('o.od_month', $this->thaiMonths)
            ->whereIn('o.od_id', function ($query) use ($year, $branchId) {
                $query->selectRaw('MAX(od_id)')
                    ->from('order')
                    ->where('od_year', $year)
                    ->where('od_br_id', $branchId)
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
            ->get();
    }


    private function formatOrderData($orders)
    {
        $data = array_fill(1, 12, 0);

        foreach ($orders as $order) {
            $monthName = trim($order->od_month);
            if (isset($this->monthMap[$monthName])) {
                $monthNumber = $this->monthMap[$monthName];
                $data[$monthNumber] = $order->od_amount;
            }
        }

        return $data;
    }

    private function monthlyMedianOrder(int $year)
    {
        $months = $this->thaiMonths;
    
        $orders = DB::table('order')
            ->select('od_month', 'od_amount')
            ->where('od_year', $year)
            ->whereIn('od_month', $months)
            ->orderByRaw("FIELD(od_month, '".implode("','", $months)."')")
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
                $monthlyMedian[$month] = 1;
            }
        }
    
        return $monthlyMedian;
    }

    
    private function growthRateCalculate($branchId, $year)
    {
        $currentMonthIndex = Carbon::now()->month - 1; 
        $months = array_values($this->thaiMonths); 

        // ถ้าเป็นมกราคม ยังไม่มีเดือนก่อนหน้าให้เปรียบเทียบ
        if ($currentMonthIndex === 0) {
            return 0;
        }

        $currentMonth = $months[$currentMonthIndex];
        $previousMonth = $months[$currentMonthIndex - 1];

        // ดึงยอดขายเดือนปัจจุบัน
        $thisMonth = DB::table('order')
            ->where('od_year', $year)
            ->where('od_br_id', $branchId)
            ->where('od_month', $currentMonth)
            ->orderByDesc('od_id')
            ->value('od_amount');

        // ดึงยอดขายเดือนก่อนหน้า
        $lastMonth = DB::table('order')
            ->where('od_year', $year)
            ->where('od_br_id', $branchId)
            ->where('od_month', $previousMonth)
            ->orderByDesc('od_id')
            ->value('od_amount');
        
            $thisMonth = $thisMonth ?? 0;
            $lastMonth = $lastMonth ?? 0;
        
            // ถ้าเดือนก่อนมียอดขาย
            if ($lastMonth != 0) {
                $change = $thisMonth - $lastMonth;
                $absPercent = number_format((abs($change) / abs($lastMonth)) * 100, 2);
            
                if ($change > 0) {
                    $percent = '+ ' . $absPercent . ' % ';
                } elseif ($change < 0) {
                    $percent = '- ' . $absPercent . ' % ';
                } else {
                    $percent = '0.00 % ';
                }
            }
            
        
            return $percent;

    }

}



