{{--
    @title : ดูข้อมูลสาขาทั้งหมด Role CEO
    @author : Samitanan Taenil 66160376
    @create date : 05/04/2568
--}}
@extends('layouts.default')
@section('content')
    <div class="pt-16 bg-white-100 w-full">
        {{-- ปุ่มย้อนกลับและหัวข้อ --}}
        <div class="mb-4 px-4">
            <div class="text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <a href="{{ route('report_CEO') }}" class="mx-3 text-white">
                    <i class="fa-solid fa-arrow-left fa-l"></i>
                </a>
                <span>สาขาของทั้งหมด (ปี {{ now()->year + 543 }})</span>
            </div>
        </div>

        {{-- ช่องค้นหา --}}
        <form method="GET" action="{{ route('branchMyMap') }}">
            <div id="search" class="flex space-x-2 mb-2 px-4 ">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="พิมพ์รหัสสาขา"
                    class="block w-full rounded-md border-2 border-gray-300 px-3 py-2 focus:outline-indigo-400">
            </div>

            {{-- ผู้ใช้สามารถเลือกจังหวัดและเรียงยอดขายของสาที่ทำรายได้มากที่สุด --}}
            @if (request('province'))
                {{-- จังหวัด --}}
                <input type="hidden" name="province" value="{{ request('province') }}">
            @endif
            @if (request('sort'))
                {{-- เรียงยอดขาย --}}
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
        </form>


        {{-- ผู้ใช้งานสามารถเลือกจังหวัดที่ต้องการจะดูได้ --}}
        <form method="GET" action="{{ route('branchMyMap') }}" class="mb-4 flex gap-2 justify-start px-4">
            <div class="relative w-full">
                <select name="province" id="provinceSelect" onchange="this.form.submit()"
                    class="block w-full rounded-md border-2 border-gray-300 p-2 text-gray-400 focus:outline-indigo-400">
                    <option value="">เลือกจังหวัด</option>
                    <option value="กรุงเทพมหานคร" {{ request('province') == 'กรุงเทพมหานคร' ? 'selected' : '' }}>
                        กรุงเทพมหานคร</option>
                    <option value="เชียงใหม่" {{ request('province') == 'เชียงใหม่' ? 'selected' : '' }}>เชียงใหม่</option>
                    <option value="ขอนแก่น" {{ request('province') == 'ขอนแก่น' ? 'selected' : '' }}>ขอนแก่น</option>
                    <option value="นครราชสีมา" {{ request('province') == 'นครราชสีมา' ? 'selected' : '' }}>นครราชสีมา
                    </option>
                    <option value="ภูเก็ต" {{ request('province') == 'ภูเก็ต' ? 'selected' : '' }}>ภูเก็ต</option>
                    <option value="ชลบุรี" {{ request('province') == 'ชลบุรี' ? 'selected' : '' }}>ชลบุรี</option>
                    <option value="นครนายก" {{ request('province') == 'นครนายก' ? 'selected' : '' }}>นครนายก</option>
                    <option value="ปราจีนบุรี" {{ request('province') == 'ปราจีนบุรี' ? 'selected' : '' }}>ปราจีนบุรี
                    </option>
                    <option value="ระยอง" {{ request('province') == 'ระยอง' ? 'selected' : '' }}>ระยอง</option>
                </select>
            </div>

            {{-- เลือกเรียงยอดขายที่ต้องการจะแสดง เช่น ยอดขายมากที่สุดและยอดขายน้อยที่สุด --}}
            {{-- onchange คือ event ที่ทำงานเมื่อผู้ใช้เปลี่ยนค่าที่เลือกใน <select> --}}
            {{-- this.form.submit() คือ JavaScript ที่สั่งให้ "ฟอร์มที่ <select> นี้อยู่ในนั้น" ทำการ submit ทันที
--}}
            <select name="sort" onchange="this.form.submit()"
                class="block w-full rounded-md border-2 border-gray-300 p-2 text-gray-400  outline-indigo-400 flex justify-between items-center">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>ยอดขายมากที่สุด</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>ยอดขายน้อยที่สุด</option>
            </select>

            {{-- เก็บค่า search parameter ถ้ามีการใช้งาน --}}
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
        </form>

        {{-- วนลูปจำนวนสาขาที่มีข้อมูล --}}
        <div id="branch-container">
            @foreach ($paginatedBranches as $branch)
                <div class="bg-white rounded-md p-4 mb-4 border-2 border-gray-300 mx-4 ">
                    <p class="text-lg font-semibold text-gray-800">{{ $branch->br_name }}
                        ({{ $branch->br_code }})
                    </p>
                    <div class="flex items-center mt-1">
                        <img src="{{ $branch->us_image }}" alt="Photo User" class="w-12 h-12 rounded-full mr-3 ">
                        <div>
                            <p class="text-gray-700 font-medium">ผู้ดูแล {{ $branch->manager->us_fname ?? '-' }}
                                {{ $branch->manager->us_lname ?? '-' }}</p>
                            <p class="text-gray-700 font-medium mt-1">
                                ตำแหน่ง
                                @php
                                    $role = $branch->manager->us_role ?? '-';
                                    $roleColor = match ($role) {
                                        'CEO' => 'text-yellow-700 border-yellow-700',
                                        'Sales Supervisor' => 'text-purple-600 border-purple-400',
                                        'Sales' => 'text-blue-500 border-blue-300',
                                        default => 'text-gray-500 border-gray-300',
                                    };
                                @endphp

                                <span class="px-2 py-1 border text-xs rounded-full bg-white {{ $roleColor }}">
                                    {{ $role }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <div class="flex">
                            <div class="w-[60px] h-[1px]"></div>
                            <p class="font-medium  text-blue-800"> ยอดรวม {{ number_format($branch->total_sales) }} ชิ้น
                            </p>
                        </div>
                        <a href="{{ route('branchMyMap', $branch->br_id) }}"
                            class="font-semibold text-sm text-blue-800">ดูเพิ่มเติม</a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ปุ่มกดแสดงหน้าถัดไป --}}
        @if (isset($totalPages) && $totalPages > 1)
            <div class="flex justify-center mt-4 mb-6">
                <nav class="inline-flex space-x-2">
                    {{-- หน้าก่อนหน้านี้ --}}
                    <a href="{{ request()->fullUrlWithQuery(['page' => max(1, $page - 1), 'search' => $search, 'sort' => $sort, 'province' => $province]) }}"
                        class="w-10 h-10 flex items-center justify-center rounded-full {{ $page <= 1 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    {{-- หน้าแรก --}}
                    @if ($page > 3)
                        <a href="{{ request()->fullUrlWithQuery(['page' => 1, 'search' => $search, 'sort' => $sort, 'province' => $province]) }}"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                            1
                        </a>

                        @if ($page > 4)
                            <span class="w-10 h-10 flex items-center justify-center">...</span>
                        @endif
                    @endif

                    {{-- หมายเลขหน้า --}}
                    @for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i, 'search' => $search, 'sort' => $sort, 'province' => $province]) }}"
                            class="w-10 h-10 flex items-center justify-center rounded-full {{ $i == $page ? 'bg-blue-800 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    {{-- หน้าสุดท้าย --}}
                    @if ($page < $totalPages - 2)
                        @if ($page < $totalPages - 3)
                            <span class="w-10 h-10 flex items-center justify-center">...</span>
                        @endif

                        <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages, 'search' => $search, 'sort' => $sort, 'province' => $province]) }}"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                            {{ $totalPages }}
                        </a>
                    @endif

                    {{-- ปุ่มหน้าถัดไป --}}
                    <a href="{{ request()->fullUrlWithQuery(['page' => min($totalPages, $page + 1), 'search' => $search, 'sort' => $sort, 'province' => $province]) }}"
                        class="w-10 h-10 flex items-center justify-center rounded-full {{ $page >= $totalPages ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </nav>
            </div>
        @endif
    </div>
@endsection
