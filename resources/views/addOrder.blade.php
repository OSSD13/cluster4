@extends('layouts.default')

@section('content')
    {{--
    @title : ทำหน้าเพิ่มยอดขาย
    @author : Suthasinee Wongphatklang 66160379
    @create date : 04/04/2568
--}}

    <div class="pt-16 bg-white-100 min-h-screen w-full">
        {{-- ปุ่มย้อนกลับและหัวข้อ --}}
        <div class="mb-4">
            <div class="mb-4 px-4">
                <div
                    class="text-white bg-[#4D55A0] border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full">
                    <a href="{{ route('order') }}">
                        <i class="fa-solid fa-arrow-left mx-3 fa-l"></i>
                    </a>
                    ลงยอดขาย
                </div>
            </div>

            {{-- รหัสสาขา + เดือน --}}
            <div class="mb-4 px-4 space-y-1">
                <div class="flex items-baseline space-x-1 whitespace-nowrap">
                    <label for="branchID" class="block text-lg font-bold text-gray-900">รหัสสาขา :
                        {{ $branch->br_code }}</label>
                    <span class="text-sm text-gray-500">(จ.{{ $branch->br_province }})</span>
                </div>

                {{-- แสดงเดือนที่ยังไม่ได้ลงยอด --}}
                <label for="month" class="block text-sm font-medium text-gray-900">เดือน{{ $thaiMonthName }}
                    {{ $thaiYearName }}</label>

            </div>

            {{-- ยอดขาย + ช่องกรอกข้อมูล --}}
            <form action="{{ route('storeOrder') }}" method="post" id="addAmount">
                @csrf

                {{-- ข้อมูลที่ต้องส่งแต่ไม่ให้ผู้ใช้กรอก --}}
                <input type="hidden" name="br_id" value="{{ $branch->br_id }}">
                <input type="hidden" name="month" value="{{ $thaiMonthName }}">
                <input type="hidden" name="year" value="{{ $thaiYearName }}">
                <input type="hidden" name="users" value="{{ $branch->br_us_id }}">

                <div class="mb-4 px-4 space-y-1">
                    <label for="order" class="block text-sm font-medium text-gray-900">ยอดขาย</label>
                    <input type="text" name="amount" id="addOrder"
                        value="{{ $addAmount ? $addAmount->od_amount : '' }}" placeholder="กรุณากรอกจำนวนสินค้า"
                        class="w-full px-3 py-2 border rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div class="fixed bottom-5 left-0 w-full px-10">
                    <div class="grid grid-cols-2 gap-10 mt-5 mb-5">
                        <div class="text-right">
                            <a href="{{ route('order') }}">
                                <button
                                    class="w-[120px] bg-white text-gray-600 border border-gray-600 px-6 py-2 rounded-lg font-bold text-base">
                                    ยกเลิก
                                </button>
                            </a>
                        </div>
                        <div class="text-left">
                            <button type="submit" name="confirm" id="saveButton"
                                class="w-[120px] bg-[#4D55A0] text-white border border-gray600 border-gray-600 px-6 py-2 rounded-lg font-bold text-base">
                                บันทึก
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection

    @section('scripts')
        <script>
            document.getElementById('saveButton').addEventListener('click', function() {
                event.preventDefault(); // ป้องกันการ submit ฟอร์มทันที

                // ปิดการใช้งานปุ่ม "บันทึก" เพื่อป้องกันการคลิกซ้ำ
                const saveButton = document.getElementById('saveButton');
                saveButton.disabled = true;

                const orderAlert = './public/alert-icon/OrderAlert.png';
                const successAlert = './public/alert-icon/SuccessAlert.png';

                Swal.fire({
                    title: 'ยืนยันการเพิ่มยอดขาย',
                    imageUrl: orderAlert,
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'swal2-confirm-custom',
                        cancelButton: 'swal2-cancel-custom',
                        title: 'no-padding-title',
                        actions: 'swal2-actions-gap',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ป้องกันการ submit ฟอร์มทันที
                        const form = document.getElementById('addAmount');
                        form.submit();

                        // หลังจาก submit แล้ว เปลี่ยนเส้นทางไปยังหน้า order
                        setTimeout(function() {
                            Swal.fire({
                                title: 'ดำเนินการเสร็จสิ้น',
                                imageUrl: successAlert,
                                confirmButtonText: 'ตกลง',
                                reverseButtons: true,
                                customClass: {
                                    confirmButton: 'swal2-confirm-custom',
                                    title: 'no-padding-title',
                                    actions: 'swal2-actions-gap',
                                },
                            }).then(() => {
                                // ไปยังหน้า order หลังจากที่ยืนยันเสร็จสิ้น
                                window.location.href =
                                    '{{ route('order') }}'; // หรือใช้ URL ของหน้า order
                            });
                        });
                    }
                });
            });
        </script>
    @endsection
