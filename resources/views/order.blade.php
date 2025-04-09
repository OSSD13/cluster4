@extends('layouts.default')

@section('content')
    {{--
    @title : ยอดขายทั้งหมด
    @author : Suthasinee Wongphatklang 66160379
    @create date : 04/04/2568
--}}

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <div class="pt-16 bg-white-100 min-h-screen w-full">
        {{-- หัวข้อ --}}
        <div class="mb-2 px-4">
            <label
                class="bg-[#4D55A0] text-white border-[#4D55A0] text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full pl-4">
                ยอดขายทั้งหมด
            </label>
        </div>

        {{-- ช่องค้นหา --}}
        <div id="search" class="flex space-x-2 mb-2 px-4">
            <input type="text" placeholder="พิมพ์รหัสสาขา ชื่อสาขา หรือชื่อผู้ดูแลสาขา"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        {{-- ตัวกรองบทบาทและจังหวัด --}}
        <div class="grid grid-cols-2 gap-4 mt-2 mb-2 px-4">
            <div class="relative">
                <!-- Dropdown บทบาท -->
                <button id="roleDropdownButton" onclick="roleDropdown()"
                    class="block w-full text-sm rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600 flex justify-between items-center">
                    <span id="roleDropdownButtonText">ทั้งหมด</span> <!-- ข้อความในปุ่ม -->
                    <svg id="roleDropdownIcon" class="w-2.5 h-2.5 ml-3 transition-transform duration-200" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg> <!-- ไอคอนลูกศร -->
                </button>
                <div id="roleDropdownMenu"
                    class="absolute hidden mt-2 w-45 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <!-- รายชื่อบทบาท -->
                    <ul id="roleList" class="max-h-48 overflow-y-auto text-sm text-gray-800 px-3 pb-3 space-y-1">
                        <li class="role-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectRole('ทั้งหมด')">ทั้งหมด</li>
                        <li class="role-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectRole('CEO')">CEO</li>
                        <li class="role-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectRole('Sales Supervisor')">Sales Supervisor</li>
                        <li class="role-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectRole('Sales')">Sales</li>
                    </ul>
                </div>
            </div>

            <div class="relative">
                <!-- Dropdown จังหวัด -->
                <button id="dropdownButton" onclick="provincesDropdown()"
                    class="block w-full text-sm rounded-md border-2 border-gray-300 p-2 text-gray-900 outline-indigo-600 flex justify-between items-center">
                    <span id="dropdownButtonText">จังหวัด</span> <!-- ข้อความในปุ่ม -->
                    <svg id="dropdownIcon" class="w-2.5 h-2.5 ml-3 transition-transform duration-200" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg> <!-- ไอคอนลูกศร -->
                </button>
                <div id="dropdownMenu"
                    class="absolute hidden mt-2 w-45 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <div class="p-3">
                        <input type="text" id="provinceSearch" onkeyup="filterProvinces()"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="ค้นหาจังหวัด...">
                    </div>
                    <!-- รายชื่อจังหวัด -->
                    <ul id="provinceList" class="max-h-48 overflow-y-auto text-sm text-gray-800 px-3 pb-3 space-y-1">
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('กรุงเทพมหานคร')">กรุงเทพมหานคร</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('เชียงใหม่')">เชียงใหม่</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('ขอนแก่น')">ขอนแก่น</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('นครราชสีมา')">นครราชสีมา</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('ภูเก็ต')">ภูเก็ต</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('ชลบุรี')">ชลบุรี</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('นครนายก')">นครนายก</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('ปราจีนบุรี')">ปราจีนบุรี</li>
                        <li class="province-item hover:bg-gray-100 px-2 py-1 rounded cursor-pointer"
                            onclick="selectProvince('ระยอง')">ระยอง</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ปุ่มลงยอดขาย + ยังไม่ได้ลงยอดขาย --}}
        <div class="left-0 w-full mb-2 px-4">
            <div class="flex gap-4">
                <label class="cursor-pointer w-1/2">
                    <input type="radio" name="options" class="peer hidden" checked>
                    <div
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700
                        peer-checked:bg-[#4D55A0] peer-checked:text-white
                        hover:bg-indigo-100 transition text-center">
                        ลงยอดขายแล้ว
                    </div>
                </label>

                <label class="cursor-pointer w-1/2">
                    <input type="radio" name="options" class="peer hidden">
                    <div
                        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700
                        peer-checked:bg-[#4D55A0] peer-checked:text-white
                        hover:bg-indigo-100 transition text-center">
                        ยังไม่ได้ลงยอดขาย
                    </div>
                </label>
            </div>
        </div>

        {{-- ข้อมูลยอดขาย --}}
        <div class="border border-gray-300 rounded-lg shadow-sm max-h-[340px] overflow-y-auto">
            <ul>
                @foreach ($orders as $allOrder)
                    <li class="px-4 py-4 flex items-center border-b border-gray-300">
                        <div class="flex items-center w-1/2 space-x-4">
                            <img src="{{ $allOrder->us_image }}" class="w-12 h-12 rounded-full ml-2" alt="User Image">
                            <div class="flex flex-col justify-center">
                                <div class="flex items-baseline space-x-1">
                                    <span class="text-sm font-semibold">สาขาที่ {{ $allOrder->br_id }}</span>
                                    <span class="text-xs text-gray-500">({{ $allOrder->br_code }})</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $allOrder->us_email }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end w-1/2 justify-center">
                            <span class="text-sm">ยอดขาย : {{ number_format($allOrder->od_amount, 2) }} ชิ้น</span>
                            <span class="text-xs text-gray-500">อัพเดต :
                                {{ \Carbon\Carbon::parse($allOrder->updated_at)->format('d/m/Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ฟังก์ชันเปิด-ปิด dropdown บทบาท
        function roleDropdown() {
            const menu = document.getElementById("roleDropdownMenu");
            const icon = document.getElementById("roleDropdownIcon");
            menu.classList.toggle("hidden"); // เปิด/ปิด dropdown

            // หมุนไอคอนลูกศร
            if (menu.classList.contains("hidden")) {
                icon.style.transform = "rotate(0deg)"; // ถ้า dropdown หายไป ให้หมุนกลับ
            } else {
                icon.style.transform = "rotate(180deg)"; // ถ้า dropdown แสดง ให้หมุนเป็นแนวตั้ง
            }
        }
        // ฟังก์ชันเลือกบทบาท
        function selectRole(roleName) {
            const button = document.getElementById("roleDropdownButton");
            const dropdownText = document.getElementById("roleDropdownButtonText");
            dropdownText.textContent = roleName; // เปลี่ยนข้อความของปุ่มเป็นบทบาทที่เลือก
            roleDropdown(); // ปิดเมนู dropdown หลังเลือก
        }

        // ฟังก์ชันเปิด-ปิด dropdown สำหรับจังหวัด
        function provincesDropdown() {
            const menu = document.getElementById("dropdownMenu");
            const icon = document.getElementById("dropdownIcon");
            menu.classList.toggle("hidden"); // เปิด/ปิด dropdown

            // หมุนไอคอนลูกศร
            if (menu.classList.contains("hidden")) {
                icon.style.transform = "rotate(0deg)"; // ถ้า dropdown หายไป ให้หมุนกลับ
            } else {
                icon.style.transform = "rotate(180deg)"; // ถ้า dropdown แสดง ให้หมุนเป็นแนวตั้ง
            }
        }
        // ฟังก์ชันเลือกจังหวัด
        function selectProvince(provinceName) {
            const button = document.getElementById("dropdownButton");
            const dropdownText = document.getElementById("dropdownButtonText");
            dropdownText.textContent = provinceName; // เปลี่ยนข้อความของปุ่มเป็นชื่อจังหวัดที่เลือก
            provincesDropdown(); // ปิดเมนู dropdown หลังเลือก
        }

        // ฟังก์ชันกรองรายการจังหวัด
        function filterProvinces() {
            const input = document.getElementById("provinceSearch");
            const filter = input.value.toLowerCase();
            const ul = document.getElementById("provinceList");
            const li = ul.getElementsByTagName("li");

            for (let i = 0; i < li.length; i++) {
                const txtValue = li[i].textContent || li[i].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }

        // ปิด dropdown ถ้าคลิกนอก dropdown
        document.addEventListener("click", function(e) {
            const menu = document.getElementById("dropdownMenu");
            const button = document.getElementById("dropdownButton");
            const roleMenu = document.getElementById("roleDropdownMenu");
            const roleButton = document.getElementById("roleDropdownButton");

            if (!menu.contains(e.target) && !button.contains(e.target)) {
                menu.classList.add("hidden");
                const icon = document.getElementById("dropdownIcon");
                icon.style.transform = "rotate(0deg)"; // หมุนลูกศรกลับ
            }

            if (!roleMenu.contains(e.target) && !roleButton.contains(e.target)) {
                roleMenu.classList.add("hidden");
                const icon = document.getElementById("roleDropdownIcon");
                icon.style.transform = "rotate(0deg)"; // หมุนลูกศรกลับ
            }
        });
    </script>
@endsection
