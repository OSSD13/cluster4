@extends('layouts.default')

@section('content')
    <div class="pt-16 bg-white-100 w-full">
        {{-- ปุ่มย้อนกลับและหัวข้อ --}}
        <div class="mb-4 px-4">
            <a href="{{ url()->previous() }}" class="text-white bg-indigo-600 px-4 py-3 rounded-2xl flex items-center justify-left w-full" style="background-color: #4D55A0;">
                <i class="fa-solid fa-arrow-left mr-5"></i>
                ยอดขายสาขาที่  {{ $order->od_br_id }}
            </a>
        </div>

        {{-- หัวข้อผู้ดูแลสาขา --}}
        @foreach($users as $user)
            <li class="user-item flex items-center justify-between p-2 border-b" value="{{ $user->us_role }}">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" class="user-checkbox h-5 w-5" onclick="toggleDeleteButton()">
                    <img src="{{ $user->us_image }}" class="w-10 h-10 rounded-full" alt="User Image">
                    <div>
                        <p class="font-semibold">{{ $user->us_fname }}</p>
                        <p class="text-sm text-gray-500">{{ $user->us_email }}</p>
                        <span class="text-xs px-2 py-1 rounded-full
                            @if ($user->us_role == 'CEO') bg-yellow-200 text-yellow-800
                            @elseif ($user->us_role == 'Sales Supervisor') bg-purple-200 text-purple-800
                            @else bg-blue-200 text-blue-800 @endif">
                            {{ $user->us_role }}
                        </span>
                    </div>
                </div>
            </li>
        @endforeach

        {{-- หัวข้อยอดขาย --}}
        @foreach($sales as $sale) <!-- assume $sales contains sales data -->
            <div class="mt-4 p-4 border-t border-gray-300">
                {{-- ยอดขายเดือน/ปี พุทธศักราช --}}
                <p class="font-semibold">
                    ยอดขายเดือน {{ \Carbon\Carbon::parse($sale->od_month)->format('F') }} /
                    {{ \Carbon\Carbon::parse($sale->od_month)->year + 543 }}
                </p>

                {{-- รหัสสาขา --}}
                <p>รหัสสาขา: {{ $sale->od_br_id }}</p>

                {{-- ยอดขาย --}}
                <p>จำนวนยอดขาย: {{ $sale->od_amount }} ชิ้น</p>

                {{-- ปุ่มลบ --}}
                <i class="fa fa-trash cursor-pointer" aria-hidden="true" onclick="editAlert({{ $sale->od_id }})" style="color: #778899;"></i>

                {{-- ปุ่มแก้ไข --}}
                <a href="{{ url('/edit-order', $sale->od_id) }}">
                    <button class="text-white px-4 py-2 rounded mt-2" style="background-color: #4D55A0;">แก้ไข</button>
                </a>
            </div>
        @endforeach

        {{-- หน้าแก้ไขข้อมูล --}}
        @isset($sale)
            <div class="pt-16 bg-white-100 w-full">
                {{-- แก้ไขข้อมูล --}}
                <div class="mb-4 px-4">
                    <a href="{{ url()->previous() }}" class="text-white bg-indigo-600 px-4 py-3 rounded-2xl flex items-center justify-left w-full" style="background-color: #4D55A0;">
                        <i class="fa-solid fa-arrow-left mr-5"></i>
                        แก้ไขข้อมูล
                    </a>

                    {{-- รหัสสาขา --}}
                    <p>รหัสสาขา : {{ $sale->od_br_id }} ({{ $sale->br_province }})</p>

                    {{-- เดือนของยอดขาย --}}
                    <p>ยอดขายเดือน {{ \Carbon\Carbon::parse($sale->od_month)->format('F') }} /
                    {{ \Carbon\Carbon::parse($sale->od_month)->year + 543 }}</p>

                    {{-- ยอดขาย --}}
                    <p>ยอดขาย </p>
                    <form action="{{ route('update-order', $sale->od_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="number" name="od_amount" value="{{ $sale->od_amount }}" class="border px-4 py-2 rounded mt-2">

                        {{-- ปุ่มยกเลิกและบันทึก --}}
                        <div class="flex justify-between mt-4">
                            <button class="bg-white text-black px-4 py-2 rounded border" onclick="window.history.back()">ยกเลิก</button>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        @endisset
    </div>

    {{-- Script การแจ้งเตือน --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function editAlert(od_id) {
            const branchAlert = '/public/alert-icon/BranchAlert.png';
            const deleteAlert = '/public/alert-icon/DeleteAlert.png';
            const editAlert = '/public/alert-icon/EditAlert.png';
            const errorAlert = '/public/alert-icon/ErrorAlert.png';
            const orderAlert = '/public/alert-icon/OrderAlert.png';
            const successAlert = '/public/alert-icon/SuccessAlert.png';
            const userAlert = '/public/alert-icon/UserAlert.png';

            Swal.fire({
                title: 'ยืนยันการแก้ไขข้อมูลใช่หรือไม่',
                showCancelButton: true,
                confirmButtonText: 'ใช่',
                cancelButtonText: 'ไม่',
                reverseButtons: true,
                imageUrl: deleteAlert,
                customClass: {
                    confirmButton: 'swal2-delete-custom',
                    cancelButton: 'swal2-cancel-custom',
                    title: 'no-padding-title',
                    actions: 'swal2-actions-gap',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'ดำเนินการเสร็จสิ้น',
                        confirmButtonText: 'ตกลง',
                        imageUrl: successAlert,
                        customClass: {
                            confirmButton: 'swal2-success-custom',
                            title: 'no-padding-title',
                        },
                        buttonsStyling: false
                    })
                }
            });
        }
    </script>
@endsection
