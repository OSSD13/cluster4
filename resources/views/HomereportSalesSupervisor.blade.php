@extends('layouts.default')
@section('content')
    <div class="w-full mx-auto mt-16 space-y-6 px-6">
        <div class="mb-2 px-4">
            <label
                class="bg-[#4D55A0] text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full pl-4">
                รายงานทั้งหมด
            </label>
        </div>
        <!-- เมนูแท็บ -->
        <div class="flex border-b mt-4">
            <button id="tab-my" onclick="showTab('my')"
                class="tab-button flex-1 border-b-2 border-indigo-700 text-indigo-700 font-medium py-2">
                สาขาของฉัน
            </button>
            <button id="tab-staff" onclick="showTab('staff')"
                class="tab-button flex-1 text-gray-600 hover:text-indigo-700 py-2">
                สาขาพนักงาน
            </button>
        </div>
        <div id="content-my" class="tab-content mt-4">
            <div class="flex justify-between items-center px-6">
                <!-- ฝั่งซ้าย -->
                <h1 class="text-3xl font-bold">ยอดรวมของปี {{ $selectedSupYear }}</h1>

                <!-- ฝั่งขวา: ปุ่ม dropdown -->
                <div class="relative inline-block text-left w-[120px]" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="inline-flex w-full justify-between items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50"
                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                        {{ $selectedSupYear }}
                        <svg class="ml-2 size-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 z-10 mt-2 w-full origin-top-right rounded-md bg-white ring-1 shadow-lg ring-black/5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            @foreach ($allYears as $year)
                                <a href="{{ route('report_SalesSupervisor', ['year' => $year]) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"
                                    tabindex="-1">
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            <hr class="my-3">
            <div class="mb-4 px-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">ยอดรวมทั้งหมดของปี {{ $selectedSupYear }}</p>
                            <div class="text-3xl font-bold my-3 flex items-center">
                                <span>{{ number_format($totalSalesSup) }} ชิ้น</span>
                                @if ($growthPercentage > 0)
                                    <span class="ml-4 text-sm text-green-600">
                                        + {{ number_format($growthPercentage) }}%
                                    @elseif ($growthPercentage < 0)
                                        <span class="ml-4 text-sm text-red-600">
                                            - {{ number_format(abs($growthPercentage)) }}%
                                        @else
                                            <span class="ml-4 text-sm text-gray-600">0%</span>
                                @endif
                            </div>
                            <p class="text-sm">ค่าเฉลี่ยรายเดือนอยู่ที่ {{ number_format($averageSales) }} ชิ้น</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-full">
                        <i class="fa-solid fa-box fa-2xl" style="color: #4d55a0;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center px-4">
            <!-- ฝั่งซ้าย -->
            <h1 class="text-3xl font-bold">กราฟยอดขายปี {{ $selectedSupYear }}</h1>
        </div>
        <!-- กราฟเส้น -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <p class="font-semibold text-lg mb-4">ยอดขายในปีนี้</p>
                <div class="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px]">
                    <canvas id="salesChart" class="w-full h-full max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="flex justify-between items-center px-4">
                    <div class="flex-1">
                        <p class="text-gray-500">จำนวนสาขาทั้งหมด</p>
                        <p class="text-3xl font-bold">{{ number_format($branchCount) }} สาขา</p>
                        <p class="text-green-600 text-sm">จำนวนสาขาเพิ่มขึ้นเฉลี่ย {{ $branchPercen }}%</p>
                    </div>
                    <div class="flex flex-col items-center ml-auto">
                        <i class="fa-solid fa-warehouse mt-3 fa-2xl" style="color: #4D55A0;"></i>
                        <a href="{{ route('branchMyMap') }}" class="text-blue-600 font-sm hover:text-blue-700 mt-7">
                            ดูเพิ่มเติม
                        </a>
                    </div>
                </div>
                <div>
                    <hr class="my-3">
                    <div class="flex justify-between">
                        <p class="text-gray-500">ยอดขายรวมมากที่สุด {{ $selectedSupYear }}</p>
                        <p class="text-gray-500">จากปีที่แล้ว</p>
                    </div>
                    <ul>
                        @foreach($branchesRank as $index => $branch)
                        <li class="flex justify-between items-center py-2">
                            <span class="flex-1 text-left font-bold text-sm">สาขาที่ {{ $branch->br_id }}</span>
                            <span class="flex-1 text-center font-bold text-sm">{{ number_format($branch->total_sales, 2) }} ชิ้น</span>
                            <span class="flex-1 text-right font-bold text-sm">
                                {{ number_format($branch->growth_percentage, 2) }}%
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>
<script>
    const monthlyTotal = @json($monthlyTotal);  // ส่งข้อมูลของยอดขาย
    const monthlyMedian = @json($monthlyMedian);  // ส่งข้อมูลของ Median
    const monthMap = @json($monthMap);
    const ctxbar = document.getElementById('salesChart').getContext('2d');

    const salesData = {
        labels: monthMap,  // ใช้ $monthMap เพื่อแสดงชื่อเดือน
        datasets: [{
            label: 'ยอดขาย',
            data: monthlyTotal,  // ยอดขายในแต่ละเดือน
            backgroundColor: 'rgba(54,79,199,0.8)',
            borderColor: 'rgba(0,0,255,1)',
            borderWidth: 2,
            pointRadius: 4,
            fill: false,
            tension: 0.3
        },
        {
            label: 'ค่า Median ของยอดขาย',
            data: monthlyMedian,  // ค่า median ของยอดขาย
            borderColor: 'rgba(255,99,132,1)',
            backgroundColor: 'rgba(255,99,132,0.2)',
            borderWidth: 2,
            fill: false,
            pointRadius: 4,
            tension: 0.3,
            borderDash: [5, 5]  // เส้นประ
        }]
    };

    const config = {
        type: 'line',
        data: salesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.raw + ' ชิ้น';
                        }
                    }
                },
                datalabels: {
                    anchor: 'center',
                    align: 'center',
                    formatter: function(value) {
                        return value;
                    },
                    color: '#FFFFFF',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                    }
                },
                y: {
                    title: {
                        display: true,
                    },
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    };

    const salesChart = new Chart(ctxbar, config);
</script>
@endsection

@section('styles')
@endsection
