{{--
    @title : ทำหน้าแผนที่ ฟังก์ชันในแผนที่ทั้งหมด
    @author : Pakkapon Chomchoey 66160080
    @create date : 21/02/2568
--}}

<div>
    <livewire:search-bar />
    <div id='map' wire:ignore style="width: 100vw; min-height: 100dvh"></div>
    {{-- add branch form --}}
    <div wire:ignore.self id="locationForm"
        class="hidden fixed inset-0 top-[50px] h-[calc(100dvh-50px)] bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="w-full h-full bg-white p-6 pb-0 px-4 rounded-md shadow-lg overflow-y-auto">
            {{-- header --}}
            <div class="text-white border-[#4D55A0] px-4 text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <button id="backBtnForm"><i class="fa-solid mr-3 fa-arrow-left" style="color: #ffffff;"></i></button>
                เพิ่มสถานที่
            </div>

            {{-- tab --}}
            <div class="border rounded-lg shadow my-4 mb-0">
                <div class="grid grid-cols-2">
                    <button class="tab-link px-6 py-3 rounded-t-lg border-r-2 transition-all duration-300 bg-white"
                        onclick="_openTab(event, 'branchTab')">
                        สาขา
                    </button>
                    <button class="tab-link px-6 py-3 rounded-t-lg border-l-2 transition-all duration-300 bg-gray-200"
                        onclick="_openTab(event, 'otherTab')">
                        อื่นๆ
                    </button>
                </div>

                {{-- tab content --}}{{-- tab branch --}}
                <form id="branchTab" wire:submit.prevent="validateBranchForm"
                    class="pb-normal tab-content pt-5 px-7 pb-32 scrollbar-ios max-h-[80vh] overflow-y-auto overscroll-contain scrollbar-hidden scroll-container bg-white rounded-b-lg">
                    <label class="block mb-1">ชื่อสาขา <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nameBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุชื่อสาขา">
                    @error('nameBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">รหัสสาขา</label>
                    <input type="text" wire:model="codeBranch" class="border p-2 w-full bg-gray-100 rounded-md"
                        placeholder="รหัสสาขาจะแสดงหลังจากกรอกรหัสไปรษณีย์" readonly>
                    @error('codeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phoneBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุเบอร์โทรศัพท์">
                    @error('phoneBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">บ้านเลขที่ <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="addressBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุบ้านเลขที่">
                    @error('addressBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">รหัสไปรษณีย์ <span class="text-red-500">*</span></label>
                    <div wire:ignore>
                        <input type="text" wire:model="zipcodeBranch" id="zipcode"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุรหัสไปรษณีย์">
                    </div>
                    @error('zipcodeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">จังหวัด</label>
                    <div wire:ignore>
                        <input type="text" wire:model="provinceBranch" id="province"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุจังหวัด">
                    </div>
                    @error('provinceBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">อำเภอ</label>
                    <div wire:ignore>
                        <input type="text" wire:model="amphoeBranch" id="amphoe"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุอำเภอ">
                    </div>
                    @error('amphoeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">ตำบล</label>
                    <div wire:ignore>
                        <input type="text" wire:model="districtBranch" id="district"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุตำบล">
                    </div>
                    @error('districtBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror

                    <div class="w-full mt-4 mx-auto">
                        <label for="images"
                            class="block w-full border rounded-lg p-6 text-center cursor-pointe hover:bg-gray-200">
                            <div wire:loading wire:target="imageBranch">
                                <p class="text-sm mb-2 text-gray-500">กำลังโหลดรูปภาพ...</p>
                            </div>
                            @if ($imageBranch)
                                <div class="flex flex-wrap justify-center space-x-2 mb-3">
                                    @foreach ($imageBranch as $image)
                                        <img class="w-15 h-15 rounded-md" src="{{ $image->temporaryUrl() }}">
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex flex-col items-center justify-center">
                                <span class=""><i class="fa-solid fa-image fa-2xl"
                                        style="color: #808080;"></i></span>
                                <p class="text-gray-500 mt-2">อัปโหลดรูปภาพ</p>
                            </div>
                            <input type="file" wire:model="imageBranch" multiple id="images" multiple
                                class="hidden">
                            @error('imageBranch.*')
                                <small class="text-red-600">{{ $message }}</small>
                            @enderror
                            @error('imageBranch')
                                <small class="text-red-600">{{ $message }}</small>
                            @enderror
                        </label>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-4">
                        <span class="text-left">ลองจิจูด</span><span
                            class="text-left">{{ $long }}</span><span></span>
                        <span class="text-left">ละติจูด</span><span
                            class="text-left">{{ $lat }}</span><span></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <span class="content-center">
                            ขอบเขตของสาขา
                        </span>
                        <div class=" items-center justify-center">
                            <div class="relative group">
                                <div id="dropdown-button"
                                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
                                    <span class="mr-2" wire:key="scope">{{ $scope }} กิโลเมตร</span>
                                    <span class="justify-center"><i class="fa-solid fa-chevron-down"></i></span>
                                </div>
                                <div id="dropdown-menu"
                                    class="hidden absolute bottom-full right-0 mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1">
                                    <input id="search-input"
                                        class="block w-full px-4 py-2 text-gray-800 border rounded-md  border-gray-300 focus:outline-none"
                                        type="text" placeholder="เพิ่มขอบเขต" autocomplete="off">
                                    <a href="#" data-value="5"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">5
                                        กิโลเมตร</a>
                                    <a href="#" data-value="10"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">10
                                        กิโลเมตร</a>
                                    <a href="#" data-value="15"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">15
                                        กิโลเมตร
                                    </a>
                                    <a href="#" data-value="20"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">20
                                        กิโลเมตร
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span><i class="fa-solid fa-house" style="color: #4D55A0;"></i></span><span
                            class="ml-2">สถานที่ใกล้เคียง</span>
                    </div>
                    @if (count($nearbyPreview) > 0)
                        <div class="mt-2 space-y-2">
                            @foreach ($nearbyPreview as $place)
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <span class="text-blue-600 ">{{ $place['name'] }}</span>
                                    <span class="text-gray-600">{{ $place['distance'] }} กม.</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mt-2">ยังไม่มีข้อมูลสถานที่ใกล้เคียง</p>
                    @endif

                    <div class="flex justify-center mt-4">
                        <button type="submit" style="background-color: #4D55A0;"
                            class="w-[120px] bg-[#4D55A0] text-white border border-transparent px-6 py-2 rounded-lg font-bold text-base">บันทึก</button>
                    </div>
                </form>
                {{-- tab other --}}
            </div>
        </div>
    </div>

    {{-- Bottom Sheet Madal Branch --}}
    <div id="bottomDrawer"
        class="fixed inset-0 top-[51px] h-[calc(100dvh-51px)] touch-pan-y overscroll-contain flex flex-col justify-end opacity-0 pointer-events-none transition-opacity duration-300">

        <div id="drawerContent"
            class="relative bg-white w-full mx-auto rounded-t-2xl shadow-lg transition-all duration-300 overflow-hidden max-h-screen"
            style="height: 35vh;">
            <div class="flex justify-center items-center ">
                <div id="drawerHandle" class="relative w-full py-2 cursor-pointer flex justify-center">
                    <div class="w-14 h-1.5 bg-gray-400 rounded-full"></div>
                </div>
            </div>
            {{-- Header --}}
            <div id="name-branch"
                class="text-white border-[#4D55A0] tabToScroll px-4 text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
            </div>

            {{-- Tab Header --}}
            <div class="flex border-b mx-3 mt-4">
                <button id="detail-branch-btn"
                    class="w-1/2 py-2 text-center font-semibold  border-b-2 text-[#4D55A0] border-[#4D55A0]">ข้อมูล</button>
                <button id="nearby-location-btn"
                    class="w-1/2 py-2 text-center font-semibold text-black">สถานที่ใกล้เคียง</button>
            </div>

            <div id="data-detail-branch"
                class="overflow-y-auto max-h-[calc(100dvh-4rem)] scrollbar-hidden px-4 pb-20">
                {{-- Chart + Summary --}}
                <div class="p-4 space-y-4">
                    {{-- <h2 class="text-lg font-bold">ยอดขายในปีนี้</h2> --}}
                    {{-- Mock Chart --}}
                    {{-- <img src="https://quickchart.io/chart?c={type:'bar',data:{labels:['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],datasets:[{label:'ยอดขายของสาขา',data:[1000,4000,9000,12000,11000,8000,15000,10000,9000,8500,7000,6000],backgroundColor:'#4F46E5'},{label:'ค่าเฉลี่ยฐาน',type:'line',data:[800,3000,7000,9000,10000,9500,13000,8500,8800,7500,6500,6200],borderColor:'#EF4444'}]}}"
                        alt="chart" class="rounded-lg"> --}}

                    {{-- Filter Tabs --}}
                    {{-- <div class="flex justify-center gap-2 mt-2">
                        <button class="px-4 py-1 border rounded-full text-sm text-gray-600">3 เดือน</button>
                        <button class="px-4 py-1 border rounded-full text-sm text-gray-600">6 เดือน</button>
                        <button class="px-4 py-1 border rounded-full text-sm text-white bg-indigo-600">12
                            เดือน</button>
                    </div> --}}

                    {{-- Summary Card --}}
                    <div class="bg-white shadow-md border p-4 rounded-xl flex justify-between items-center mt-4">
                        <div>
                            <p class="mb-2">จำนวนออเดอร์ทั้งหมดของปีนี้</p>
                            <p class="text-xl font-bold text-black" id="order-branch"></p>
                        </div>
                        <div class="">
                            <i class="fa-solid fa-box fa-2xl" style="color: #4D55A0;"></i>
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-4 mt-2">
                    <div class="flex-grow h-0.5 bg-gray-300"></div>
                    <span class="px-3 text-gray-500 text-sm">ผู้ดูแล</span>
                    <div class="flex-grow h-0.5 bg-gray-300"></div>
                </div>

                {{-- Manager Info --}}
                <div class="flex justify-center items-center gap-4">
                    <img id="manager-image" class="w-12 h-12 rounded-full" />
                    <div>
                        <p id="manager-name" class="text-black"></p>
                    </div>
                </div>

                <div class="flex items-center my-4">
                    <div class="flex-grow h-0.5 bg-gray-300"></div>
                </div>
                <div class="px-4 pb-2 space-y-3">

                    <p id="code-branch" class="font-bold text-lg text-black"></p>
                    <div class="flex items-start text-sm text-gray-800 gap-4">
                        <span><i class="fa-solid fa-location-dot" style="color: #4D55A0;"></i></span>
                        <p id="address-branch"></p>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-800">
                        <span><i class="fa-solid fa-phone" style="color: #4D55A0;"></i></span>
                        <p id="phone-branch"></p>
                    </div>
                    <div id="latlong-branch" class="text-sm text-blue-600 mt-2"></div>
                </div>
                @if (auth()->user()->us_role == 'CEO' || auth()->user()->us_role == 'Sales Supervisor')
                    <div class="grid grid-cols-2 gap-10 mt-5 mb-5">
                        <div class="text-right">
                            <button id="deleteBranchBtn"
                                class="w-[120px] bg-white text-gray-600 border border-gray-600 px-6 py-2 rounded-lg font-bold text-base">
                                ลบ
                            </button>
                        </div>
                        <div class="text-left">
                            <button id="editBranchBtn"
                                class="w-[120px] bg-white text-gray-600 border border-gray-600 px-6 py-2 rounded-lg font-bold text-base">
                                แก้ไข
                            </button>
                        </div>
                    </div>
                @endif
                {{-- Nearby Places --}}
                {{-- <div class="border-t mt-2 px-4 pb-6">
                    <div class="space-y-3">
                        @foreach ([['name' => 'มหาวิทยาลัยบูรพา', 'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/13/Burapha_University_logo.svg/2048px-Burapha_University_logo.svg.png', 'distance' => '1'], ['name' => 'สถาบันวิทยาศาสตร์ทางทะเล', 'image' => 'https://www.bims.buu.ac.th/marine/wp-content/uploads/2020/07/logo-1-1.png', 'distance' => '1.2'], ['name' => 'แหลมทอง บางแสน', 'image' => 'https://media.timeout.com/images/105240238/image.jpg', 'distance' => '1.5'], ['name' => 'หาดบางแสน', 'image' => 'https://www.paiduaykan.com/travel/wp-content/uploads/2019/04/3-1.jpg', 'distance' => '1.7']] as $place)
                            <div class="bg-white rounded-xl shadow flex items-center overflow-hidden">
                                <img src="{{ $place['image'] }}" class="w-24 h-24 object-cover" />
                                <div class="p-3">
                                    <p class="font-semibold text-black truncate w-52">{{ $place['name'] }}</p>
                                    <p class="text-sm text-gray-500">ระยะทาง {{ $place['distance'] }} กิโลเมตร</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
            </div>

            <div id="nearby-branch" class="mt-5 overflow-y-auto max-h-[calc(100vh-250px)] px-4">
                <!-- รายการสถานที่ -->
            </div>
            <div id="nearby-pagination" class="px-4 mb-4 mt-2 flex justify-center gap-2">
                <!-- ปุ่ม pagination -->
            </div>

        </div>
    </div>

    {{-- Edit Location --}}
    <div wire:ignore.self id="editBranchForm"
        class="hidden fixed inset-0 top-[50px] h-[calc(100dvh-50px)] bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="w-full h-full bg-white p-6 pb-0 px-4 rounded-md shadow-lg overflow-y-auto">
            {{-- header --}}
            <div class="text-white border-[#4D55A0] px-4 text-2xl font-extrabold py-3 rounded-2xl flex items-center w-full"
                style="background-color: #4D55A0;">
                <button id="backBtnEditBranchForm"><i class="fa-solid mr-3 fa-arrow-left"
                        style="color: #ffffff;"></i></button>
                แก้ไขสาขา
            </div>

            {{-- tab --}}
            <div class="border rounded-lg shadow my-4 mb-0">
                <div class="grid ">
                    <div class="text-center py-2 border-b-2">
                        <label for="">สาขา</label>
                    </div>
                </div>

                {{-- tab content --}}{{-- tab branch --}}
                <form wire:submit.prevent="validateEditBranchForm"
                    class="pb-normal tab-content pt-5 px-7 scrollbar-ios max-h-[80vh] overflow-y-auto overscroll-contain scrollbar-hidden scroll-container bg-white rounded-b-lg">
                    <label class="block mb-1">ชื่อสาขา <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nameBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุชื่อสาขา">
                    @error('nameBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">รหัสสาขา</label>
                    <input type="text" wire:model="codeBranch" class="border p-2 w-full bg-gray-100 rounded-md"
                        placeholder="รหัสสาขาจะแสดงหลังจากกรอกรหัสไปรษณีย์" readonly>
                    @error('codeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phoneBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุเบอร์โทรศัพท์">
                    @error('phoneBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">บ้านเลขที่ <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="addressBranch" class="border p-2 w-full rounded-md"
                        placeholder="กรุณาระบุบ้านเลขที่">
                    @error('addressBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">รหัสไปรษณีย์ <span class="text-red-500">*</span></label>
                    <div wire:ignore>
                        <input type="text" wire:model="zipcodeBranch" id="zipcodeEdit"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุรหัสไปรษณีย์">
                    </div>
                    @error('zipcodeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">จังหวัด</label>
                    <div wire:ignore>
                        <input type="text" wire:model="provinceBranch" id="provinceEdit"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุจังหวัด">
                    </div>
                    @error('provinceBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">อำเภอ</label>
                    <div wire:ignore>
                        <input type="text" wire:model="amphoeBranch" id="amphoeEdit"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุอำเภอ">
                    </div>
                    @error('amphoeBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror
                    <label class="block mt-4 mb-1">ตำบล</label>
                    <div wire:ignore>
                        <input type="text" wire:model="districtBranch" id="districtEdit"
                            class="border p-2 w-full rounded-md" placeholder="กรุณาระบุตำบล">
                    </div>
                    @error('districtBranch')
                        <small class="text-red-600">{{ $message }}</small>
                    @enderror

                    <div class="w-full mt-4 mx-auto">
                        <label for="images"
                            class="block w-full border rounded-lg p-6 text-center cursor-pointe hover:bg-gray-200">
                            <div wire:loading wire:target="imageBranch">
                                <p class="text-sm mb-2 text-gray-500">กำลังโหลดรูปภาพ...</p>
                            </div>
                            @if ($imageBranch)
                                <div class="flex flex-wrap justify-center space-x-2 mb-3">
                                    @foreach ($imageBranch as $image)
                                        <img class="w-15 h-15 rounded-md" src="{{ $image->temporaryUrl() }}">
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex flex-col items-center justify-center">
                                <span class=""><i class="fa-solid fa-image fa-2xl"
                                        style="color: #808080;"></i></span>
                                <p class="text-gray-500 mt-2">อัปโหลดรูปภาพ</p>
                            </div>
                            <input type="file" wire:model="imageBranch" multiple id="images" multiple
                                class="hidden">
                            @error('imageBranch.*')
                                <small class="text-red-600">{{ $message }}</small>
                            @enderror
                            @error('imageBranch')
                                <small class="text-red-600">{{ $message }}</small>
                            @enderror
                        </label>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mt-4">
                        <span class="text-left">ลองจิจูด</span><span
                            class="text-left">{{ $long }}</span><span></span>
                        <span class="text-left">ละติจูด</span><span
                            class="text-left">{{ $lat }}</span><span></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <span class="content-center">
                            ขอบเขตของสาขา
                        </span>
                        <div class=" items-center justify-center">
                            <div class="relative group">
                                <div id="dropdown-button-edit"
                                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">
                                    <span class="mr-2" wire:key="scope">{{ $scope }} กิโลเมตร</span>
                                    <span class="justify-center"><i class="fa-solid fa-chevron-down"></i></span>
                                </div>
                                <div id="dropdown-menu-edit"
                                    class="hidden absolute bottom-full right-0 mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1">
                                    <input id="search-input-edit"
                                        class="block w-full px-4 py-2 text-gray-800 border rounded-md  border-gray-300 focus:outline-none"
                                        type="text" placeholder="เพิ่มขอบเขต" autocomplete="off">
                                    <a href="#" data-value="5"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">5
                                        กิโลเมตร</a>
                                    <a href="#" data-value="10"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">10
                                        กิโลเมตร</a>
                                    <a href="#" data-value="15"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">15
                                        กิโลเมตร
                                    </a>
                                    <a href="#" data-value="20"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">20
                                        กิโลเมตร
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span><i class="fa-solid fa-house" style="color: #4D55A0;"></i></span><span
                            class="ml-2">สถานที่ใกล้เคียง</span>
                    </div>

                    <div class="flex justify-center mt-4">
                        <button type="submit" style="background-color: #4D55A0;"
                            class="w-[120px] bg-[#4D55A0] text-white border border-transparent px-6 py-2 rounded-lg font-bold text-base">บันทึก</button>
                    </div>
                </form>
                {{-- tab other --}}
            </div>
        </div>
    </div>

    {{-- <div id="bottomDrawer"
        class="fixed bottom-0 left-0 right-0 z-50 w-full max-h-[90vh] h-[50vh] translate-y-full overflow-y-auto bg-white transition-transform duration-300 rounded-t-2xl shadow-lg">
        <div class="w-12 h-1 bg-gray-400 mx-auto rounded my-2 cursor-pointer" id="drawerHandle"></div>
        <div class="px-4 pb-6">
            <h2 class="text-xl font-semibold" id="drawerTitle">ชื่อสาขา</h2>
            <p class="text-gray-600" id="drawerAddress">ที่อยู่</p>
            <div class="flex flex-wrap gap-2 mt-4" id="drawerImages">
                <!-- Images -->
            </div>
            <h2 class="text-xl font-semibold" id="drawerManager">ชื่อผู้ดูแล</h2>
            <p class="text-gray-600" id="drawerManagerEmail">อีเมล</p>
            <h2 class="text-xl font-semibold" id="drawerManagerPhone">เบอร์โทรศัพท์</h2>
        </div>
    </div> --}}

</div>

<script>
    tailwind.config = {
        theme: {
            extend: {},
        },
        safelist: [
            'touch-pan-y',
            'overscroll-contain',
        ]
    }
</script>

{{-- แก้ฟอร์มบัค --}}
<script>
    const formElement = document.getElementById("branchTab");

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            const isKeyboardOpen = window.visualViewport.height < window.innerHeight - 100;

            if (isKeyboardOpen) {
                formElement.classList.remove('pb-normal');
                formElement.classList.add('pb-safe');
            } else {
                formElement.classList.remove('pb-safe');
                formElement.classList.add('pb-normal');
            }
        });
    }

    document.querySelectorAll('#branchTab input, #branchTab textarea').forEach(input => {
        input.addEventListener('focus', () => {
            formElement.classList.remove('pb-normal');
            formElement.classList.add('pb-safe');
        });

        input.addEventListener('blur', () => {
            setTimeout(() => {
                formElement.classList.remove('pb-safe');
                formElement.classList.add('pb-normal');
            }, 300);
        });
    });
</script>

{{-- AutoFill --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $.Thailand({
            $district: $('#districtEdit'),
            $amphoe: $('#amphoeEdit'),
            $province: $('#provinceEdit'),
            $zipcode: $('#zipcodeEdit'),
        });
        $.Thailand({
            $district: $('#district'),
            $amphoe: $('#amphoe'),
            $province: $('#province'),
            $zipcode: $('#zipcode'),
        });
        document.addEventListener("livewire:init", function() {
            $(document).ready(function() {
                $('#zipcode').on('change', function() {
                    let zipcode = $(this).val();
                    Livewire.dispatch('updateZipBranch', {
                        zipcode: zipcode
                    });
                    Livewire.dispatch('generateCodeBranch');
                });

                $('#province').on('change', function() {
                    let province = $(this).val();
                    Livewire.dispatch('updateProBranch', {
                        province: province
                    })
                })

                $('#amphoe').on('change', function() {
                    let amphoe = $(this).val();
                    Livewire.dispatch('updateAmpBranch', {
                        amphoe: amphoe
                    })
                })

                $('#district').on('change', function() {
                    let district = $(this).val();
                    Livewire.dispatch('updateDisBranch', {
                        district: district
                    })
                })
            });
        });
    });
</script>

{{-- Dropdown Add Branch --}}
<script>
    document.addEventListener("livewire:init", function() {
        $(document).ready(function() {
            const dropdownButton = $('#dropdown-button');
            const dropdownMenu = $('#dropdown-menu');
            const searchInput = $('#search-input');
            let isOpen = false;

            function _toggleDropdown(forceOpen = null) {
                if (forceOpen !== null) {
                    isOpen = forceOpen;
                } else {
                    isOpen = !isOpen;
                }
                dropdownMenu.toggleClass('hidden', !isOpen);
            }

            // Set initial state
            _toggleDropdown();

            dropdownButton.on('click', () => {
                $('#temp-item').remove();
                _toggleDropdown();
            });

            searchInput.on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                let found = false;

                dropdownMenu.find('a').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchTerm) || searchTerm === '') {
                        $(this).show();
                        found = true;
                    } else {
                        $(this).hide();
                    }
                    if (!found && searchTerm.match(/^\d+$/)) {
                        $('#temp-item').remove();
                        dropdownMenu.append(`
                            <a href="#" id="temp-item" data-value="${searchTerm}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">
                                ${searchTerm} กิโลเมตร
                            </a>
                        `);
                    }
                    if (searchTerm === '') {
                        $('#temp-item').remove();
                    }
                });
            });

            //เมื่อกด enter
            searchInput.on('keypress', function(event) {
                if (event.which === 13) { // Enter Key
                    event.preventDefault();
                    let newValue = $(this).val().trim();

                    if (newValue.match(/^\d+$/)) {
                        Livewire.dispatch('updateScope', {
                            scope: newValue
                        });
                        _toggleDropdown(false);
                    }
                }
            });

            $(document).on('click', '#dropdown-menu a', function() {
                let selectValue = $(this).data('value');
                console.log(selectValue);

                Livewire.dispatch('updateScope', {
                    scope: selectValue
                });

                _toggleDropdown(false);
            });
        })
    })
</script>

{{-- Dropdown Edit Branch --}}
<script>
    document.addEventListener("livewire:init", function() {
        $(document).ready(function() {
            const dropdownButton = $('#dropdown-button-edit');
            const dropdownMenu = $('#dropdown-menu-edit');
            const searchInput = $('#search-input-edit');
            let isOpen = false;

            function _toggleDropdown(forceOpen = null) {
                if (forceOpen !== null) {
                    isOpen = forceOpen;
                } else {
                    isOpen = !isOpen;
                }
                dropdownMenu.toggleClass('hidden', !isOpen);
            }

            // Set initial state
            _toggleDropdown();

            dropdownButton.on('click', () => {
                $('#temp-item-edit').remove();
                _toggleDropdown();
            });

            searchInput.on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                let found = false;

                dropdownMenu.find('a').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchTerm) || searchTerm === '') {
                        $(this).show();
                        found = true;
                    } else {
                        $(this).hide();
                    }
                    if (!found && searchTerm.match(/^\d+$/)) {
                        $('#temp-item-edit').remove();
                        dropdownMenu.append(`
                            <a href="#" id="temp-item-edit" data-value="${searchTerm}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 active:bg-blue-100 cursor-pointer rounded-md">
                                ${searchTerm} กิโลเมตร
                            </a>
                        `);
                    }
                    if (searchTerm === '') {
                        $('#temp-item-edit').remove();
                    }
                });
            });

            //เมื่อกด enter
            searchInput.on('keypress', function(event) {
                if (event.which === 13) { // Enter Key
                    event.preventDefault();
                    let newValue = $(this).val().trim();

                    if (newValue.match(/^\d+$/)) {
                        Livewire.dispatch('updateScope', {
                            scope: newValue
                        });
                        _toggleDropdown(false);
                    }
                }
            });

            $(document).on('click', '#dropdown-menu-edit a', function() {
                let selectValue = $(this).data('value');
                console.log(selectValue);

                Livewire.dispatch('updateScope', {
                    scope: selectValue
                });

                _toggleDropdown(false);
            });
        })
    })
</script>

{{-- Add Location Tab --}}
<script>
    function _openTab(event, tabId) {
        $('.tab-content').each(() => {
            $('.tab-content').addClass('hidden');
        });

        $('#' + tabId).removeClass('hidden');

        $('.tab-link').each(() => {
            $('.tab-link').removeClass('bg-white').addClass('bg-gray-200');
        });

        $(event.target).addClass('bg-white').removeClass('bg-gray-200');
    }
</script>

{{-- SweetAlert2 --}}
<script>
    const branchAlert = './public/alert-icon/BranchAlert.png';
    const deleteAlert = './public/alert-icon/DeleteAlert.png';
    const editAlert = './public/alert-icon/EditAlert.png';
    const errorAlert = './public/alert-icon/ErrorAlert.png';
    const orderAlert = './public/alert-icon/OrderAlert.png';
    const successAlert = './public/alert-icon/SuccessAlert.png';
    const userAlert = './public/alert-icon/UserAlert.png';

    // function _testAlert() {
    //     Livewire.dispatch('branch-added-alert');
    // }

    document.addEventListener('livewire:init', () => {
        //Alert Confirm Add Branch
        Livewire.on('showConfirmCreateBranch', () => {
            Swal.fire({
                title: 'ยืนยันการเพิ่มสาขา',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                imageUrl: branchAlert,
                customClass: {
                    confirmButton: 'swal2-confirm-custom',
                    cancelButton: 'swal2-cancel-custom',
                    title: 'no-padding-title',
                    actions: 'swal2-actions-gap',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('create-branch');
                }
            });
        });

        //Alert Success Add branch
        Livewire.on('branch-added-alert', () => {
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
                $('#locationForm').addClass('hidden');
            });
        })

        //Alert Error
        Livewire.on('error', (message) => {
            console.log(message);
            Swal.fire({
                title: message,
                confirmButtonText: 'ตกลง',
                imageUrl: errorAlert,
                customClass: {
                    confirmButton: 'swal2-success-custom',
                    title: 'no-padding-title',
                },
                buttonsStyling: false
            });
        });

        //Alert Delete Branch Success
        Livewire.on('branch-deleted-alert', () => {
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
                _closeDrawer();
                marker?.remove();
                marker = null;
            });
        });

        //Alert Confirm Edit Branch
        Livewire.on('showConfirmEditBranch', () => {
            Swal.fire({
                title: 'ยืนยันการแก้ไขสาขา',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                imageUrl: editAlert,
                customClass: {
                    confirmButton: 'swal2-confirm-custom',
                    cancelButton: 'swal2-cancel-custom',
                    title: 'no-padding-title',
                    actions: 'swal2-actions-gap',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('save-edit-branch');
                }
            });
        });

        Livewire.on('branch-edit-alert', () => {
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
                $('#editBranchForm').addClass('hidden');
                Livewire.dispatch('update-edit-sheet-branch');
            });
        });

        //Alert Confirm Delete Branch
        _confirmDelete = (branchCode, branchId) => {
            Swal.fire({
                title: 'ยืนยันการลบสาขา',
                html: `
                        <p class="text-sm text-gray-700">พิมพ์รหัสสาขา “<strong>${branchCode}</strong>” ในช่องด้านล่างเพื่อยืนยันการลบสาขา</p>
                        <input id="branchCodeInput" class="border p-2 w-full text-center rounded-md mt-4" placeholder="โปรดใส่รหัสสาขาเพื่อยืนยัน">
                        <p id="inputError"></p>
                    `,
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                imageUrl: deleteAlert,
                customClass: {
                    confirmButton: 'swal2-delete-custom',
                    cancelButton: 'swal2-cancel-custom',
                    title: 'no-padding-title',
                    actions: 'swal2-actions-gap',
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const input = $('#branchCodeInput').val().trim();
                    if (input !== branchCode) {
                        $('#inputError').text('กรอกรหัสสาขาไม่ถูกต้อง').addClass(
                            'text-red-500 mt-2 text-sm');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete-branch', {
                        id: branchId
                    });
                }
            });
        }
    });
