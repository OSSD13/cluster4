@extends('layouts.default')

@section('content')
    {{--
    @title : ทำลำดับสาขายอดขายทั้งหมด
    @author : นนทพัทธ์ ศิลธรรม 66160104
    @create date : 05/04/2568
--}}

    {{-- จัดอันดับ --}}
    <div class="pt-16 bg-white-100 w-full">
        <div class="bg-white rounded-2xl shadow border m-3 pb-4">
            <div class="flex flex-row">
                <h3 id="rankTitle" class="font-semibold text-lg mt-1 p-4">สาขาที่ทำยอดขายดีที่สุด</h3>
                <button id="switchRankButton"
                    class="text-sm font-bold m-3 p-2 ml-auto border border-[#CAC4D0] rounded-lg border-[0.5px]"
                    onclick="switchRank()">
                    จำนวนยอดขาย
                    <i class="fa-solid fa-repeat"></i>
                </button>
            </div>

            {{-- แสดงอันดับสาขาที่มียอดขายสูงสุด --}}
            <div id="branchRank">
                @foreach ($topBranch as $index => $alluser)
                    <div
                        class="p-4 {{ $index == 0 ? 'bg-[#4D55A0] text-white w-full shadow-[0px_3px_5px_rgba(0,0,0,1)]' : 'bg-white border border-[#CAC4D0] rounded-2xl mx-4 shadow-[0px_4px_5px_rgba(0,0,0,0.2)]' }}  mb-4 pb-2 flex items-center min-h-[140px]">
                        <div class="flex flex-col">
                            <h2 class="text-lg font-bold">สาขาที่ {{ $alluser->br_id }}</h2>
                            <div class="flex items-center my-2">
                                <img src="{{ $alluser->us_image }}" class="w-10 h-10 rounded-full mr-4">
                                <h3 class="font-semibold">{{ $alluser->us_fname }}</h3>
                            </div>
                            <p class="text-sm">รหัสสาขา : {{ $alluser->br_code }}</p>
                        </div>
                        <div class="ml-auto text-xl font-bold flex flex-col items-center">
                            @if ($index == 0)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#FFD43B] text-6xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-3xl font-bold pt-3">
                                        1
                                    </span>
                                </div>
                                <span class="text-lg font-semibold mt-1"
                                    id="amount{{ $index }}">{{ number_format($alluser->od_amount) }}
                                    ชิ้น</span>
                            @elseif ($index == 1)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#D2CFC6] text-5xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-lg font-bold pt-3">
                                        2
                                    </span>
                                </div>
                                <span class="text-lg font-semibold mt-1"
                                    id="amount{{ $index }}">{{ number_format($alluser->od_amount) }}
                                    ชิ้น</span>
                            @elseif ($index == 2)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#CD7F32] text-5xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-lg font-bold pt-3">
                                        3
                                    </span>
                                </div>
                                <span class="text-lg font-semibold mt-1"
                                    id="amount{{ $index }}">{{ number_format($alluser->od_amount) }}
                                    ชิ้น</span>
                            @else
                                <span class="text-lg font-semibold mt-1"
                                    id="amount{{ $index }}">{{ number_format($alluser->od_amount) }}
                                    ชิ้น</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- อับดับพนักงาน --}}
            <div id="userRank" style="display: none;">
                @foreach ($topUsers as $index => $topUser)
                    <div
                        class="p-4 {{ $index == 0 ? 'bg-[#4D55A0] text-white w-full shadow-[0px_3px_5px_rgba(0,0,0,1)]' : 'bg-white border border-[#CAC4D0] rounded-lg mx-4 shadow-[0px_4px_5px_rgba(0,0,0,0.2)]' }}  mb-4 pb-2 flex items-center min-h-[140px]">
                        <img src="{{ $topUser->us_image }}" class="w-12 h-12 rounded-full border-2 border-white"
                            alt="user">
                        <div class="ml-4">
                            <p class="font-semibold">{{ $topUser->us_fname }} {{ $topUser->us_lname }}</p>
                            <p class="text-sm">
                                <span
                                    class="px-2 mt-1 border rounded-full text-xs bg-white
                            @if ($topUser->us_role == 'CEO') border-yellow-700 text-yellow-700
                            @elseif ($topUser->us_role == 'Sales Supervisor') border-purple-500 text-purple-500
                            @else border-blue-300 text-blue-300 @endif">
                                    {{ $topUser->us_role }}
                                </span>
                            </p>
                            <p class="text-xs">{{ $topUser->us_email }}</p>
                        </div>

                        <div class="ml-auto text-xl font-bold flex flex-col items-center">
                            @if ($index == 0)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#FFD43B] text-6xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-3xl font-bold pt-3">
                                        1
                                    </span>
                                </div>
                                <span class="text-sm font-semibold mt-1"
                                    id="amountUser{{ $index }}">{{ number_format($topUser->branch_count) }}
                                </span>
                            @elseif ($index == 1)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#D2CFC6] text-5xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-lg font-bold pt-3">
                                        2
                                    </span>
                                </div>
                                <span class="text-sm font-semibold mt-1"
                                    id="amountUser{{ $index }}">{{ number_format($topUser->branch_count) }}
                                </span>
                            @elseif ($index == 2)
                                <div class="relative inline-block text-center">
                                    <i class="fa-solid fa-crown text-[#CD7F32] text-5xl"></i>
                                    <span
                                        class="absolute inset-0 flex justify-center items-center text-white text-lg font-bold pt-3">
                                        3
                                    </span>
                                </div>
                                <span class="text-sm font-semibold mt-1"
                                    id="amountUser{{ $index }}">{{ number_format($topUser->branch_count) }}
                                </span>
                            @else
                                <span class="text-sm font-semibold mt-1"
                                    id="amountUser{{ $index }}">{{ number_format($topUser->branch_count) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- //@auther : boom --}}
        {{-- all Order Graph --}}
        {{-- <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-none mt-20 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">ยอดขายทั้งหมดในปีนี้</p>
                    <h2 class="text-3xl font-bold">{{ number_format($totalSales) }} <span class="text-lg">ชิ้น</span>
                        <span class="text-green-500 text-sm font-semibold">{{ number_format($growthPercentage) }}%</span>
                    </h2>
                    <p class="text-gray-400 text-xs">ค่าเฉลี่ยรายเดือนอยู่ที่ <span
                            class="font-semibold">{{ number_format($averageSales) }}</span> ชิ้น</p>
                </div>
                <div>
                    <i class="fa-solid fa-box fa-2xl scale-150" style="color: #4d55a0;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-none mt-10 border border-gray-200">
            <p class="text-lg font-bold text-gray-800 mb-2">ยอดขายในปีนี้</p>

            <div class="w-full h-[400px] sm:h-[500px] relative">
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 w-full">
                <div class="bg-white p-4 rounded-lg shadow w-full text-left border border-gray-200">
                    <p class="text-sm font-semibold">Min :</p>
                    <p class="text-lg font-bold" id="minValue">-</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow w-full text-left border border-gray-200">
                    <p class="text-sm font-semibold">Max :</p>
                    <p class="text-lg font-bold" id="maxValue">-</p>
                </div>
            </div>
        </div> --}}

        {{-- Mork --}}
        {{-- กล่องข้อมูลสรุป --}}
        <div class="bg-white border border-gray-200 rounded-2xl m-3 p-5 mb-2 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-500 text-lg">จำนวนสาขาทั้งหมด</p>
                    <p class="text-3xl font-bold py-1">{{ number_format($totalBranches) }} สาขา</p>
                    <p class="text-green-600 text-sm">จำนวนสาขาเพิ่มขึ้นเฉลี่ย {{ $growthPercentage }}%</p>
                </div>
                <div class="p-4 rounded-full">
                    <i class="fa-solid fa-warehouse fa-2xl" style="color: #4D55A0;"></i>
                </div>
            </div>
        </div>

        {{-- กล่องกราฟ --}}
        <div class="bg-white border border-gray-200 rounded-2xl m-3 mb-2 p-5 shadow">
            <p class="font-semibold text-lg mb-4">อัตราการเติบโตของสาขาภายในปีนี้</p>
            <div class="w-full">
                <canvas id="branchGrowthChart"></canvas>
            </div>
        </div>

        {{-- wave --}}
        {{-- EmployeeGrowthRate --}}
        {{-- การ์ดแสดงจำนวนพนักงาน --}}
        <div class="bg-white border border-gray-200 rounded-2xl m-3 p-5 shadow">
            <p class="font-semibold text-gray-500 text-lg mb-3">จำนวนพนักงานทั้งหมด</p>
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-3xl font-bold text-gray-900">{{ $totalEmployees }} คน</h2>
                <i class="fa-solid fa-users text-[#4D55A0] fa-2xl"></i>
            </div>
            <hr class="my-3">
            <p class="text-base text-gray-700"> Sales <span class="float-right text-indigo-500 ">{{ $salesCount }}
                    คน</span></p>
            <hr class="my-3">
            <p class="text-base text-gray-700"> Sales Supervisor <span
                    class="float-right text-indigo-500 ">{{ $supervisorCount }} คน</span></p>
            <hr class="my-3">
            <p class="text-base text-gray-700"> CEO <span class="float-right text-indigo-500 ">{{ $ceoCount }}
                    คน</span></p>
        </div>

        {{-- การ์ดกราฟแสดงการเติบโต --}}
        <div class="bg-white border border-gray-200 rounded-2xl m-3 p-5 shadow">
            <p class="font-semibold text-lg mb-4">อัตราการเติบโตของพนักงานในปีนี้</p>
            <div class="w-full">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // allOrder Graph
        const monthlySales = @json($monthlySales);
        const labels = @json($labels); // ชื่อเดือน
        const ctxOrder = document.getElementById('salesChart').getContext('2d');

        const salesChart = new Chart(ctxOrder, {
            type: 'bar',
            data: {
                labels: labels, // ชื่อเดือนที่ส่งมาจาก Controller
                datasets: [{
                        label: 'ยอดขายของสาขา',
                        data: Object.values(monthlySales), // ยอดขายแต่ละเดือนที่ส่งมาจาก Controller
                        backgroundColor: 'rgba(54, 79, 199, 0.8)',
                        borderRadius: 4
                    },
                    {
                        label: 'ค่ามัธยฐาน',
                        type: 'line',
                        data: [3000, 5000, 4000, 12000, 20000, 25000, 40000, 50000, 30000, 45000, 70000,
                            80000
                        ], // ข้อมูลมัธยฐานตัวอย่าง
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        pointRadius: 4,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        type: 'logarithmic',
                        min: 0,
                        max: 1000000,
                        ticks: {
                            autoSkip: false,
                            stepSize: 1000,
                            callback: function(value) {
                                const allowedTicks = [0, 1000, 10000, 100000, 1000000];
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
                                const allowedTicks = [0, 1000, 10000, 100000, 1000000];
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

        // BranchRank and UserRank
        function switchRank() {
            const button = document.getElementById('switchRankButton');
            const title = document.getElementById('rankTitle');
            const branchData = document.getElementById('branchRank');
            const userData = document.getElementById('userRank');
            const currentText = button.innerText.trim();

            // เช็คข้อความปัจจุบันของปุ่ม
            if (currentText === "จำนวนยอดขาย") {
                // เปลี่ยนข้อความปุ่ม
                button.innerHTML = 'จำนวนสาขา <i class="fa-solid fa-repeat"></i>';

                // เปลี่ยนหัวเรื่อง
                title.innerText = 'พนักงานที่เพิ่มสาขามากที่สุด';

                // ซ่อนข้อมูลในกล่อง
                branchData.style.display = 'none';

                userData.style.display = 'block';

            } else {
                // เปลี่ยนข้อความปุ่มกลับ
                button.innerHTML = 'จำนวนยอดขาย <i class="fa-solid fa-repeat"></i>';

                // เปลี่ยนหัวเรื่องกลับ
                title.innerText = 'สาขาที่ทำยอดขายดีที่สุด';

                // แสดงข้อมูลในกล่องกลับ
                branchData.style.display = 'block';

                userData.style.display = 'none';
            }

            // สำหรับทุกยอดขายที่แสดงในหน้า
            const amountsBranch = document.querySelectorAll('[id^="amountBranch"]');
            amountsBranch.forEach(function(amountBranch) {
                // ใช้ toLocaleString เพื่อแสดงผลยอดขายด้วยเครื่องหมายคั่นพัน
                amountBranch.innerText = parseInt(amountBranch.innerText.replace(/[^0-9]/g, '')).toLocaleString() +
                    " ชิ้น";
            });

            const amountsUser = document.querySelectorAll('[id^="amountUser"]');
            amountsUser.forEach(function(amountUser) {
                // ใช้ toLocaleString เพื่อแสดงผลยอดขายด้วยเครื่องหมายคั่นพัน
                amountUser.innerText = parseInt(amountUser.innerText.replace(/[^0-9]/g, '')).toLocaleString() +
                    " สาขา";
            });
        }
    </script>

    <script>
        const ctxsales = document.getElementById('growthChart').getContext('2d');
        const growthChart = new Chart(ctxsales, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.',
                    'ธ.ค.'
                ],
                datasets: [{
                    data: @json($growthData),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#3B82F6',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        ticks: {
                            precision: 0, // <<< บอก Chart.js ว่าให้แสดงแค่จำนวนเต็มเท่านั้น
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return Number(value).toFixed(0); // <<< ปัดเศษแบบไม่มีทศนิยม
                            }
                        },
                        grid: {
                            color: '#E5E7EB'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            },
                            maxRotation: 45,
                            minRotation: 30
                        },
                        grid: {
                            color: '#E5E7EB'
                        }
                    }
                }

            }
        });

        //Mork
        const ctxBranch = document.getElementById('branchGrowthChart').getContext('2d');
        const branchGrowthChart = new Chart(ctxBranch, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($growthRates)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($growthRates)) !!},
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#3B82F6',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        ticks: {
                            precision: 0, // บอก Chart.js ว่าให้แสดงแค่จำนวนเต็มเท่านั้น
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return Number(value).toFixed(0); // ปัดเศษแบบไม่มีทศนิยม
                            }
                        },
                        grid: {
                            color: '#E5E7EB'
                        },
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#E5E7EB'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .dashboard-wrapper {
            max-width: 350px;
            margin: 60px auto;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .employee-card,
        .growth-card {
            padding: 24px;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .employee-card {
            width: 100%;
        }

        .employee-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .employee-card h1 {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
        }

        .employee-card i.fa-users {
            font-size: 32px;
            color: #4d55a0;
        }

        .employee-card h2 {
            text-align: left;
            font-size: 20px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 10px;
        }

        .employee-card p {
            text-align: left;
            font-size: 15px;
            color: #555;
            margin: 10px 0;
        }

        .employee-card span {
            float: right;
            color: #6b71da;
        }

        .employee-card hr {
            margin: 10px 0;
        }

        .growth-card h3 {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        canvas#growthChart {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
@endsection
