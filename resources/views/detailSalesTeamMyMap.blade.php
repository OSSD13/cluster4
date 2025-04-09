    @extends('layouts.default')

    @section('content')
       <div class="pt-14 bg-white min-h-screen">
            {{-- ปุ่มกลับ --}}
            <div class="mb-4 px-4">
                <a href="{{ route('team') }}"
                    class="text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                    style="background-color: #4D55A0;">
                    <i class="fa-solid fa-arrow-left mx-3"></i>
                    รายชื่อทีมงาน
                </a>
            </div>

            {{-- ข้อมูลผู้ใช้ --}}
            <div class="bg-white px-6">
                <div class="relative flex flex-col items-start pl-6">
                    <div class="w-full border-t border-gray-200 absolute top-12 z-0 left-0"></div>

                    <img src="{{ $user->us_image }}" class="w-24 h-24 rounded-full object-cover z-10 bg-white"
                        alt="User Image">

                    <p class="mt-4 text-lg font-semibold">{{ $user->us_fname }} {{ $user->us_lname }}</p>
                    <p class="text-sm text-gray-500">{{ $user->us_email }}</p>
                </div>

                <div class="mt-4 border-t border-gray-200">
                    <div class="py-4 flex justify-between text-sm">
                        <span class="font-semibold">ตำแหน่ง</span>
                        <span>{{ $user->us_role }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <div class="py-4 flex justify-between text-sm">
                        <span class="font-semibold">วันที่เพิ่ม</span>
                        <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <div class="py-4 flex justify-between text-sm">
                        <span class="font-semibold">หัวหน้า</span>
                        <span>{{ $user->us_head }}</span>
                    </div>
                </div>
            </div>

            {{-- รายการสาขาที่เปิด --}}
            <div class="bg-white border border-gray-200 rounded-2xl m-3 p-5 mb-2 shadow">
                <h2 class="font-bold text-center py-4">
                    สาขาที่เปิด <span class="text-gray-500">( จำนวน {{ $branches->total() }} สาขา )</span>
                </h2>

                {{-- ตารางหัว --}}
                <div class="grid grid-cols-2 text-sm font-semibold border-b border-gray-300">
                    <div class="px-4 py-2">ชื่อสาขา</div>
                    <div class="px-4 py-2 text-right">วันที่เพิ่ม</div>
                </div>

                {{-- รายการสาขา --}}
                @foreach ($branches as $branch)
                    <div class="grid grid-cols-2 text-sm border-b border-gray-200">
                        <div class="px-4 py-3">
                            {{ $branch->br_name }} ({{ $branch->br_code }})<br>
                            <span class="text-gray-500 text-xs">จังหวัด : {{ $branch->br_province }}</span>
                        </div>
                        <div class="px-4 py-3 flex items-center justify-end text-right">
                            {{ \Carbon\Carbon::parse($branch->created_at)->format('d/m/Y') }}
                        </div>
                    </div>
                @endforeach

                @if ($branches->lastPage() > 1)
                    <div class="flex justify-center items-center gap-2 mt-4">
                        {{-- Previous --}}
                        @if ($branches->onFirstPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-400">
                                <i class="fa-solid fa-angle-left"></i>
                            </span>
                        @else
                            <a href="{{ $branches->previousPageUrl() }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        @endif

                        {{-- Page numbers --}}
                        @for ($i = 1; $i <= $branches->lastPage(); $i++)
                            @if ($i == $branches->currentPage())
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-[#4D55A0] text-white font-semibold">{{ $i }}</span>
                            @else
                                <a href="{{ $branches->url($i) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next --}}
                        @if ($branches->hasMorePages())
                            <a href="{{ $branches->nextPageUrl() }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        @else
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-400">
                                <i class="fa-solid fa-angle-right"></i>
                            </span>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    @endsection
