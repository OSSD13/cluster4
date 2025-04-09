@extends('layouts.default')
@section('content')
    {{--
    @title : ทำหน้าแก้ไขข้อมูล
    @author : Worrawat Namwat 66160372 ,Samitanan Taenil 66160376
    @create date : 04/04/2568
--}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="pt-16 h-screen bg-white-100 w-full">
        {{-- ปุ่มย้อนกลับและหัวข้อ --}}
        <div class="mb-4 px-4">
            <a href="{{ route('manage.user') }}"
                class="text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <i class="fa-solid fa-arrow-left mx-3 fa-l"></i>
                แก้ไขข้อมูล
            </a>
        </div>
        {{-- ส่วนแสดงรุูปภาพ --}}
        <div class="flex space-x-2 mb-4">
            <form action="{{ route('edit.user') }}" method="post" id="editForm"
                class="w-full bg-white p-6 rounded-lg shadow-lg">
                @csrf
                @method('put')
                <div class="col-span-full">
                    <div class="mt-2 flex items-center justify-left gap-x-3">
                        <img src="{{ $users->us_image }}" class="w-10 h-10 rounded-full" alt="User Image">
                    </div>
                </div>
                {{-- ส่วนแสดงรายละเอียดของผู้ที่ต้องการจะแก้ไข --}}
                <div class="space-y-12">
                    <div class="border- border-gray-900/10 pb-5">
                        {{-- <h1 class="text-lg font-semibold text-gray-900">สมศักดิ์ รักดี</h1> --}}
                        <h1 class="text-gray-900">{{ $users->us_fname }} {{ $users->us_lname }}</h1>
                        <p class="mt-1 text-gray-600">{{ $users->us_email }}</p>
                        <hr class="my-4 border-gray-300">
                        <div class="mt- grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6">
                            <input type="hidden" name="id" value="{{ $users->us_id }}">
                            <div>
                                <label for="fname" class="block font-semibold text-gray-900">ชื่อ
                                    <span style="color: red">* </span></label>
                                <input type="text" name="fname" id="fname" value="{{ $users->us_fname }}"
                                    placeholder="กรุณาระบุชื่อ"
                                    class="mt-2 block w-full rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600"
                                    oninput="validateField('fname', 'nameError')">
                                <p id="nameError" class="text-red-500 text-sm mt-1 hidden">กรุณาระบุชื่อ</p>
                            </div>
                            <div>
                                <label for="lname" class="block font-semibold text-gray-900">นามสกุล<span
                                        style="color: red"> *
                                    </span></label>
                                <input type="text" name="lname" id="lname" value="{{ $users->us_lname }}"
                                    placeholder="กรุณาระบุนามสกุล" oninput="validateField('lname', 'lastnameError')"
                                    class="mt-2 block w-full rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600">
                                <p id="lastnameError" class="text-red-500 text-sm mt-1 hidden">กรุณาระบุนามสกุล</p>
                            </div>
                        </div>
                        {{-- ส่วนแสดงเลือกตำแหน่งที่ต้องการ --}}
                        <fieldset id="role-group" class="mt-6">
                            <legend class=" font-semibold text-gray-900">ตำแหน่ง<span style="color: red"> *
                                </span></legend>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-center gap-x-3">
                                    {{-- เช็คบทบาท Sales --}}
                                    <input id="sales" name="role" type="radio" value="Sales"
                                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        {{ $users->us_role === 'Sales' ? 'checked' : '' }}
                                        onchange="validateRole(); selectHeadField();">
                                    <label for="sales" class=" font-medium text-gray-900">Sales</label>
                                </div>
                                <div class="flex items-center gap-x-3">
                                    {{-- เช็คบทบาท Sales Supervisor --}}
                                    <input id="supervisor" name="role" type="radio" value="Sales Supervisor"
                                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        {{ $users->us_role === 'Sales Supervisor' ? 'checked' : '' }}
                                        onchange="validateRole(); selectHeadField();">
                                    <label for="supervisor" class=" font-medium text-gray-900">Sales
                                        Supervisor</label>
                                </div>
                                <div class="flex items-center gap-x-3">
                                    {{-- เช็คบทบาท CEO --}}
                                    <input id="ceo" name="role" type="radio" value="CEO"
                                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        {{ $users->us_role === 'CEO' ? 'checked' : '' }}
                                        onchange="validateRole(); selectHeadField();">
                                    <label for="ceo" class="font-medium text-gray-900">CEO</label>
                                </div>
                            </div>
                        </fieldset>
                        <p id="roleError" class="text-red-500 text-sm mt-2 hidden">กรุณาเลือกตำแหน่ง</p>
                    </div>
                </div>
                <div id="position-fields">
                    {{-- ส่วนแสดงเลือกหัวหน้างาน --}}
                    <div id="head-container" class="mt-0">
                        <label for="head" class="block font-medium text-gray-900">หัวหน้างาน<span style="color: red">
                                *</span></label>
                        {{-- Select dropdown --}}
                        <select id="head" name="head"
                            class="mt-2 block w-full rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600"
                            onchange="validateHead()">

                            {{-- Option เริ่มต้นเมื่อผู้ใช้งานไม่เลือกหัวหน้าจะแสดงจะขึ้นข้อความกรุณาเลือกหัวหน้า --}}
                            <option value="">-- กรุณาเลือกหัวหน้างาน --</option>

                            {{-- วนลูปรายชื่อหัวหน้าที่เป็น Sales Supervisor --}}
                            @foreach ($allUser as $muser)
                                @if ($muser->us_role == 'Sales Supervisor')
                                    <option value="{{ $muser->us_id }}"
                                        {{ $users->us_head == $muser->us_id ? 'selected' : '' }}>
                                        {{ $muser->us_fname }} {{ $muser->us_lname }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        {{-- Error message ถ้าไม่ได้เลือกหัวหน้า --}}
                        <p id="headError" class="text-red-500 text-sm mt-2 hidden">กรุณาเลือกหัวหน้างาน</p>
                    </div>

                    {{-- ส่วนแสดงอีเมล --}}
                    <div id="email-container" class="mt-6">
                        <label for="email" class="block font-medium text-gray-900">อีเมล<span style="color: red">
                                * </span></label>
                        <input id="email" name="email" type="email" autocomplete="email"
                            value="{{ $users->us_email }}" placeholder="กรุณาระบุอีเมล"
                            class="mt-2 block w-full rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600"
                            oninput="validateField('email', 'emailError', true)">
                        <p id="emailError" class="text-red-500 text-sm mt-1 hidden">กรุณาระบุอีเมลที่ถูกต้อง</p>
                    </div>

                </div>
                <div class="mt-6 flex items-center justify-between">
                    {{-- ปุ่มยกเลิก --}}
                    <a href="{{ route('manage.user') }}">
                        <button type="button"
                            class="w-[120px] bg-white text-black border border-black px-6 py-2 rounded-lg font-bold text-base">ยกเลิก</button>
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
@endsection

@section('scripts')
    <script>
        function validateField(id, errorId, isEmail = false) {
            const input = document.getElementById(id);
            const error = document.getElementById(errorId);
            const value = input.value.trim();
            let isValid = true;

            if (value === "") {
                isValid = false; // ถ้าเว้นว่างให้ invalid
            } else if (isEmail) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailRegex.test(value); // ตรวจอีเมล
            }

            input.classList.remove("border-green-500", "ring-green-300", "border-red-500", "ring-red-300");
            error.classList.add("hidden");

            if (!isValid) {
                input.classList.add("border-red-500", "ring-red-300");
                error.classList.remove("hidden"); // แสดง error
            } else {
                input.classList.add("border-green-500", "ring-green-300");
            }

            return isValid;
        }

        function validateRole() {
            const error = document.getElementById("roleError");
            const selected = document.querySelector('input[name="role"]:checked');
            if (!selected) {
                error.classList.remove("hidden"); // ถ้่า remove ข้อความจะแสดง error
                return false;
            }
            error.classList.add("hidden"); // ถ้าเพิ่มแล้วข้อความจะซ่อน error
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
            const isNameValid = validateField("fname", "nameError");
            const isLastValid = validateField("lname", "lastnameError");
            const isEmailValid = validateField("email", "emailError", true);
            const isRoleValid = validateRole();
            const isHeadValid = validateHead();

            return isNameValid && isLastValid && isEmailValid && isRoleValid && isHeadValid;
        }
    </script>
    <script>
        // DOMContentLoaded event ที่ทำงานทันทีเมื่อ html โหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            // กำหนดตัวแปรแล้วดึงมาจาก div id="email-container"
            const roleRadios = document.querySelectorAll('input[name="role"]');
            const headRole = document.getElementById('head-container');
            const emailUser = document.getElementById('email-container');
            const form = document.getElementById('editForm');

            // ตรวจสอบว่าผู้ใช้เลือกตำแหน่งไหน
            function selectHeadField() {
                const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
                // หากเลือก sales จะแสดงช่องฟิลด์ให่้เลือกหัวหน้างาน
                if (selectedRole === 'Sales') {
                    headRole.style.display = 'block';
                    emailUser.classList.add('mt-6'); // เพิ่มระยะห่างระหว่างบรรทัดเมื่อถูกซ่อน
                    // หากเลือกอย่างอื่น ช่องฟิลด์หัวหน้างานจะถูกซ่อน
                } else {
                    headRole.style.display = 'none';
                    emailUser.classList.remove('mt-6'); // ลบระยะห่างระหว่างบรรทัดเมื่อถูกซ่อน
                }
            }
            // ปรับช่องว่างของอีเมล
            roleRadios.forEach(radio => {
                radio.addEventListener('change', selectHeadField);
            });

            // ตรวจสอบค่าเริ่มต้นเมื่อโหลดหน้า
            selectHeadField();
            validateRole();
            validateHead();

            const successAlert = './public/alert-icon/SuccessAlert.png';
            const userAlert = './public/alert-icon/UserAlert.png';
            const errorAlert = './public/alert-icon/ErrorAlert.png';

            // เมื่อผู้ใช้งานคลิกปุ่ม "บันทึก" จะยับยั้งการส่งฟอร์มโดยใช้ e.preventDefault(); เพื่อให้สามารถแสดง sweetalert ได้
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const fname = document.getElementById('fname').value.trim();
                const lname = document.getElementById('lname').value.trim();
                const email = document.getElementById('email').value.trim();
                const selectRole = document.querySelector('input[name="role"]:checked');
                const head = document.getElementById('head').value;

                // เช๋็คเมื่อผู้ใช้งานกรอกข้อมูลไม่ครบถ้วน
                if (!validateForm()) {
                    return;
                }
                // กรอกข้อมูลครบถ้วน
                Swal.fire({
                    title: 'ยืนยันการแก้ไขบัญชี',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
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
                            form.submit();
                        });
                    }
                });
            });
        });

        // ฟังก์ชั่นสำหรับใช้ภายนอก script block
        function selectHeadField() {
            const selectedRole = document.querySelector('input[name="role"]:checked')?.value;
            const headRole = document.getElementById('head-container');
            const emailUser = document.getElementById('email-container');

            if (selectedRole === 'Sales') {
                headRole.style.display = 'block';
                emailUser.classList.add('mt-6');
            } else {
                headRole.style.display = 'none';
                emailUser.classList.remove('mt-6');
            }
            validateHead();
        }
    </script>
@endsection
