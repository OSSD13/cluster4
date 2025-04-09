@extends('layouts.default')
@section('content')
    <div class="w-full mx-auto mt-16 mb-10 space-y-6 px-6">
        <div class="mb-2 px-4">
            <label
                class="bg-[#4D55A0] text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full pl-4">
                รายงานทั้งหมด
            </label>
        </div>
        <div class="flex justify-between items-center px-4">
            <!-- ฝั่งซ้าย -->
            <h1 class="text-3xl font-bold">ยอดรวมของปี {{ $selectedYear }}</h1>

            <!-- ฝั่งขวา: ปุ่ม dropdown -->
            <div class="relative inline-block text-left w-[120px]" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="inline-flex w-full justify-between items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50"
                    id="menu-button" aria-expanded="true" aria-haspopup="true">
                    {{ $selectedYear }}
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
                            <a href="{{ route('report_CEO', ['year' => $year]) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"
                                tabindex="-1">
                                {{ $year }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">ยอดรวมทั้งหมดของปี {{ $selectedYear }}</p>
                        <div class="text-3xl font-bold my-3 flex items-center">
                            <span>{{ number_format($totalAmount) }} ชิ้น</span>
                            @if ($growthPercentage > 0)
                                <span class="ml-4 text-sm text-green-600">
                                    + {{ number_format($growthPercentage) }}%
                                @elseif ($growthPercentage < 0)
                                    <span class="ml-4 text-sm text-red-600">
                                        - {{ number_format(abs($growthPercentage)) }}%
                                    @else
                                        <span class="ml-4 text-sm text-gray-600">0%</span>
                            @endif
                            </span>
                        </div>
                        <p class="text-sm">ค่าเฉลี่ยรายเดือนอยู่ที่ {{ number_format($average) }} ชิ้น</p>
                    </div>

                    <div class="p-4 rounded-full">
                        <i class="fa-solid fa-box fa-2xl" style="color: #4d55a0;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center px-4">
            <!-- ฝั่งซ้าย -->
            <h1 class="text-3xl font-bold">กราฟยอดขายปี {{ $selectedYear }}</h1>
        </div>
        <!-- กราฟแท่ง -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <p class="font-semibold text-lg mb-4">ยอดขายในปีนี้</p>
                <div class="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px]">
                    <canvas id="salesChart" class="w-full h-full max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>
        <!-- กราฟวงกลม -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <p class="font-semibold text-lg mb-4">ภูมิภาค</p>
                <div class="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px] flex justify-center items-center">
                    <canvas id="cicleChart"></canvas>
                </div>
            </div>
        </div>

        <!-- จำนวนสาขา -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">จำนวนสาขาทั้งหมด</p>
                        <p class="text-3xl font-bold">{{ number_format($currentYearBranches) }} สาขา</p>
                        <p class="text-green-600 text-sm">จำนวนสาขาเพิ่มขึ้นเฉลี่ย {{ $growthPercentageBranches }}%</p>
                    </div>
                    <div class="flex flex-col items-center"> <!-- flex column ที่นี่ -->
                        <i class="fa-solid fa-warehouse mt-3 fa-2xl" style="color: #4D55A0;"></i>
                        <a href="{{ route('branchMyMap') }}" class="text-blue-600 font-sm hover:text-blue-700 mt-7">
                            ดูเพิ่มเติม
                        </a>
                    </div>
                </div>
            </div>
        </div>



        <!-- กราฟอัตราการเติบโตสาขา -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px]">
                    <canvas id="branchChart" class="w-full h-full max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>
        <!-- จำนวนพนักงาน -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500">จำนวนพนักงานทั้งหมด</p>
                        <p class="text-3xl font-bold">{{ number_format($currentYearEmployeeCount) }} คน</p>
                        <p class="text-green-600 text-sm">จำนวนพนักงานเพิ่มขึ้นเฉลี่ย {{ $growthPercentagemployee }}%</p>
                    </div>
                    <div class="flex flex-col items-center"> <!-- flex column ที่นี่ -->
                        <i class="fa-solid fa-users fa-2xl mt-3" style="color: #4D55A0;"></i>
                        <a href="/path-to-details-page" class="text-blue-600 font-sm hover:text-blue-700 mt-7">
                            ดูเพิ่มเติม
                        </a>
                    </div>
                </div>

                <hr class="my-3">
                <p class="text-base text-gray-700"> Sales
                    <span class="float-right text-indigo-500 font-medium">{{ $currentYearRoleCounts['Sales'] }} คน</span>
                </p>
                <hr class="my-3">
                <p class="text-base text-gray-700"> Sales Supervisor
                    <span class="float-right text-indigo-500 font-medium">{{ $currentYearRoleCounts['Sales Supervisor'] }}
                        คน</span>
                </p>
                <hr class="my-3">
                <p class="text-base text-gray-700"> CEO
                    <span class="float-right text-indigo-500 font-medium">{{ $currentYearRoleCounts['CEO'] }} คน</span>
                </p>
            </div>
        </div>
        <!-- กราฟอัตราการเติบโตพนักงาน -->
        <div class="mb-4 px-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow">
                <div class="w-full h-[300px] sm:h-[350px] md:h-[400px] lg:h-[450px]">
                    <canvas id="employeeChart" class="w-full h-full max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>
    {{-- กราฟแท่ง --}}
    <script>
        const monthlyMedian = @json(array_values($monthlyMedian));
        const monthlyTotal = @json(array_values($monthlyTotal));
        const ctxbar = document.getElementById('salesChart').getContext('2d');

        // ข้อมูลกราฟ: ข้อมูลยอดขายที่ส่งมาจาก Controller
        const salesData = {
            labels: @json($sales->pluck('od_month')), // แสดงชื่อเดือนจากข้อมูลที่ดึงมา
            datasets: [{
                    label: 'ยอดขาย',
                    data: monthlyTotal, // ยอดขายในแต่ละเดือน
                    backgroundColor: 'rgba(54,79,199,0.8)', // สีของแท่ง
                    borderColor: 'rgba(0,0,255,1)', // สีของขอบแท่ง
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: false, // ไม่เติมสีภายในกราฟ
                    tension: 0.3 // กำหนดความโค้งของเส้นกราฟ


                },
                {
                    label: 'ค่า Median ของยอดขาย',
                    data: monthlyMedian, // ค่า median ที่ส่งมาจาก Controller
                    borderColor: 'rgba(255,99,132,1)', // สีของเส้น Median
                    backgroundColor: 'rgba(255,99,132,0.2)',
                    borderWidth: 2, // ความหนาของเส้น
                    fill: false, // ไม่เติมสีภายในกราฟ
                    pointRadius: 4,
                    tension: 0.3, // กำหนดความโค้งของเส้น
                    borderDash: [5, 5], // ให้เส้นเป็นเส้นประ


                }
            ]
        };

        const config = {
            type: 'line', // ชนิดกราฟ
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
                                return tooltipItem.raw + ' ชิ้น'; // แสดงข้อมูลยอดขายเป็นจำนวนชิ้น
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

        // สร้างกราฟ
        const salesChart = new Chart(ctxbar, config);
    </script>

    {{-- // กราฟวงกลม --}}
    <script>
        const ctxcicle = document.getElementById('cicleChart').getContext('2d');

        const regionSalesData = {
            labels: @json($regionLabels), // ชื่อภาค
            datasets: [{
                label: 'ยอดขายตามภาค',
                data: @json($salesData), // ยอดขายในแต่ละภาค
                backgroundColor: [
                    '#4D55A0', '#FF6F61', '#6B8E23', '#F39C12', '#2980B9', '#E74C3C', '#8E44AD', '#1ABC9C',
                    '#F1C40F'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        const regionSalesConfig = {
            type: 'doughnut',
            data: regionSalesData,
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw +
                                    ' ชิ้น'; // เปลี่ยนจาก บาท เป็น ชิ้น
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    datalabels: {
                        anchor: 'center', // จัดข้อความให้อยู่กลางส่วนของกราฟ
                        align: 'center', // จัดข้อความให้อยู่กลาง
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((sum, val) => sum + val,
                                0); // หาผลรวมของข้อมูลทั้งหมด
                            const percentage = ((value / total) * 100).toFixed(2); // คำนวณเปอร์เซ็นต์
                            return value + ' ชิ้น (' + percentage + '%)'; // แสดงยอดขายและเปอร์เซ็นต์ในกราฟ
                        },
                        color: '#ffffff', // สีของข้อความในกราฟ
                        font: {
                            weight: 'bold', // ทำให้ข้อความหนา
                            size: 14 // ขนาดของข้อความ
                        },
                    }
                },
            }
        };

        new Chart(ctxcicle, regionSalesConfig);
    </script>


    <script>
        const ctxbranch = document.getElementById('branchChart').getContext('2d');

        const labels = {!! json_encode(collect($cumulativeBranches)->pluck('month')) !!};
        const rawData = {!! json_encode(collect($cumulativeBranches)->pluck('total_branches')) !!};
        const lastMonthWithNewBranch = {{ $lastMonthWithNewBranch ?? 0 }}; // Laravel ส่งมาจาก controller

        // แปลงข้อมูลให้หยุดเส้นกราฟหลังจากเดือนสุดท้ายที่มีการเพิ่มสาขา
        const modifiedData = rawData.map((val, index) => index <= (lastMonthWithNewBranch - 1) ? val : null);

        const branchChart = new Chart(ctxbranch, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'จำนวนสาขาสะสม',
                    data: modifiedData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 3,
                    pointBackgroundColor: '#007bff',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'การเติบโตของจำนวนสาขารายเดือน ปี {{ $selectedYear }}',
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        },
                        title: {
                            display: true,
                            text: 'จำนวนสาขา'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'เดือน'
                        }
                    }
                }
            }
        });
    </script>

    <script>
        const ctxemployee = document.getElementById('employeeChart').getContext('2d');

        const labelsemployee = {!! json_encode(collect($cumulativeemployee)->pluck('month')) !!};
        const rawDataemployee = {!! json_encode(collect($cumulativeemployee)->pluck('total_employee')) !!};
        const lastMonthWithNewemployee = {{ $lastMonthWithNewemployee ?? 0 }};

        // แปลงข้อมูลให้หยุดเส้นกราฟหลังจากเดือนสุดท้ายที่มีการเพิ่มพนักงาน
        const modifiedDataemployee = rawDataemployee.map((valemployee, indexemployee) => indexemployee <= (
            lastMonthWithNewemployee - 1) ? valemployee : null);

        const employeeChart = new Chart(ctxemployee, {
            type: 'line',
            data: {
                labels: labelsemployee,
                datasets: [{
                    label: 'จำนวนพนักงานสะสม',
                    data: modifiedDataemployee,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: '#007bff',
                    borderWidth: 3,
                    pointBackgroundColor: '#007bff',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'การเติบโตของจำนวนพนักงานรายเดือน ปี {{ $selectedYear }}',
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        },
                        title: {
                            display: true,
                            text: 'จำนวนพนักงาน'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'เดือน'
                        }
                    }
                }
            }
        });
    </script>
@endsection

@section('styles')
@endsection
