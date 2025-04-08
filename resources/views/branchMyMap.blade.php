

@extends('layouts.default')
@section('content')
    <div class="pt-16 max-w-md mx-auto p-4 bg-gray-100 min-h-screen w-full">
        {{-- หัวข้อ --}}
        <div class="mb-4">
            <div class="text-white text-xl font-extrabold py-3 rounded-2xl text-left" style="background-color: #4D55A0;">
                <span style="padding-left: 5%;">My Map สาขาที่ 1</span>
            </div>

        </div>

        {{-- ปุ่มแท็บ --}}
        <div class="flex justify-center items-center rounded-xl overflow-hidden">
            <button
                class="tab-btn w-1/3 py-1 px-4  font-bold text-center text-gray-800
        hover:border-b-2 hover:border-[#4D55A0] whitespace-nowrap"
                onclick="showTab('data')">ข้อมูล</button>
            <button
                class="tab-btn w-1/3 py-1 px-4  font-bold text-center text-gray-800
        hover:border-b-2 hover:border-[#4D55A0] whitespace-nowrap flex justify-center"
                onclick="showTab('location')">สถานที่ใกล้เคียง</button>
        </div>
        <div class="w-full h-px bg-gray-400 mb-4"></div>


        {{-- Section: ข้อมูล --}}
        <section id="data" class="tab-content">
            <h2 class="text-base font-bold mb-4">ยอดขายในปีนี้</h2>
            <canvas id="salesChart" class="mb-10"></canvas>



            {{-- ตัวเลือกเดือน --}}
            <div class="flex justify-center items-center mb-7">
                <div class="inline-flex rounded-lg border border-gray-400 overflow-hidden shadow-sm">
                    <button
                        class="px-6 py-2 font-semibold text-gray-600 hover:bg-[#4D55A0] hover:text-white focus:bg-[#4D55A0] focus:text-white transition-colors duration-200">3
                        เดือน</button>
                    <div class="w-px bg-gray-400"></div>
                    <button
                        class="px-6 py-2 font-semibold text-gray-600 hover:bg-[#4D55A0] hover:text-white focus:bg-[#4D55A0] focus:text-white transition-colors duration-200">6
                        เดือน</button>
                    <div class="w-px bg-gray-400"></div>
                    <button
                        class="px-6 py-2 font-semibold text-gray-600 hover:bg-[#4D55A0] hover:text-white focus:bg-[#4D55A0] focus:text-white transition-colors duration-200">12
                        เดือน</button>
                </div>
            </div>

            {{-- จำนวนออเดอร์ --}}
            <div class="flex items-center justify-between bg-white rounded-xl p-4 shadow">
                <div>
                    <p class="text-gray-800 ">จำนวนออเดอร์ทั้งหมดของปีนี้</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">12,000 ชิ้น</p>
                </div>
                <svg class="w12 h-12 text-[#4D55A0]" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 3a1 1 0 011-1h2a1 1 0 011 1v1h8V3a1 1 0 011-1h2a1 1 0 011 1v3H2V3zm0 5h16v9a1 1 0 01-1 1H3a1 1 0 01-1-1V8z" />
                </svg>
            </div>
        </section>
        {{-- กล่องข้อมูลผู้ดูแล --}}
        <div class="flex items-center justify-center my-4">
            <div class="flex-grow border-t border-gray-400"></div>
            <span class="px-4 text-gray-500 font-medium">ผู้ดูแล</span>
            <div class="flex-grow border-t border-gray-400"></div>
        </div>

        {{-- รูปภาพและชื่อ --}}

        <div class="flex flex-wrap items-center justify-center mb-4 space-x-4">

                <div class="flex items-center space-x-2 m-2">
                    <img src="{{ asset('images/profile.jpg') }}" alt="user" class="w-14 h-14 rounded-full object-cover">
                    <div>


                    </div>
                </div>

        </div>

        {{-- รหัสสาขา --}}
        <div class="w-full h-px bg-gray-400 mb-4"></div>
        <div class="space-y-5 mb-1">
            <p class="translate-x-[25px]">
                <span class="font-semibold text-gray-800 text-lg">รหัสสาขา :</span> 268761
            </p>
        </div>

        {{-- ที่อยู่ติดต่อของสาขา --}}
        <div class="flex items-start translate-x-[25px]">
            <svg class="w-7 h-9 text-[#4D55A0]" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 2C6.134 2 3 5.134 3 9c0 5.25 7 9 7 9s7-3.75 7-9c0-3.866-3.134-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" />
            </svg>
            <span class="text-gray-700 leading-relaxed">
                123/321 ถนน ลงหาด ตำบล แสนสุข<br>
                อำเภอ เมืองชลบุรี จังหวัด ชลบุรี 20130
            </span>
        </div>

        {{-- เบอร์โทร --}}
        <div class="flex items-center translate-x-[25px]">
            <svg class="w-6 h-6 text-[#4D55A0]" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M2.003 5.884l.94-2.346A1 1 0 013.89 3h2.45a1 1 0 01.926.628l1.12 2.797a1 1 0 01-.217 1.062l-1.652 1.652a11.042 11.042 0 005.657 5.657l1.652-1.652a1 1 0 011.062-.217l2.797 1.12a1 1 0 01.628.926v2.45a1 1 0 01-.538.887l-2.346.94a2 2 0 01-1.967-.253l-2.123-1.591a15.978 15.978 0 01-7.562-7.562L2.256 7.85a2 2 0 01-.253-1.967z" />
            </svg>
            <span class=" text-gray-700">โทร 02-123-4567</span>
        </div>

        {{-- พิกัด --}}
        <span>(13.2814385, 100.9240104)</span>
        <br>
        <button onclick="copyCoords()" class="ml-2 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M8 2a2 2 0 00-2 2v1h1V4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1h-1v1h1a2 2 0 002-2V4a2 2 0 00-2-2H8z" />
                <path d="M3 6a2 2 0 012-2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V6z" />
            </svg>
        </button>
        </p>

        {{-- ปุ่มลบและแก้ไข --}}
        <div class="pt-4 flex items-center justify-between">
            <a href="#">
                <button class="w-[120px] bg-white text-black border border-black px-6 py-2 rounded-lg font-bold text-base">
                    ลบ
                </button>
            </a>
            <button
                class="w-[120px] bg-[#4D55A0] text-white border border-transparent px-6 py-2 rounded-lg font-bold text-base">
                แก้ไข
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ข้อมูลกราฟ
    const labels = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    const orderData = [1000, 5000, 10000, 20000, 15000, 8000, 50000, 30000, 25000, 40000, 27000, 35000];
    const baseData = [2000, 7000, 15000, 12000, 18000, 11000, 25000, 20000, 18000, 21000, 19000, 22000];

    const ctx = document.getElementById('salesChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'ยอดขายของสาขา',
                    data: orderData,
                    backgroundColor: '#4D55A0'
                },
                {
                    label: 'ค่ามัธยฐาน',
                    data: baseData,
                    type: 'line',
                    borderColor: 'red',
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toLocaleString()
                    }
                }
            }
        }
    });

    // อัปเดตกราฟตามช่วงเวลา
    function updateChart(months) {
        let start = 12 - months;
        chart.data.labels = labels.slice(start);
        chart.data.datasets[0].data = orderData.slice(start);
        chart.data.datasets[1].data = baseData.slice(start);
        chart.update();
    }

    // Event listeners for buttons
    document.querySelectorAll('.tab-btn').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            if (index === 0) {
                // 3 เดือน
                updateChart(3);
            } else if (index === 1) {
                // 6 เดือน
                updateChart(6);
            } else {
                // 12 เดือน (default)
                updateChart(12);
            }
        });
    });
</script>

@endsection
