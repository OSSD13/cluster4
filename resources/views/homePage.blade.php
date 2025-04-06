@extends('layouts.default')

@section('content')

    {{--
    @title : ทำลำดับสาขายอดขายทั้งหมด
    @author : นนทพัทธ์ ศิลธรรม 66160104
    @create date : 05/04/2568
--}}

    <div class="pt-16 bg-white-100 w-full">
        <div class="bg-white rounded-lg shadow m-3 pb-4">
            <div class="flex flex-row">
                <h3 id="rankTitle" class="text-sm font-bold mt-1 p-4">สาขาที่ทำยอดขายดีที่สุด</h3>
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
                        class="p-4 {{ $index == 0 ? 'bg-[#4D55A0] text-white w-full shadow-[0px_4px_5px_rgba(0,0,0,0.5)]' : 'bg-white border border-[#CAC4D0] rounded-lg mx-4' }}  mb-4 pb-2 flex items-center ">
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
                {{-- อันดับ 1 --}}
                @if (count($topUsers) > 0)
                    <div class="w-full bg-[#4D55A0] text-white flex items-center shadow-md min-h-[130px] px-6">
                        <img src="{{ $topUsers[0]->us_image }}" class="w-12 h-12 rounded-full border-2 border-white"
                            alt="user">
                        <div class="ml-4">
                            <p class="font-semibold">{{ $topUsers[0]->us_fname }} {{ $topUsers[0]->us_lname }}</p>
                            <p class="text-sm">
                                <span
                                    class="px-2 mt-1 border rounded-full text-xs bg-white
                            @if ($topUsers[0]->us_role == 'CEO') border-yellow-700 text-yellow-700
                            @elseif ($topUsers[0]->us_role == 'Sales Supervisor') border-purple-500 text-purple-500
                            @else border-blue-300 text-blue-300 @endif">
                                    {{ $topUsers[0]->us_role }}
                                </span>
                            </p>
                            <p class="text-xs">{{ $topUsers[0]->us_email }}</p>
                        </div>
                        <div class="ml-auto flex flex-col items-center justify-center relative">
                            <div class="relative">
                                <i class="fa-solid fa-crown text-7xl" style="color: #FFD43B"></i>
                                <span
                                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl text-white">
                                    1
                                </span>
                            </div>
                            <p class="text-lg mt-1">{{ number_format($topUsers[0]->branch_count) }} สาขา</p>
                        </div>
                    </div>
                @endif

                {{-- อันดับ 2-5 --}}
                @if (count($topUsers) > 1)
                    <div class="mt-4 space-y-3 p-6">
                        @for ($i = 1; $i < count($topUsers); $i++)
                            <div class="p-4 rounded-xl bg-white flex items-center shadow-md border border-gray-300">
                                <img src="{{ $topUsers[$i]->us_image }}"
                                    class="w-12 h-12 rounded-full border border-gray-300" alt="user">
                                <div class="ml-4">
                                    <p class="font-semibold">{{ $topUsers[$i]->us_fname }} {{ $topUsers[$i]->us_lname }}
                                    </p>
                                    <p class="text-sm">
                                        <span
                                            class="px-2 mt-1 border rounded-full text-xs bg-white
                                        @if ($topUsers[$i]->us_role == 'CEO') border-yellow-700 text-yellow-700
                                        @elseif ($topUsers[$i]->us_role == 'Sales Supervisor') border-purple-500 text-purple-500
                                        @else border-blue-300 text-blue-300 @endif">
                                            {{ $topUsers[$i]->us_role }}
                                        </span>
                                    </p>
                                    <p class="text-xs">{{ $topUsers[$i]->us_email }}</p>
                                </div>
                                <div class="ml-auto flex flex-col items-center justify-center relative">
                                    @if ($i < 3)
                                        <div class="relative">
                                            <i class="fa-solid fa-crown text-5xl"
                                                style="color: {{ $i == 1 ? '#D2CFC6' : '#CD7F32' }}"></i>
                                            <span
                                                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl text-white">
                                                {{ $i + 1 }}
                                            </span>
                                        </div>
                                    @endif
                                    <p class="text-lg mt-1">{{ number_format($topUsers[$i]->branch_count) }} สาขา</p>
                                </div>
                            </div>
                        @endfor
                    </div>
                @else
                    <div class="text-center py-8">
                        <p>ไม่พบข้อมูลของพนักงาน</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- //@auther : boom --}}
        <div class="bg-white shadow-md rounded-2xl p-6 w-full max-w-none mt-20 border border-gray-200">
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



                <div class="pt-16 bg-white-100 w-screen -mx-8 px-4 sm:px-6 lg:px-8">
                    {{-- กล่องข้อมูลสรุป --}}
                    <div class="mb-2">
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow w-full">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500">จำนวนสาขาทั้งหมด</p>
                                    <p class="text-3xl font-bold">{{ number_format($totalBranches) }} สาขา</p>
                                    <p class="text-green-600 text-sm">จำนวนสาขาเพิ่มขึ้นเฉลี่ย {{ $growthPercentage }}%</p>
                                </div>
                                <div class="p-4 rounded-full">
                                    <i class="fa-solid fa-warehouse fa-2xl" style="color: #4D55A0;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- กล่องกราฟ --}}
                    <div class="mb-8">
                        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow w-full">
                            <p class="font-semibold text-lg mb-4">อัตราการเติบโตของสาขาภายในปีนี้</p>
                            <div class="w-full" style="height: 500px;">
                                <canvas id="growthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>




            @endsection



            @section('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const monthlySales = @json($monthlySales);
                    const labels = @json($labels); // ชื่อเดือน
                    const ctx = document.getElementById('salesChart').getContext('2d');

                    const salesChart = new Chart(ctx, {
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
                        const amounts = document.querySelectorAll('[id^="amount"]');
                        amounts.forEach(function(amount) {
                            // ใช้ toLocaleString เพื่อแสดงผลยอดขายด้วยเครื่องหมายคั่นพัน
                            amount.innerText = parseInt(amount.innerText.replace(/[^0-9]/g, '')).toLocaleString() + " ชิ้น";
                        });
                    }
                    const ctx = document.getElementById('growthChart').getContext('2d');
                    const growthChart = new Chart(ctx, {
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
                                    ticks: {
                                        stepSize: 2, // <-- กำหนดช่วงของตัวเลขที่จะแสดงบนแกน Y
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: '#E5E7EB'
                                    },
                                    suggestedMax: 8 // <-- แนะนำให้กราฟมี max อย่างน้อย 8 หรือ 10
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
