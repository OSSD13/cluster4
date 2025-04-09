@extends('layouts.default')
@section('content')
    <div class="pt-16 h-screen mx-auto p-4 bg-white min-h-screen w-full">

        <div class="mb-4">
            <div class="text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <a href="{{ route('manage.user') }}"><i class="fa-solid fa-arrow-left mx-3 fa-l"></i></a>
                เพิ่มบัญชีผู้ใช้
            </div>
        </div>

        <div class="flex items-center justify-center h-auto bg-white">
            <form action="{{ route('create.user') }}" method="post" id="editForm" class="w-full h-fit max-w-none bg-white">
                @csrf
                <div class=" border-gray-900/10 pb-12">
                    <div class="mt-6 grid grid-cols-1 gap-6">
                        <div>
                            <!-- ชื่อ -->
                            <div class="pb-4">
                                <label for="username" class="block text-sm font-medium text-gray-900">
                                    ชื่อ <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="username" id="username" placeholder="กรุณาระบุชื่อ"
                                    class="mt-2 block w-full rounded-md border border-slate-200 p-2 text-gray-900 outline-indigo-600"
                                    oninput="validateField('username','nameError')">

                                <p id="nameError" class="text-red-500 text-sm mt-1 hidden">กรุณากรอกชื่อ</p>

                            </div>

                            <!-- นามสกุล -->
                            <div>
                                <label for="lastname" class="block text-sm font-medium text-gray-900">
                                    นามสกุล <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="lastname" id="lastname" placeholder="กรุณาระบุนามสกุล"
                                    class="mt-2 block w-full rounded-md border border-slate-200 p-2 text-gray-900 outline-indigo-600"
                                    oninput="validateField('lastname','lastnameError')">

                                <p id="lastnameError" class="text-red-500 text-sm mt-1 hidden">กรุณากรอกนามสกุล</p>
                            </div>
                        </div>
                    </div>

                    <!-- ตำแหน่ง -->
                    <fieldset class="mt-6">
                        <legend class="text-sm font-semibold text-gray-900">
                            ตำแหน่ง <span class="text-red-500">*</span>
                        </legend>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center gap-x-3">
                                <input id="sales" name="role" type="radio" value="Sales"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    onchange="validateRole()">
                                <label for="sales" class="text-sm font-medium text-gray-900">Sales</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="supervisor" name="role" type="radio" value="Sales Supervisor"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    onchange="validateRole()">
                                <label for="supervisor" class="text-sm font-medium text-gray-900">Sales Supervisor</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="ceo" name="role" type="radio" value="CEO"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    onchange="validateRole()">
                                <label for="ceo" class="text-sm font-medium text-gray-900">CEO</label>
                            </div>
                        </div>

                        <p id="roleError" class="text-red-500 text-sm mt-1 hidden">กรุณาเลือกตำแหน่ง</p>

                    </fieldset>

                    <!-- หัวหน้างาน -->
                    <div class="mt-6" id="headContainer" class="hidden">
                        <label for="head" class="block text-sm font-medium text-gray-900">หัวหน้างาน<span
                                style="color: red"> *</span></label>
                        <select id="head" name="head"
                            class="mt-2 block w-full rounded-md border p-2 text-gray-900 outline-indigo-600"
                            onchange="validateHead()">
                            <option value="" class="text-gray-900">-- กรุณาเลือกหัวหน้างาน --</option>
                            @foreach ($allUser as $muser)
                                @if ($muser->us_role == 'Sales Supervisor')
                                    <option value="{{ $muser->us_id }}">{{ $muser->us_fname }} {{ $muser->us_lname }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        <p id="headError" class="text-red-500 text-sm mt-1 hidden">กรุณาเลือกหัวหน้างาน</p>

                    </div>

                    <!-- อีเมล -->
                    <div class="mt-6">
                        <label for="email" class="block text-sm font-medium text-gray-900">อีเมล<span
                                class="text-red-500"> *</span></label>
                        <input id="email" name="email" type="email" autocomplete="email"
                            placeholder="กรุณาระบุข้อมูล"
                            class="mt-2 block w-full rounded-md border border-slate-200 p-2 text-gray-900 outline-indigo-600"
                            oninput="validateField('email','emailError', true)">
                        <p id="emailError" class="text-red-500 text-sm mt-1 hidden">กรุณาระบุอีเมลที่ถูกต้อง</p>
                    </div>

                    <!-- ปุ่มยกเลิกและบันทึก -->
                    <div class="mt-6 flex items-center justify-between">
                        <button type="button" onclick="window.location.href='{{ route('manage.user') }}'"
                            class="w-[120px] bg-white text-black border border-black px-6 py-2 rounded-lg font-bold text-base">
                            ยกเลิก
                        </button>

                        <button type="button" onclick="confirmAddUser()"
                            class="w-[120px] bg-[#4D55A0] text-white border border-transparent px-6 py-2 rounded-lg font-bold text-base">
                            บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function validateField(id, errorId, isEmail = false) {
            const input = document.getElementById(id);
            const error = document.getElementById(errorId);
            const value = input.value.trim();
            let isValid = true;

            if (value === "") {
                isValid = false;
            } else if (isEmail) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailRegex.test(value);
            }

            input.classList.remove("border-green-500", "ring-green-300", "border-red-500", "ring-red-300");
            error.classList.add("hidden");

            if (!isValid) {
                input.classList.add("border-red-500", "ring-red-300");
                error.classList.remove("hidden");
            } else {
                input.classList.add("border-green-500", "ring-green-300");
            }

            return isValid;
        }



        function validateRole() {
            const error = document.getElementById("roleError");
            const selected = document.querySelector('input[name="role"]:checked');
            if (!selected) {
                error.classList.remove("hidden");
                return false;
            }
            error.classList.add("hidden");
            return true;
        }

        function validateHead() {
            const roleSelected = document.querySelector('input[name="role"]:checked')?.value;
            const headSelect = document.getElementById("head");
            const headError = document.getElementById("headError");

            // ถ้าไม่ใช่ Sales ไม่ต้องเช็กหัวหน้า
            if (roleSelected !== "Sales") {
                headSelect.classList.remove("border-red-500", "ring-red-300", "border-green-500", "ring-green-300");
                headError.classList.add("hidden");
                return true;
            }

            if (headSelect.value === "") {
                headSelect.classList.remove("border-green-500", "ring-green-300");
                headSelect.classList.add("border-red-500", "ring-red-300");
                headError.classList.remove("hidden");
                return false;
            } else {
                headSelect.classList.remove("border-red-500", "ring-red-300");
                headSelect.classList.add("border-green-500", "ring-green-300");
                headError.classList.add("hidden");
                return true;
            }
        }


        function validateForm() {
            const isNameValid = validateField("username", "nameError");
            const isLastValid = validateField("lastname", "lastnameError");
            const isEmailValid = validateField("email", "emailError", true);
            const isRoleValid = validateRole();
            const isHeadValid = validateHead();

            return isNameValid && isLastValid && isEmailValid && isRoleValid && isHeadValid;
        }




        // function validateName() {
        //     const input = document.getElementById("name");
        //     const errorText = document.getElementById("nameError");

        //     if (input.value.trim() === "") {
        //         input.classList.remove("border-green-500", "ring-green-300");
        //         input.classList.add("border-red-500", "ring-red-300");
        //         errorText.classList.remove("hidden");
        //     } else {
        //         input.classList.remove("border-red-500", "ring-red-300");
        //         input.classList.add("border-green-500", "ring-green-300");
        //         errorText.classList.add("hidden");
        //     }
        // }
    </script>
    <!-- โหลด SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const branchAlert = './public/alert-icon/BranchAlert.png';
        const deleteAlert = './public/alert-icon/DeleteAlert.png';
        const editAlert = './public/alert-icon/EditAlert.png';
        const errorAlert = './public/alert-icon/ErrorAlert.png';
        const orderAlert = './public/alert-icon/OrderAlert.png';
        const successAlert = './public/alert-icon/SuccessAlert.png';
        const userAlert = './public/alert-icon/UserAlert.png';

        function confirmAddUser() {
            const form = document.getElementById('editForm');

            // validateName(); // ตรวจสอบก่อน

            // const input = document.getElementById("username");
            // if (input.value.trim() === "") {
            //     return; // ไม่ต้องเปิด Swal ถ้ายังไม่ได้กรอกชื่อ
            // }
            if (!validateForm()) return;

            Swal.fire({
                title: 'ยืนยันการเพิ่มบัญชี',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                imageUrl: userAlert,
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
                        confirmButtonText: 'ตกลง',
                        imageUrl: successAlert,
                        customClass: {
                            confirmButton: 'swal2-success-custom',
                            title: 'no-padding-title',
                        },
                        buttonsStyling: false
                    }).then(() => {
                        document.getElementById('editForm').submit();
                    })
                }
            });
        }
    </script>

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'ข้อผิดพลาด',
                text: 'อีเมลนี้ถูกใช้งานแล้ว',
                confirmButtonText: 'ตกลง',
                showConfirmButton: true,
                imageUrl: errorAlert, // ใช้รูปภาพถ้าต้องการ
                customClass: {
                    confirmButton: 'swal2-cancel-custom',
                    title: 'no-padding-title',
                },
                buttonsStyling: false
            });
        </script>
    @endif


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const headContainer = document.getElementById("headContainer");
            const positionRadios = document.querySelectorAll('input[name="role"]');

            function toggleHeadSelect() {
                const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
                const headSelect = document.getElementById("head");
                const headError = document.getElementById("headError");

                if (selectedRole === "Sales") {
                    headContainer.classList.remove("hidden");
                } else {
                    headContainer.classList.add("hidden");
                    headSelect.value = "";
                    headSelect.classList.remove("border-red-500", "ring-red-300", "border-green-500",
                        "ring-green-300");
                    headError.classList.add("hidden");
                }
            }

            toggleHeadSelect();

            positionRadios.forEach(radio => {
                radio.addEventListener("change", () => {
                    toggleHeadSelect();
                    validateRole();

                });
            });
        });
    </script>
@endsection
