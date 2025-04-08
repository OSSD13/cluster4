@extends('layouts.default')

@section('content')
<div class="pt-16 bg-white-100 w-full flex flex-col min-h-screen">
    {{-- ปุ่มย้อนกลับและหัวข้อ --}}
    <div class="mb-4 px-4">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center text-white bg-[#4D55A0] px-4 py-3 rounded-2xl">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            <span>แก้ไขข้อมูล</span>
        </a>
    </div>

    {{-- ส่วนเนื้อหา --}}
    <div class="px-4 flex-grow">
        {{-- ข้อมูลสาขา --}}
        <p class="mb-2 font-bold uppercase text-xl">
            รหัสสาขา : {{ $order->branch->br_code }} (จ.{{ $order->branch->br_province ?? '-' }})
        </p>
        <p class="mb-2 text-lg">
            ยอดขายเดือน {{ $order->od_month }} {{ $order->od_year }}
        </p>

        {{-- ฟอร์มแก้ไขยอดขาย --}}
        <form id="updateForm" action="{{ route('update.order', $order->od_id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="od_amount" class="block mb-1 text-sm font-medium">ยอดขาย</label>
                <input
                    type="text"
                    id="od_amount"
                    name="od_amount"
                    placeholder="{{ $order->od_amount }}"
                    pattern="[0-9]+"
                    inputmode="numeric"
                    class="border border-gray-300 px-2 py-1 rounded w-full text-sm"

                />
            </div>
            <div class="mt-6 flex items-center justify-between">
                {{-- ปุ่มยกเลิก --}}
                <a href="{{ route('manage.user') }}">
                    <button type="button"
                        class="w-[120px] bg-white text-black border border-black px-6 py-2 rounded-lg font-bold text-base">
                        ยกเลิก
                    </button>
                </a>
                {{-- ปุ่มบันทึก --}}
                <button type="submit"
                    class="w-[120px] bg-[#4D55A0] text-white border border-transparent px-6 py-2 rounded-lg font-bold text-base">
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = '/public/alert-icon/SuccessAlert.png';
    const EditAlert = '/public/alert-icon/EditAlert.png';
    const userAlert = '/public/alert-icon/UserAlert.png';
    const errorAlert = '/public/alert-icon/ErrorAlert.png';

    const form = document.getElementById('updateForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // หยุดการส่งฟอร์ม

        // รับค่าจากช่องกรอกยอดขาย
        const odAmount = document.getElementById('od_amount').value.trim();

        // ตรวจสอบว่ากรอกยอดขายหรือไม่
        if (!odAmount) {
            Swal.fire({
                title: 'กรุณากรอกยอดขาย',
                confirmButtonText: 'ตกลง',
                imageUrl: errorAlert,
                customClass: {
                    confirmButton: 'swal2-confirm-custom',
                    title: 'no-padding-title',
                    actions: 'swal2-actions-gap'
                },
                buttonsStyling: false
            });
            return;
        }

        // แสดง SweetAlert เพื่อยืนยันการแก้ไขข้อมูล
        Swal.fire({
            title: 'ยืนยันการแก้ไขยอดขาย',
            showCancelButton: true,
            confirmButtonText: 'ใช่',
            cancelButtonText: 'ไม่',
            reverseButtons: true,
            imageUrl: EditAlert,
            customClass: {
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom',
                title: 'no-padding-title',
                actions: 'swal2-actions-gap'
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
                        title: 'no-padding-title'
                    },
                    buttonsStyling: false
                }).then(() => {
                    // ส่งฟอร์มจริงหลังจาก SweetAlert ปิดลง
                    form.submit();
                });
            }
        });
    });
});
</script>
@endsection
