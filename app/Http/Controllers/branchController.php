<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class branchController extends Controller
{
    private array $thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];

    private array $monthMap = [
        'มกราคม' => 1, 'กุมภาพันธ์' => 2, 'มีนาคม' => 3, 'เมษายน' => 4,
        'พฤษภาคม' => 5, 'มิถุนายน' => 6, 'กรกฎาคม' => 7, 'สิงหาคม' => 8,
        'กันยายน' => 9, 'ตุลาคม' => 10, 'พฤศจิกายน' => 11, 'ธันวาคม' => 12,
    ];

    // Index view
    public function index()
    {
        return view('branchMyMap');
    }

    // Show supervisor
    public function showSupervisor($id)
    {
        $user = User::with('head')->find($id);
        return view('branchMyMap', ['branch' => $user]);
    }

    //Ice
    public function branch_detail($br_id)
    {
       
        $thaiYear = Carbon::now()->year + 543;

        
        $branch = Branch::findOrFail($br_id);
    
        
        $totalAmount = $this->sumOrder($br_id, $thaiYear);
    
        dd($totalAmount);
        return view('branchDetail', [
            'br_id'       => $br_id,
            'branch'      => $branch,
            'thaiYear'    => $thaiYear,
            'totalAmount' => $totalAmount,
        ]);

    }

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
        ]);
    }
    


}

