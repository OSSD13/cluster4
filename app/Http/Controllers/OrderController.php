<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller{
    private array $thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    private array $monthMap = [
        'มกราคม' => 1, 'กุมภาพันธ์' => 2, 'มีนาคม' => 3, 'เมษายน' => 4,
        'พฤษภาคม' => 5, 'มิถุนายน' => 6, 'กรกฎาคม' => 7, 'สิงหาคม' => 8,
        'กันยายน' => 9, 'ตุลาคม' => 10, 'พฤศจิกายน' => 11, 'ธันวาคม' => 12,
    ];

    public function order_detail($br_id){
        $thaiYear = Carbon::now()->year + 543;
        $currentMonthIndex = Carbon::now()->month - 1; // index starts from 0
        $currentMonthName = $this->thaiMonths[$currentMonthIndex];

        $branch = Branch::findOrFail($br_id);
        $user = User::findOrFail($branch->br_us_id);

        $monthlySales = $this->getMonthlySales($br_id, $thaiYear);

        $orderData = $this->formatSalesData($monthlySales);

        return view('orderDetail', [
            'branch'     => $branch,
            'user'       => $user,
            'orderData'  => $orderData,
            'month'      => $this->thaiMonths,
            'monthMap'   => $this->monthMap,
        ]);
    }

    private function getMonthlySales(int $branchId, int $year){
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
                'o.od_amount',
                'b.br_id',
                'b.br_code',
                'u.us_fname',
                'u.us_image'
            )
            ->get();
    }

    private function formatSalesData($sales){
        $data = array_fill(1, 12, 0);

        foreach ($sales as $sale) {
            $monthName = trim($sale->od_month);
            if (isset($this->monthMap[$monthName])) {
                $monthNumber = $this->monthMap[$monthName];
                $data[$monthNumber] = $sale->od_amount;
            }
        }

        return $data;
    }

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

        return redirect()->route('orderDetail', ['br_id' => $order->od_br_id]);
    }
}
