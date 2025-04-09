@extends('layouts.default')

@section('content')
    {{--
    @title : ทำหน้าเพิ่มยอดขาย
    @author : Suthasinee Wongphatklang 66160379
    @create date : 04/04/2568
--}}

    <div class="pt-16 bg-white-100 min-h-screen w-full">
        {{-- ปุ่มย้อนกลับและหัวข้อ --}}
        <div class="mb-4 px-4">
            <div class="mb-4 px-4">
                <a href="{{ route('order') }}"
                    class="text-white bg-[#4D55A0] border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full">
                    <i class="fa-solid fa-arrow-left mx-3 fa-l"></i>
                    ลงยอดขาย
                </a>
            </div>

            {{-- รหัสสาขา + เดือน --}}
            <div class="mb-4 px-4 space-y-3">
                <label for="branchID" class="block text-lg font-bold text-gray-900">รหัสสาขา : </label>
                <label for="month" class="block text-sm font-medium text-gray-900">เดือน</label>
            </div>

            {{-- ยอดขาย + ช่องกรอกข้อมูล --}}
            <div class="mb-4 px-4 space-y-4">
                <label for="order" class="block text-sm font-medium text-gray-900">ยอดขาย</label>
                <input type="text" name="addOrder" id="addOrder" placeholder="กรุณากรอกจำนวนสินค้า"
                    class="w-full px-3 py-2 border rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            {{-- ปุ่มยกเลิก + ปุ่มบันทึก --}}
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
        </div>
    @endsection

    @section('scripts')
        <script>
            document.getElementById('saveButton').addEventListener('click', function() {
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
                        });
                    }
                });
            });
        </script>
    @endsection
