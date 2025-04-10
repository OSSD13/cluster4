@extends('layouts.default')

@section('content')
    {{--
    @title : ยอดขายทั้งหมด
    @author : Suthasinee Wongphatklang 66160379
    @create date : 04/04/2568
--}}

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <div class="pt-16 bg-white-100 min-h-screen w-full">
        {{-- หัวข้อ --}}
        <div class="mb-2 px-4">
            <label
                class="bg-[#4D55A0] text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full pl-4">
                ยอดขายทั้งหมด
            </label>
        </div>

        {{-- ช่องค้นหา --}}
        <form method="GET" action="{{ route('order') }}">
            <div id="search" class="flex space-x-2 mb-2 px-4">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="พิมพ์รหัสสาขา ชื่อสาขา หรือชื่อผู้ดูแลสาขา"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            {{-- ผู้ใช้สามารถเลือก role และ จังหวัดพร้อมกันได้ --}}
            @if (request('role'))
                {{-- role --}}
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            @if (request('province'))
                {{-- จังหวัด --}}
                <input type="hidden" name="province" value="{{ request('province') }}">
            @endif
        </form>

        {{-- ตัวกรองบทบาทและจังหวัด --}}
        <!-- Dropdown บทบาท -->
        <form method="GET" action="{{ route('order') }}" id="filterForm" class="mb-2 flex gap-2 justify-start px-4">
            <!-- ฟอร์มสำหรับเลือกบทบาท -->
            <div class="relative w-full">
                <select name="role" id="roleSelect" onchange="this.form.submit()"
                    class="block w-full rounded-md border p-1 text-gray-500 focus:outline-indigo-600">
                    <option value="">ทั้งหมด</option>
                    <option value="CEO" {{ request('role') == 'CEO' ? 'selected' : '' }}>CEO</option>
                    <option value="Sales Supervisor" {{ request('role') == 'Sales Supervisor' ? 'selected' : '' }}>Sales
                        Supervisor</option>
                    <option value="Sales" {{ request('role') == 'Sales' ? 'selected' : '' }}>Sales</option>
                </select>
            </div>

            <!-- Dropdown จังหวัด -->
            <div class="relative w-full">
                <select name="province" id="provinceSelect" onchange="this.form.submit()"
                    class="block w-full rounded-md border p-1 text-gray-500 focus:outline-indigo-600">
                    <option value="">เลือกจังหวัด</option>
                    <option value="กรุงเทพมหานคร" {{ request('province') == 'กรุงเทพมหานคร' ? 'selected' : '' }}>
                        กรุงเทพมหานคร</option>
                    <option value="เชียงใหม่" {{ request('province') == 'เชียงใหม่' ? 'selected' : '' }}>เชียงใหม่
                    </option>
                    <option value="ขอนแก่น" {{ request('province') == 'ขอนแก่น' ? 'selected' : '' }}>ขอนแก่น</option>
                    <option value="นครราชสีมา" {{ request('province') == 'นครราชสีมา' ? 'selected' : '' }}>นครราชสีมา
                    </option>
                    <option value="ภูเก็ต" {{ request('province') == 'ภูเก็ต' ? 'selected' : '' }}>ภูเก็ต</option>
                    <option value="ชลบุรี" {{ request('province') == 'ชลบุรี' ? 'selected' : '' }}>ชลบุรี</option>
                    <option value="นครนายก" {{ request('province') == 'สงขลา' ? 'selected' : '' }}>สงขลา</option>
                    <option value="ปราจีนบุรี" {{ request('province') == 'ศรีสะเกษ' ? 'selected' : '' }}>ศรีสะเกษ
                    </option>
                    <option value="ระยอง" {{ request('province') == 'ระยอง' ? 'selected' : '' }}>ระยอง</option>
                </select>
            </div>

            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
        </form>

        {{-- ปุ่มลงยอดขาย + ยังไม่ได้ลงยอดขาย --}}
        <div class="left-0 w-full mb-2 px-4">
            <div class="flex gap-2">
                <label class="cursor-pointer w-1/2">
                    <input type="radio" name="salesToggle" class="peer hidden" checked onchange="toggleSalesList('done')">
                    <div
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700
                        peer-checked:bg-[#4D55A0] peer-checked:text-white
                        hover:bg-indigo-100 transition text-center">
                        ลงยอดขายแล้ว
                    </div>
                </label>

                <label class="cursor-pointer w-1/2">
                    <input type="radio" name="salesToggle" class="peer hidden" onchange="toggleSalesList('notdone')">
                    <div
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700
                        peer-checked:bg-[#4D55A0] peer-checked:text-white
                        hover:bg-indigo-100 transition text-center">
                        ยังไม่ได้ลงยอดขาย
                    </div>
                </label>
            </div>
        </div>

        {{-- รายการที่ลงยอดขายแล้ว --}}
        <div id="done-list">
            <div class="border border-gray-300 rounded-lg shadow-sm max-h-[600px] mx-4 overflow-y-auto">
                <ul>
                    @forelse ($branchesWithSales as $branch)
                        <a href="{{ route('order.detail', ['br_id' => $branch->br_id]) }}">
                            <li class="px-4 py-4 flex items-center border-b border-gray-300">
                                <div class="flex items-center w-1/2 space-x-4">
                                    <img src="{{ $branch->us_image }}" class="w-12 h-12 rounded-full ml-2"
                                        alt="User Image">
                                    <div class="flex flex-col justify-center">
                                        <div class="flex items-baseline space-x-1 whitespace-nowrap">
                                            <span
                                                class="text-sm font-semibold block truncate max-w-[70px] md:max-w-[200px] overflow-hidden whitespace-nowrap">สาขา
                                                {{ $branch->br_name }}</span>
                                            <span class="text-xs text-gray-500">({{ $branch->br_code }})</span>
                                        </div>
                                        <span
                                            class="text-xs text-gray-500 block truncate max-w-[160px] overflow-hidden whitespace-nowrap">{{ $branch->us_email }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end w-1/2 justify-center">
                                    <span class="text-xs">ยอดขาย : {{ number_format($branch->total_sales, 2) }} ชิ้น</span>
                                    <span class="text-xs text-gray-500">อัพเดต :
                                        {{ \Carbon\Carbon::parse($branch->latest_updated_at)->format('d/m/Y') }}</span>
                                </div>
                            </li>
                        </a>
                    @empty
                        <li class="px-4 py-4 flex items-center">
                            <span class="text-sm">ไม่มีข้อมูลยอดขายในขณะนี้</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- รายการที่ยังไม่ได้ลงยอดขาย --}}
        <div id="notdone-list" class="hidden">
            <div class="border border-gray-300 rounded-lg shadow-sm max-h-[600px] mx-4 overflow-y-auto">
                <ul>
                    @forelse ($branchesWithoutSales as $branch)
                        <a
                            href="{{ route('add.order', ['br_id' => $branch->br_id, 'month' => $branch->missing_month_number]) }}">
                            <li class="px-4 py-4 flex items-center border-b border-gray-300">

                                <div class="flex items-center w-1/2 space-x-4">
                                    <img src="{{ $branch->us_image }}" class="w-12 h-12 rounded-full ml-2"
                                        alt="User Image">
                                    <div class="flex flex-col justify-center">
                                        <div class="flex items-baseline space-x-1 whitespace-nowrap">
                                            <span
                                                class="text-sm font-semibold block truncate  max-w-[70px] md:max-w-[200px] overflow-hidden whitespace-nowrap">สาขา
                                                {{ $branch->br_name }}</span>
                                            <span class="text-xs text-gray-500">({{ $branch->br_code }})</span>
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 block truncate max-w-[160px] overflow-hidden whitespace-nowrap">
                                            {{ $branch->us_email }}</div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end w-1/2 justify-center">

                                    <div class="flex items-baseline space-x-1">
                                        <span class="text-xs">ยอดขาย :</span>
                                        <span class="text-xs text-red-500">ยังไม่มีข้อมูล</span>
                                    </div>

                                    <span class="text-xs text-gray-500">เดือน : {{ $branch->od_month }}</span>
                                </div>

                            </li>
                        </a>
                    @empty
                        <li class="px-4 py-4 flex items-center">
                            <span class="text-sm">ไม่มีข้อมูลยอดขายในขณะนี้</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // เปลี่ยนหน้าตรงปุ่มลงแล้ว + ยังไม่ลง
        function toggleSalesList(status) {
            // ซ่อนทั้งสอง div
            document.getElementById('done-list').classList.add('hidden');
            document.getElementById('notdone-list').classList.add('hidden');

            // แสดงตามที่เลือกปุ่มลงยอดขายแล้วหรือยังไม่ลง
            if (status === 'done') {
                document.getElementById('done-list').classList.remove('hidden');
            } else if (status === 'notdone') {
                document.getElementById('notdone-list').classList.remove('hidden');
            }
        }
    </script>
@endsection
