@extends('layouts.default')

@section('content')
    @php
        $currentRole = request()->query('role', 'all');
    @endphp

    @if ($userRole == "CEO")
    <div class="pt-16 bg-white w-full min-h-screen">
        {{-- ปุ่มย้อนกลับ + หัวข้อ --}}
        <div class="mb-4 px-4">
            <div class="text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <a href="#" class="mx-3">
                    <i class="fa-solid fa-arrow-left fa-l"></i>
                </a>
                รายชื่อทีมงาน
            </div>
        </div>

        {{-- ปุ่ม Filter --}}
        <div class="px-4 mb-4">
            <div class="flex flex-wrap gap-3">
                <button
                    class="filter-btn px-4 py-1.5 border rounded-full text-sm {{ $currentRole == 'all' ? 'border-[#4D55A0] text-[#4D55A0] font-semibold' : 'text-gray-600' }}"
                    value="all">
                    ทั้งหมด (<span id="count-all">{{ $countAll }}</span>)
                </button>
                <button
                    class="filter-btn px-4 py-1.5 border rounded-full text-sm {{ $currentRole == 'Sales' ? 'border-[#4D55A0] text-[#4D55A0] font-semibold' : 'text-gray-600' }}"
                    value="Sales">
                    Sales (<span id="count-sales">{{ $countSales }}</span>)
                </button>
                <button
                    class="filter-btn px-4 py-1.5 border rounded-full text-sm {{ $currentRole == 'Sales Supervisor' ? 'border-[#4D55A0] text-[#4D55A0] font-semibold' : 'text-gray-600' }}"
                    value="Sales Supervisor">
                    Sales Supervisor (<span id="count-supervisor">{{ $countSupervisor }}</span>)
                </button>
                <button
                    class="filter-btn px-4 py-1.5 border rounded-full text-sm {{ $currentRole == 'CEO' ? 'border-[#4D55A0] text-[#4D55A0] font-semibold' : 'text-gray-600' }}"
                    value="CEO">
                    CEO (<span id="count-ceo">{{ $countCEO }}</span>)
                </button>
            </div>
        </div>

        {{-- รายการผู้ใช้ --}}
        <div class="px-4 space-y-2">
            @foreach ($users as $user)
                <div
                    class="user-item flex items-center justify-between py-4 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->us_image ?? 'https://via.placeholder.com/40' }}"
                            class="w-14 h-14 rounded-full object-cover border border-gray-300 shadow-sm" alt="avatar">
                        <div>
                            <div class="font-semibold text-lg text-gray-800">{{ $user->us_fname }} {{ $user->us_lname }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $user->us_email }}</div>
                            <span
                                class="inline-block mt-1 px-2 py-0.5 border rounded-full text-xs bg-white
                                @if ($user->us_role == 'CEO') border-yellow-700 text-yellow-700
                                @elseif ($user->us_role == 'Sales Supervisor') border-purple-500 text-purple-500
                                @else border-blue-300 text-blue-300 @endif">
                                {{ $user->us_role }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('team.detail', $user->us_id) }}"
                        class="text-[#4D55A0] text-sm font-medium hover:underline">
                        รายละเอียด
                    </a>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->lastPage() > 1)
                <div class="flex justify-center items-center gap-2 mt-4 py-5">
                    {{-- Previous --}}
                    @if ($users->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-400">
                            <i class="fa-solid fa-angle-left"></i>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $users->lastPage(); $i++)
                        @if ($i == $users->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-[#4D55A0] text-white font-semibold">{{ $i }}</span>
                        @else
                            <a href="{{ $users->url($i) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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
    @elseif ($userRole == "Sales Supervisor")
    <div class="pt-16 bg-white w-full min-h-screen">
        {{-- ปุ่มย้อนกลับ + หัวข้อ --}}
        <div class="mb-4 px-4">
            <div class="text-white text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <a href="#" class="mx-3">
                    <i class="fa-solid fa-arrow-left fa-l"></i>
                </a>
                รายชื่อทีมงาน
            </div>
        </div>


        {{-- รายการผู้ใช้ --}}
        <div class="px-4 space-y-2">
            @foreach ($users as $user)
                <div
                    class="user-item flex items-center justify-between py-4 {{ $loop->last ? '' : 'border-b border-gray-200' }}">
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->us_image ?? 'https://via.placeholder.com/40' }}"
                            class="w-14 h-14 rounded-full object-cover border border-gray-300 shadow-sm" alt="avatar">
                        <div>
                            <div class="font-semibold text-lg text-gray-800">{{ $user->us_fname }} {{ $user->us_lname }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $user->us_email }}</div>
                            <span
                                class="inline-block mt-1 px-2 py-0.5 border rounded-full text-xs bg-white
                                @if ($user->us_role == 'CEO') border-yellow-700 text-yellow-700
                                @elseif ($user->us_role == 'Sales Supervisor') border-purple-500 text-purple-500
                                @else border-blue-300 text-blue-300 @endif">
                                {{ $user->us_role }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('team.detail', $user->us_id) }}"
                        class="text-[#4D55A0] text-sm font-medium hover:underline">
                        รายละเอียด
                    </a>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->lastPage() > 1)
                <div class="flex justify-center items-center gap-2 mt-4 py-5">
                    {{-- Previous --}}
                    @if ($users->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 text-gray-400">
                            <i class="fa-solid fa-angle-left"></i>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $users->lastPage(); $i++)
                        @if ($i == $users->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-[#4D55A0] text-white font-semibold">{{ $i }}</span>
                        @else
                            <a href="{{ $users->url($i) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-200">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    {{-- Next --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filterButtons = document.querySelectorAll(".filter-btn");

            filterButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const role = this.getAttribute("value");
                    const baseUrl = window.location.origin + window.location.pathname;
                    const url = role === 'all' ? baseUrl :
                        `${baseUrl}?role=${encodeURIComponent(role)}`;
                    window.location.href = url;
                });
            });
        });
    </script>
@endsection
