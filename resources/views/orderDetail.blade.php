@extends('layouts.default')

@section('content')
    <div class="pt-16 h-screen mx-auto p-4 bg-white min-h-screen w-full">

        <div class="mb-4">
            <a href="{{ url('') }}" 
                class="text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <i class="fa-solid fa-arrow-left mx-3"></i>
                สาขาของฉัน (ปี {{ now()->year + 543 }})
            </a>
        </div>

        <div class="w-full bg-white">
            {{-- ข้อมูลสาขา --}}
            <div class="bg-white p-4 rounded-lg shadow mt-4">
                <h2 class="text-xl font-bold">สาขาที่ {{ $branch->br_id }} ({{ $branch->br_code }})</h2>

                <div class="bg-white p-4 rounded-lg shadow mt-4 flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                    <div>
                        <p class="font-semibold">ผู้ดูแล: {{ $branch->manager->us_fname ?? 'ไม่พบข้อมูลผู้ดูแล' }} {{ $branch->manager->us_lname ?? '' }}</p>
                        <span class="px-2 mt-1 border rounded-full text-xs bg-white">
                            {{ $branch->manager->us_role ?? 'ไม่พบข้อมูลบทบาท' }}
                        </span>
                        <p class="text-gray-500 text-sm">ที่อยู่: ตำบล {{ $branch->br_subdistrict}} อำเภอ {{ $branch->br_district}} จังหวัด {{ $branch->br_province}} {{ $branch->br_postalcode}}</p>
                    </div>
                </div>

                {{-- กราฟยอดขาย --}}
                <div class="mb-8 px-4">
                    <p class="font-semibold text-lg mb-4">จำนวนยอดขาย</p>

                    <div class="w-full" style="height: 200px;">
                        <canvas id="orderMonthChart"></canvas>
                    </div>
                </div>

                {{-- ยอดรวมทั้งปี --}}
                <div class="bg-white p-4 rounded-lg shadow mt-4 flex justify-between items-center">
                    <span class="font-semibold">ยอดรวมทั้งปี</span>
                    <span class="text-xl font-bold">
                        {{ $totalSales ?? 0 }} ชิ้น
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('orderMonthChart').getContext('2d');
    
        // ดึงข้อมูลจาก PHP
        const orderMonth = {!! json_encode(array_values($growthRates)) !!};
    
        // หาค่าต่ำสุดและค่าสูงสุดจากข้อมูลใน orderMonth
        const maxValue = Math.max(...orderMonth);  // หาค่าสูงสุดจากข้อมูลใน orderMonth
        const minValue = orderMonth.length > 1 ? Math.min(...orderMonth) : 0;  // ถ้ามีข้อมูลมากกว่าหนึ่งตัว ใช้ค่าต่ำสุด ถ้ามีข้อมูลแค่ตัวเดียวให้เป็น 0

        // คำนวณค่ามัธยฐาน
        const sortedData = [...orderMonth].sort((a, b) => a - b);
        const median = (sortedData.length % 2 === 0)
            ? (sortedData[sortedData.length / 2 - 1] + sortedData[sortedData.length / 2]) / 2
            : sortedData[Math.floor(sortedData.length / 2)];

        const orderMonthChart = new Chart(ctx, {  
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($growthRates)) !!},  // ใช้ค่าเดือนจาก PHP
                datasets: [
                    {
                        label: 'ยอดขายทั้งหมด',
                        data: orderMonth,  // ใช้ข้อมูลจาก PHP ที่ชื่อใหม่ว่า orderMonth
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#3B82F6',
                        borderWidth: 2
                    },
                    {
                        label: 'มัธยฐาน',
                        data: Array(orderMonth.length).fill(median),  // สร้างค่ามัธยฐานให้กับทุกจุด
                        borderColor: '#FF5733',  
                        backgroundColor: '#FF5733',  
                        fill: false,
                        tension: 0,
                        borderWidth: 2,
                        borderDash: [5, 5],  // เส้นประ
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: minValue,
                        max: maxValue,
                        ticks: {
                            stepSize: Math.ceil((maxValue - minValue) / 5),
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        grid: {
                            color: '#E5E7EB',
                            borderDash: [5, 5]
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    </script>
@endsection
