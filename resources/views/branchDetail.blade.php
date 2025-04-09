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
        <div class="bg-white p-4 rounded-lg border shadow mt-4">
            <h2 class="text-xl font-bold">สาขาที่  {{ $branch->br_id }} {{ $branch->br_name }} ({{ $branch->br_code }})</h2>

            <div class="bg-white p-4 rounded-lg shadow border mt-4 flex items-start space-x-4">
                <img src="{{ $branch->manager->avatar ?? 'https://via.placeholder.com/48' }}"
                    alt="Manager Avatar"
                    class="w-12 h-12 rounded-full object-cover" />

                <div>
                    <p class="font-semibold">
                        ผู้ดูแล: {{ $branch->manager->us_fname ?? 'ไม่พบข้อมูลผู้ดูแล' }} {{ $branch->manager->us_lname ?? '' }}
                    </p>
                    <span
                        class="px-2 mt-1 border rounded-full text-xs bg-white
                        @if ($user->us_role == 'CEO') border-yellow-700 text-yellow-700
                        @elseif ($user->us_role == 'Sales Supervisor')
                            border-purple-500 text-purple-500
                        @else
                            border-blue-300 text-blue-300
                        @endif">
                        {{ $user->us_role }}
                    </span>
                    <p class="text-gray-500 text-sm">
                        ที่อยู่: ตำบล {{ $branch->br_subdistrict }} อำเภอ {{ $branch->br_district }}
                        จังหวัด {{ $branch->br_province }} {{ $branch->br_postalcode }}
                    </p>
                </div>
            </div>
        </div>


                {{-- กราฟยอดขาย --}}
                <div class="mt-4 px-4">
                    <p class="font-semibold text-lg mb-4">จำนวนยอดขาย</p>

                    <div class="w-full" style="height: 400px;">
                        <canvas id="orderMonthChart"></canvas>
                    </div>
                </div>

                {{-- ยอดรวมทั้งปี --}}
                <div class="bg-white p-4 rounded-lg border shadow mt-4 flex justify-between items-center">
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
<script>
    const ctx = document.getElementById('orderMonthChart').getContext('2d');
    
    const monthlySales = @json($orderData);  
    const labels = @json($month);  
    const monthlyMedian = @json($median);

    // ประกาศตัวแปร medianLine
    const medianLine = new Array(Object.keys(monthlySales).length).fill(monthlyMedian);

    const monthShortNames = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    const shortMonthLabels = labels.map(month => {
        const mapping = {
            'มกราคม': 0,
            'กุมภาพันธ์': 1,
            'มีนาคม': 2,
            'เมษายน': 3,
            'พฤษภาคม': 4,
            'มิถุนายน': 5,
            'กรกฎาคม': 6,
            'สิงหาคม': 7,
            'กันยายน': 8,
            'ตุลาคม': 9,
            'พฤศจิกายน': 10,
            'ธันวาคม': 11
        };
        return monthShortNames[mapping[month]]; 
    });

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: shortMonthLabels,
            datasets: [
                {
                    label: 'ยอดขายต่อเดือน',
                    data: Object.values(monthlySales),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)', 
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    order: 2,
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
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false  
                    }
                }
            },
            plugins: {
                legend: {
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
