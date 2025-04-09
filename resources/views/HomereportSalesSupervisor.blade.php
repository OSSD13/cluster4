@extends('layouts.default')

@section('content')
    <div class="pt-16 bg-white min-h-screen px-4 space-y-4 mb-16">

        {{-- หัวข้อ --}}
        <div>
            <label class="bg-[#4D55A0] text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full pl-4">
                รายงาน
            </label>
        </div>

        {{-- Tabs --}}
        <div class="flex justify-between text-sm font-medium text-center text-gray-500 border-b border-gray-200">
            <a class="w-1/2 py-2 border-b-2 border-[#4D55A0] text-[#4D55A0] font-semibold">
                สาขาของฉัน
            </a>
            <a class="w-1/2 py-2" href="{{ route('reportSale_sup2') }}">
                สาขาพนักงาน
            </a>
        </div>

        {{-- ยอดรวม --}}
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-lg font-bold">ยอดรวม</h2>

            {{-- เลือกปี --}}
            <form method="GET" action="{{ route('report_SalesSupervisor') }}">
                <select name="year"
                    class="text-sm font-semibold px-4 py-1 rounded-md border border-[#CAC4D0] focus:outline-none"
                    onchange="this.form.submit()">
                    @for ($y = 2566; $y <= now()->year + 543; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>ปี {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>

        {{-- กล่องยอดขาย --}}
        <div class="bg-white shadow-md rounded-2xl p-4 flex justify-between items-center border border-gray-200">
            <div>
                <p class="text-sm text-gray-600">ยอดขายรวมทั้งหมดของฉันในปี {{ $year }}</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ number_format($totalSales) }} ชิ้น
                    {{-- ถ้าจะเพิ่มเปอร์เซ็นต์เติบโต ก็ใส่ <span> ตรงนี้ได้ --}}
                </p>
                {{-- ถ้าอยากแสดงค่าเฉลี่ยรายเดือน: --}}
                <p class="text-sm mt-1 text-[#4169E1]">
                    ค่าเฉลี่ยรายเดือนอยู่ที่ {{ number_format($totalSales / 12, 2) }} ชิ้น
                </p>
            </div>
            <div class="text-2xl">
                <i class="fa-solid fa-box fa-2xl" style="color: #4d55a0"></i>
            </div>
        </div>

        {{-- หัวข้อกราฟ --}}
        <div class="flex justify-between items-center mt-6">
            <h3 class="text-md font-bold">กราฟยอดขาย</h3>
        </div>

        {{-- กราฟ --}}
        <div class="bg-white rounded-lg shadow p-4" style="height: auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-md font-bold">ยอดขายของฉันในปีนี้</h3>
            </div>
            <div class="w-full h-[400px]">
                <canvas id="orderTotalChart"></canvas>
            </div>
        </div>

        {{-- กล่องจำนวนสาขา --}}
        <div class="bg-white shadow-md rounded-2xl p-4 border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm">จำนวนสาขา</p>
                    <p class="text-2xl text-gray-800">{{ $branchCount }} สาขา</p>
                    <p class="text-sm mt-1" style="color: #279C27">จำนวนสาขาเพิ่มขึ้นเฉลี่ย {{ $branchGrowthPercent }} %</p>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fa-solid fa-warehouse text-4xl" style="color: #4d55a0;"></i>
                    <a href="{{ route('branchMyMap') }}">
                        <button class="btn btn-warning text-[#4169E1]">ดูเพิ่มเติม</button>
                    </a>
                </div>
            </div>

            <div class="mt-4 border-t border-gray-200 pt-4">

                <div class="flex justify-between items-center mb-2">
                    <p class="text-md">ยอดขายรวมมากที่สุด (ภายในปี)</p>
                    <p class="text-md text-right">จากปีที่แล้ว</p>
                </div>

                <div class="space-y-2">
                    @foreach ($branchSales as $index => $branch)
                        <div class="flex justify-between items-center">
                            <!-- สาขาอยู่ซ้าย -->
                            <p class="font-bold w-1/3">{{ 'สาขาที่ ' . ($index + 1) }}</p>

                            <!-- ยอดขายอยู่กลาง -->
                            <div class="w-1/3 text-center">
                                <p class="text-lg font-semibold">{{ number_format($branch['sales']) }} ชิ้น</p>
                            </div>

                            <!-- % อยู่ขวา -->
                            <div class="w-1/3 text-right {{ $branch['growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                <span class="font-semibold">{{ $branch['growth'] >= 0 ? '+' : '' }}{{ $branch['growth'] }}
                                    %</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <script>
        const monthlySales = @json($completeOrderData->values()); // ข้อมูลยอดขายรายเดือน
        const labels = @json($thaiMonths); // ชื่อเดือนที่ใช้เป็น labels สำหรับกราฟ
        const monthlyMedian = @json(array_values($medain)); // ค่ามัธยฐานสำหรับกราฟ

        const ctxOrder = document.getElementById('orderTotalChart').getContext('2d');

        // หาค่ามากสุดจากยอดขายในเดือนต่างๆ
        const maxSales = Math.max(...Object.values(monthlySales));
        const maxValue = Math.pow(10, Math.ceil(Math.log10(maxSales)));

        const salesChart = new Chart(ctxOrder, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
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
                responsive: true, // ยืดหยุ่นตามขนาดหน้าจอ
                maintainAspectRatio: false, // คงอัตราส่วนของกราฟ
                layout: {
                    padding: {
                        top: 20, // เพิ่ม padding ด้านบนเพื่อไม่ให้กราฟถูกตัด
                        bottom: 20 // เพิ่ม padding ด้านล่างเพื่อไม่ให้กราฟถูกตัด
                    }
                },
                plugins: {
                    legend: {
                        display: false // ไม่แสดง legend ในกราฟ
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        type: 'linear',
                        min: 0,
                        ticks: {
                            autoSkip: false,
                            stepSize: Math.ceil(maxSales / 10),
                            callback: function(value) {
                                return Math.floor(value).toLocaleString(); // แสดงตัวเลขเต็ม (ไม่มีทศนิยม)
                            }
                        },
                        grid: {
                            drawTicks: true,
                            drawOnChartArea: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false
                        },
                        grid: {
                            drawOnChartArea: false,
                            color: 'rgba(0, 0, 0, 0.1)' // เพิ่มความเข้มของเส้นแนวตั้งแต่ละเดือน
                        }
                    }
                }
            }
        });

        // สร้าง custom legend ที่ฝั่งขวาระดับเดียวกับหัวข้อ
        const legendContainer = document.querySelector('.flex.justify-between.items-center.mb-4');
        if (legendContainer) {
            // สร้าง element ใหม่สำหรับ legend
            const legendElement = document.createElement('div');
            legendElement.className = 'flex items-center gap-4';

            // เพิ่ม legend สำหรับแต่ละ dataset
            salesChart.data.datasets.forEach(dataset => {
                const color = dataset.backgroundColor || dataset.borderColor;
                const legendItem = document.createElement('div');
                legendItem.className = 'flex items-center gap-1';
                legendItem.innerHTML = `
                <span class="w-3 h-3 rounded-full inline-block" style="background-color: ${typeof color === 'object' ? color : color};"></span>
                <span class="text-sm">${dataset.label}</span>
            `;
                legendElement.appendChild(legendItem);
            });

            // แทนที่หรือเพิ่ม legend ไปที่ container
            const existingLegend = legendContainer.querySelector('.custom-legend');
            if (existingLegend) {
                legendContainer.replaceChild(legendElement, existingLegend);
            } else {
                legendContainer.appendChild(legendElement);
            }
        }
    </script>
@endsection