</script>

{{-- Bottom Sheet Madal Branch --}}
<script>
    const drawer = document.getElementById("bottomDrawer");
    const drawerContent = document.getElementById("drawerContent");
    const drawerHandle = document.getElementById("drawerHandle");

    let isDragging = false,
        startY, startHeight;

    let dataBranch;

    function _openDrawer(data, latLong) {
        dataBranch = null;
        $('#manager-name').empty();
        $('#manager-email').text('');
        $('#manager-image').attr('src', '');
        $('#code-branch').empty();
        $('#address-branch').empty();
        $('#phone-branch').empty();
        $('#latlong-branch').empty();
        $('#name-branch').empty();
        $('#order-branch').empty();

        drawer.classList.remove("opacity-0", "pointer-events-none");
        drawer.classList.add("opacity-100");
        drawerContent.classList.remove("fullscreen");
        drawerContent.style.height = "35vh";

        // const tabButtons = document.querySelectorAll("#bottomDrawer .flex.border-b button");
        // if (tabButtons.length === 2) {
        //     tabButtons[0].classList.add("text-[#4D55A0]", "border-[#4D55A0]", "border-b-2");
        //     tabButtons[1].classList.remove("text-[#4D55A0]", "border-[#4D55A0]", "border-b-2");
        // }

        drawerContent.scrollTop = 0;

        dataBranch = data;
        // Set data
        $('#manager-name').html(
            `${data.manager_name} <span id="manager-role" class="text-black">( ${data.manager_role} )</span>`);
        $('#manager-image').attr('src', data.manager_image);
        // $('#manager-image').attr('src', data.manager_image + '?t=' + Date.now());
        $('#code-branch').html(`รหัสสาขา : <span class="text-black font-bold"> ${data.code} </span>`);
        $('#address-branch').text(data.address);
        $('#phone-branch').text(data.phone);
        $('#latlong-branch').html(
            `(${latLong[1].toFixed(5)}, ${latLong[0].toFixed(5)}) <i class="ml-1 fa-solid fa-copy" style="color: #4D55A0;"></i>`
        );
        $('#name-branch').text(data.name);
        console.log(data.orders);
        let orders = data.orders;
        orders = JSON.parse(orders);
        let totalAmount = orders.reduce((sum, item) => sum + (item.amount ?? 0), 0);

        $('#order-branch').html(`${totalAmount} ชิ้น`);
        // const images = Array.isArray(data.image) ? data.image : JSON.parse(data.image);
        // const imageHtml = images.map(img => `<img src="${img}" class="w-24 h-24 object-cover rounded" />`).join(
        //     '');
        // document.getElementById("drawerImages").innerHTML = imageHtml;
    }

    function openNearbyTab(branchId) {
        $('#data-detail-branch').addClass('hidden');
        $('#nearby-branch').removeClass('hidden');
        $('#nearby-location-btn').removeClass('text-black').addClass('border-b-2 text-[#4D55A0] border-[#4D55A0]');
        $('#detail-branch-btn').removeClass('border-b-2 text-[#4D55A0] border-[#4D55A0]');

        if (dataBranch) {
            fetchNearby(dataBranch.id, 1);
        }
    }



    $(document).on('click', '#nearby-location-btn', function(e) {
        if (dataBranch) {
            openNearbyTab(dataBranch.id); // 🚀 แบบไม่ผ่าน Livewire
        }
    })


    $(document).on('click', '#detail-branch-btn', function() {
        $('#nearby-branch').addClass('hidden');
        $('#data-detail-branch').removeClass('hidden');
        $('#detail-branch-btn').removeClass('text-black').addClass(
            'border-b-2 text-[#4D55A0] border-[#4D55A0]');
        $('#nearby-location-btn').removeClass('border-b-2 text-[#4D55A0] border-[#4D55A0]');
    })

    $(document).on('click', '.tabToScroll', function() {
        const currentHeight = parseFloat(drawerContent.style.height);
        if (currentHeight < 100) {
            updateDrawerHeight(100);
        } else {
            updateDrawerHeight(35);
        }
    });

    $(document).off('click', '#deleteBranchBtn').on('click', '#deleteBranchBtn', function() {
        if (dataBranch) {
            _confirmDelete(dataBranch.code, dataBranch.id);
        }
    });

    $(document).off('click', '#editBranchBtn').on('click', '#editBranchBtn', function() {
        if (dataBranch) {
            Livewire.dispatch('edit-branch', {
                id: dataBranch.id
            });
            $('#editBranchForm').removeClass('hidden');
        }
    });

    $(document).off('click', '#backBtnEditBranchForm').on('click', '#backBtnEditBranchForm', function() {
        $('#editBranchForm').addClass('hidden');
        Livewire.dispatch('clearEditBranchForm');
    });

    function _closeDrawer() {
        drawerContent.classList.remove("fullscreen");
        drawerContent.style.height = "35vh";
        drawer.classList.remove("opacity-100");
        drawer.classList.add("opacity-0", "pointer-events-none");
        $('#nearby-branch').addClass('hidden');
        $('#data-detail-branch').removeClass('hidden');
        $('#detail-branch-btn').removeClass('text-black').addClass(
            'border-b-2 text-[#4D55A0] border-[#4D55A0]');
        $('#nearby-location-btn').removeClass('border-b-2 text-[#4D55A0] border-[#4D55A0]');
    }

    function updateDrawerHeight(height) {
        drawerContent.style.height = `${height}vh`;
        drawerContent.classList.toggle("fullscreen", height >= 100);
    }

    const dragStart = (e) => {
        isDragging = true;
        startY = e.pageY || e.touches?.[0].pageY;
        startHeight = parseFloat(drawerContent.style.height);
        drawerContent.style.transition = "none";
    };

    const dragging = (e) => {
        if (!isDragging) return;
        const delta = startY - (e.pageY || e.touches?.[0].pageY);
        const newHeight = startHeight + (delta / window.innerHeight * 100);
        updateDrawerHeight(newHeight);
    };

    const dragStop = () => {
        isDragging = false;
        drawerContent.style.transition = "";
        const currentHeight = parseFloat(drawerContent.style.height);

        if (currentHeight < 25) {
            _closeDrawer();
        } else if (currentHeight > 50) {
            updateDrawerHeight(100);
        } else {
            updateDrawerHeight(35);
        }
    };

    drawerHandle.addEventListener("mousedown", dragStart);
    drawerHandle.addEventListener("touchstart", dragStart);
    document.addEventListener("mousemove", dragging);
    document.addEventListener("touchmove", dragging);
    document.addEventListener("mouseup", dragStop);
    document.addEventListener("touchend", dragStop);

    drawer.addEventListener("click", (e) => {
        if (e.target === drawer) _closeDrawer(); // click background
    });
