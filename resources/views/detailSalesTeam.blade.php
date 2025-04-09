@extends('layouts.default')

@section('content')
    <div class="pt-16 bg-white-100 w-full">
        <div class="mb-4 px-4">
            <a href="{{ route('manage.user') }}"
                class="text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <i class="fa-solid fa-arrow-left mx-3 fa-l"></i>
                แก้ไขข้อมูล
            </a>
        </div>
    </div>
    <div class="mb-4 px-4">
        <img src="{{ $user->us_image }}" class="w-12 h-12 rounded-full" alt="User Image">
        <div>
            <p class="font-semibold">{{ $user->us_fname }} {{ $user->us_lname }}</p>
            <p class="text-sm text-gray-500">{{ $user->us_email }}</p>
            <span
                class="px-2 mt-1 border rounded-full text-xs bg-white
                @if ($user->us_role == 'CEO') border-yellow-700 text-yellow-700
                @elseif ($user->us_role == 'Sales Supervisor')
                    border-purple-500 text-purple-500
                @else
                    border-blue-300 text-blue-300 @endif">
                {{ $user->us_role }}
            </span>
        </div>
    </div>
@endsection
