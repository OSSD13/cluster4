

@extends('layouts.default')

@section('content')
<div class="pt-16 min-h-screen px-4 bg-white">
    {{--  @author : 66160381 --}}
    {{-- Header --}}
    <div class="w-full space-y-4 pt-4 pb-4">
        <a href="{{ url('') }}" class="text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full bg-indigo-800">
            <i class="fa-solid fa-arrow-left mx-3"></i>
            ยอดขาย (สาขา {{ $branch->br_name }})
        </a>
    </div>

  {{-- ข้อมูลผู้ดูแล --}}
<div class="bg-white shadow-md rounded-2xl p-6 flex items-center justify-between mt-4">
    <div class="flex items-center">
        <img src="{{ $user->us_image }}" class="w-16 h-16 rounded-full object-cover" alt="User Image">
        <div class="ml-4">
            <p class="font-semibold text-xl text-gray-800">
                {{ $user->us_fname }} {{ $user->us_lname }}
            </p>
            <span class="inline-block mt-1 px-3 py-1 border rounded-full text-xs bg-white 
                {{ $user->us_role === 'CEO' ? 'border-yellow-700 text-yellow-700' : '' }}
                {{ $user->us_role === 'Sales Supervisor' ? 'border-purple-500 text-purple-500' : '' }}
                {{ !in_array($user->us_role, ['CEO', 'Sales Supervisor']) ? 'border-blue-300 text-blue-300' : '' }}">
                {{ $user->us_role }}
            </span>
            <p class="text-gray-500 text-sm mt-1">{{ $user->us_email }}</p>
        </div>
    </div>

    {{-- growthRate --}}
    <div class="flex flex-col items-center">
        <span>
            @if($growthRate >= 0)
                <i class="fas fa-arrow-up text-green-600"></i>
            @else
                <i class="fas fa-arrow-down text-red-600"></i>
            @endif
        </span>
        <span class="text-sm font-semibold 
            {{ $growthRate >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ $growthRate >= 0 ? '' : '' }}{{$growthRate}}
        </span>
    </div>
    
</div>


    {{-- กราฟยอดขาย --}}
    <div class="bg-white p-4 rounded-lg shadow mt-4">
        <div class="flex justify-between items-center mb-4">
            <p class="text-lg font-bold">ยอดขายในปีนี้</p>
            <div id="custom-legend" class="flex gap-4 items-center text-sm"></div>
        </div>
        <div class="w-full" style="height: 200px;">
            <canvas id="orderTotalChart"></canvas>
        </div>
    </div>


    {{-- จำนวนออเดอร์ทั้งหมด --}}
    <div class="w-full mt-8">
        <div class="bg-white shadow-md rounded-2xl p-6 flex items-center justify-between">
            <div>
                <h4 class=" text-l mb-4">จำนวนออเดอร์ทั้งหมด</h4>
                <h2 class="text-3xl font-bold text-gray-800">{{ number_format(array_sum($orderData)) }} ชิ้น</h2>
            </div>
            <div class="p-4 ">
                <i class="fa-solid fa-box fa-2xl text-indigo-600"></i>
            </div>
        </div>
    </div>
    

    {{-- ยอดขายรายเดือน --}}
    <div class="w-full bg-white space-y-4 mt-8">
        @foreach ($monthMap as $monthName => $monthNumber)
            <div class="bg-white p-4 rounded-sm shadow flex justify-between items-center">
                <div>
                    <h3 class="text-xl mb-2 font-bold">ยอดขายเดือน{{ $monthName }} {{ $thisyear }}</h3>
                    <p class="text-lg">รหัสสาขา : {{ $branch->br_code }}</p>
                    <p class="text-lg">
                        ยอดขาย : {{ number_format((float)($orderData[$monthNumber]['amount'] ?? 0)) }} ชิ้น
                    </p>
                    
                </div>

                {{-- Kebab Menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-gray-500 hover:text-gray-800">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg w-32 z-10">
                        <ul>
                            <li class="border-b">
                                <a href="{{ url('order/edit/' . ($orderData[$monthNumber]['id'] ?? 0)) }}"
                                class="block px-4 py-2 text-sm text-gray-700">
                                    แก้ไข
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block px-4 py-2 text-sm text-red-600">ลบ</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
<script>
    const monthlySales = @json($orderData); // ข้อมูลยอดขายรายเดือน
    const labels = @json($month); // ชื่อเดือนที่ใช้เป็น labels สำหรับกราฟ
    const monthlyMedian = @json($medain); // ค่ามัธยฐานสำหรับกราฟ

    const ctxOrder = document.getElementById('orderTotalChart').getContext('2d');

    // หาค่ามากสุดจากยอดขายในเดือนต่างๆ
    const maxSales = Math.max(...Object.values(monthlySales));


    const maxValue = Math.pow(10, Math.ceil(Math.log10(maxSales)));

    const salesChart = new Chart(ctxOrder, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'ยอดขายในเดือนนี้',
                    type: 'line',
                    data: Object.values(monthlySales),
                    borderColor: 'rgba(54, 79, 199, 0.8)',  
                    backgroundColor: 'rgba(54, 79, 199, 0.8)', 
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    spanGaps: true,
                    pointStyle: 'circle',
                    order: 1
                },
                {
                    label: 'ค่ามัธยฐาน',
                    type: 'line',
                    data: Object.values(monthlyMedian),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    spanGaps: true,
                    pointStyle: 'circle',
                    order: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    type: 'logarithmic',
                    min: 0,
                    max: maxValue,  // กำหนด max จากระดับที่คำนวณ
                    ticks: {
                        autoSkip: false,
                        stepSize: 1000,
                        callback: function(value) {
                            const allowedTicks = [1, 1000, 10000, 100000, 1000000, 100000000];
                            if (allowedTicks.includes(value)) {
                                return value.toLocaleString(); // แสดงตัวเลขพร้อมคอมม่า
                            }
                            return '';
                        }
                    },
                    grid: {
                        drawTicks: true,
                        drawOnChartArea: true,
                        color: function(context) {
                            const tickValue = context.tick.value;
                            const allowedTicks = [0, 1000, 10000, 100000, 1000000, 100000000];
                            if (allowedTicks.includes(tickValue)) {
                                return 'rgba(0, 0, 0, 0.1)';
                            }
                            return 'transparent';
                        }
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // กำหนด Legend จุด . ด้านบน
    const legendContainer = document.getElementById('custom-legend');
    legendContainer.innerHTML = salesChart.data.datasets.map(dataset => {
        const color = dataset.backgroundColor || dataset.borderColor;
        return `
            <div class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full inline-block" style="background-color: ${color};"></span>
                <span>${dataset.label}</span>
            </div>
        `;
    }).join('');
</script>
@endsection