</script>

{{-- Map --}}
<script>
    let marker = null;
    document.addEventListener("livewire:init", function() {
        const mapBoxToken = @js($mapBoxToken);
        mapboxgl.accessToken = mapBoxToken;
        var map = new mapboxgl.Map({
            container: 'map',
            center: {!! json_encode($center) !!},
            zoom: 11.15,
            style: 'mapbox://styles/mapbox/streets-v12'
        });
        // mapbox: //styles/mapbox/streets-v11
        //outdoors-v12, satellite-v9, dark-v11, streets-v11
        //mapbox://styles/mapbox/standard
        //light-v11, dark-v11, outdoors-v12, satellite-v9
        //streets-v11, satellite-streets-v11, light-v10
        //dark-v10, outdoors-v11, satellite-v9, satellite-hybrid-v9

        // const nav = new mapboxgl.NavigationControl();
        // map.addControl(nav);

        // ดึง latlong ปัจจุบัน ของผู้ใช้
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(_successLocation, _errorLocation, {
                enableHighAccuracy: true
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }

        // ดึง latlong ปัจจุบัน ของผู้ใช้ ถ้าสำเร็จ
        function _successLocation(position) {
            const userLocation = [position.coords.longitude, position.coords.latitude];
            console.log("My Location: " + userLocation);

            Livewire.dispatch('updateLocation', {
                long: position.coords.longitude,
                lat: position.coords.latitude
            });
        }

        // ดึง latlong ปัจจุบัน ของผู้ใช้ ถ้าไม่สำเร็จ
        function _errorLocation(error) {
            console.error("Error getting location:", error.message);
            alert("ไม่สามารถเข้าถึงตำแหน่งของคุณได้ กรุณาเปิด GPS");
        }

        Livewire.on('locationUpdate', (center) => {
            let userLocation = [center[0][0], center[0][1]]
            map.flyTo({
                center: userLocation,
                zoom: 15,
                essential: true
            });

            new mapboxgl.Marker({
                    color: "blue"
                })
                .setLngLat(userLocation)
                .addTo(map);
        });


        let popup = null;
        map.on('click', function(e) {
            let lngLat = e.lngLat;
            const features = map.queryRenderedFeatures(e.point);
            const clickedLayerIds = features.map(f => f.layer.id);

            //กดหมุด
            if (clickedLayerIds.includes('branch-marker-layer')) {
                const feature = features.find(f => f.layer.id === 'branch-marker-layer');
                if (marker) {
                    marker.remove();
                }
                _openDrawer(feature.properties, feature.geometry.coordinates);
                return;
            }

            if (marker) {
                marker.remove();
                marker = null;
            }
            if (popup) {
                popup.remove();
            }
            popup = new mapboxgl.Popup({
                closeButton: false
            }).setHTML(`
                    <div class="flex flex-col items-center">
                        <div class="self-end">
                            <button id="closePopupBtn"><i class="fa-solid fa-xmark" style="color: #adadad;"></i></button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-right">ละติจูด :</span>
                            <span class="text-left" id="lat">${lngLat.lat.toFixed(5)}</span>
                            <span class="text-right">ลองจิจูด :</span>
                            <span class="text-left" id="long">${lngLat.lng.toFixed(5)}</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div id="addLocationBtn" class="flex flex-row justify-center">
                        <span class="mr-2"><i class=" fa-solid fa-circle-plus" style="color: #3b82f6;"></i></span>
                        <span class="text-blue-500">เพิ่มสถานที่</span>
                    </div>`);
            marker = new mapboxgl.Marker({
                    color: "black"
                })
                .setLngLat(lngLat)
                .setPopup(popup)
                .addTo(map);

            popup.addTo(map);

            Livewire.dispatch('setLongLat', {
                long: parseFloat(lngLat.lng.toFixed(6)),
                lat: parseFloat(lngLat.lat.toFixed(6))
            });
        })

        $(document).on('click', '#closePopupBtn', () => {
            if (marker) {
                marker.remove();
            }
        })

        $(document).on('click', '#addLocationBtn', () => {
            $('#locationForm').removeClass('hidden');
            map.removeControl(nav);
        })

        $(document).on('click', '#backBtnForm', () => {
            $('#locationForm').addClass('hidden');
            Livewire.dispatch('updateScope', {
                scope: 5
            });
        })


        // Load the branch markers
        const _loadBranchs = (geoJson) => {
            const circleFeatures = [];
            console.log(geoJson.features.properties);
            geoJson.features.forEach((location) => {
                const {
                    geometry,
                    properties
                } = location;
                const {
                    id,
                    code,
                    name,
                    address,
                    scope,
                    image,
                    phone,
                    manager_id,
                    manager_name,
                    manager_image,
                    manager_role,
                    manager_email,
                    orders,
                } = properties;

                const center = geometry.coordinates;

                const displayScope = scope * 0.9;
                const circle = turf.circle(turf.point(center), displayScope, {
                    steps: 64,
                    units: 'kilometers'
                });

                circle.properties = {
                    id,
                    code,
                    name,
                    address,
                    scope,
                    image,
                    phone,
                    manager_id,
                    manager_name,
                    manager_role,
                    manager_email,
                    orders,
                };

                circleFeatures.push(circle);
            });

            if (map.getSource('branch-markers')) {
                map.getSource('branch-markers').setData(geoJson);
            } else {
                map.addSource('branch-markers', {
                    type: 'geojson',
                    data: geoJson
                });
            }

            const imageUrl =
                `https://api.mapbox.com/v4/marker/pin-m-marker+4D55A0.png?access_token=${mapboxgl.accessToken}`;
            const iconName = `branch-icon-marker`;

            if (!map.hasImage(iconName)) {
                map.loadImage(imageUrl, (error, image) => {
                    if (error) throw error;
                    map.addImage(iconName, image);

                    if (!map.getLayer('branch-marker-layer')) {
                        map.addLayer({
                            id: 'branch-marker-layer',
                            type: 'symbol',
                            source: 'branch-markers',
                            layout: {
                                'icon-image': 'branch-icon-marker',
                                'icon-allow-overlap': true,
                            },
                        });
                    }
                });
            } else {
                if (!map.getLayer('branch-marker-layer')) {
                    map.addLayer({
                        id: 'branch-marker-layer',
                        type: 'symbol',
                        source: 'branch-markers',
                        layout: {
                            'icon-image': 'branch-icon-marker',
                            'icon-allow-overlap': true,
                        },
                    });
                }
            }

            const geojsonCircleCollection = {
                type: 'FeatureCollection',
                features: circleFeatures
            };

            if (map.getSource('branch-circles')) {
                map.getSource('branch-circles').setData(geojsonCircleCollection);
            } else {
                map.addSource('branch-circles', {
                    type: 'geojson',
                    data: geojsonCircleCollection
                });

                map.addLayer({
                    id: 'branch-circle-layer',
                    type: 'fill',
                    source: 'branch-circles',
                    paint: {
                        'fill-color': '#007BFF',
                        'fill-opacity': 0.3,
                        'fill-outline-color': '#0044cc'
                    }
                });
            }
        }

        map.on('load', () => {
            _loadBranchs({!! $geoJsonBranch !!})
        });

        Livewire.on('updateBranchLocation', (geoJson) => {
            marker.remove();
            _loadBranchs(JSON.parse(geoJson));
        })

        Livewire.on('deleteBranchLocation', (geoJson) => {
            _loadBranchs(JSON.parse(geoJson));
        })

        Livewire.on('updateEditBranchLocation', (geoJson) => {
            _loadBranchs(JSON.parse(geoJson));
        })

        Livewire.on('locationsUpdate', (center) => {
            let userLocation = [center[0][0][0], center[0][0][1]]
            map.flyTo({
                center: userLocation,
                zoom: 15,
                essential: true
            });
        });
    });


    async function fetchNearby(branchId, page = 1) {
        try {
            const imageUrl =
                `https://api.mapbox.com/v4/marker/pin-m-marker+4D55A0.png?access_token=${mapboxgl.accessToken}`;
            const res = await fetch(`/nearby/${branchId}?page=${page}&per_page=5`);
            const data = await res.json();
            console.log(data)
            const allNearby = data.nearby_combined.data;

            const container = document.getElementById('nearby-branch');
            container.innerHTML = '';
            allNearby.forEach(place => {
                const html = `
        <div class="bg-white rounded-xl shadow-md border overflow-hidden flex mb-4">
            <img class="w-[62px] h-[100px] object-cover object-top px-3 pt-5 rounded" src="https://api.mapbox.com/v4/marker/pin-m-marker+4D55A0.png?access_token=${mapboxgl.accessToken}"/>
            <div class="flex-1 p-3 overflow-hidden">
                <p class="text font-semibold text-black break-words whitespace-normal">${place.name}</p>
                <p class="text-sm my-2 text-gray-600 break-words whitespace-normal">${place.address ?? '-'}</p>
                <p class="text-sm text-gray-500 mt-1">ระยะทาง ${place.distance} กิโลเมตร</p>
            </div>
            <img src="${place.image ?? '/img/placeholder.png'}" alt="${place.name}" class="w-24 h-max-full object-cover">
        </div>`;
                container.innerHTML += html;
            });

            // 👇 ส่วน Pagination พร้อมปุ่ม “<< < 1 2 3 > >>”
            const paginationDiv = document.getElementById('nearby-pagination');
            paginationDiv.innerHTML = '';

            const current = data.nearby_combined.current_page;
            const last = data.nearby_combined.last_page;

            let paginationHTML = `<div class="flex gap-2 justify-center mt-4">`;

            if (current > 1) {
                paginationHTML +=
                    `
        <button onclick="fetchNearby(${branchId}, 1)" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200">«</button>
        <button onclick="fetchNearby(${branchId}, ${current - 1})" class="w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300">‹</button>`;
            }

            for (let i = 1; i <= last; i++) {
                paginationHTML += `
        <button onclick="fetchNearby(${branchId}, ${i})"
            class="w-9 h-9 rounded-full ${i === current ? 'bg-[#4D55A0] text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-300'}">
            ${i}
        </button>`;
            }

            if (current < last) {
                paginationHTML +=
                    `
        <button onclick="fetchNearby(${branchId}, ${current + 1})" class="w-9 h-9 rounded-full bg-gray-200 hover:bg-gray-300">›</button>
        <button onclick="fetchNearby(${branchId}, ${last})" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200">»</button>`;
            }

            paginationHTML += `</div>`;
            paginationDiv.innerHTML = paginationHTML;
        } catch (err) {
            console.error("❌ Nearby error:", err);
        }
    }
</script>
