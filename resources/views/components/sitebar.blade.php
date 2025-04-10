{{--
    @title : sidebar
    @author : Nontapat Sinthum 66160104
    @create date : 04/04/2568
--}}

<aside class="app-sidebar">
    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform z-50">
        <!-- Close Button -->
        <button id="closeMenu" class="absolute top-2 right-4 ">
            <i class="fa-solid fa-x"></i>
        </button>

        <!-- Sidebar Menu -->
        <ul class="pt-5 mt-4">
            <li
                class="{{ (Request::is('/', 'home') && auth()->user()->us_role === 'CEO') ||
                (Request::is('/', 'home', 'branch-Sales', 'branch-detail/*') && auth()->user()->us_role === 'Sales')
                    ? 'bg-indigo-100 text-[#4D55A0]'
                    : '' }}">
                <a href="{{ route('home') }}" class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                    <i class="fa-solid fa-house mr-3"
                        style="color: {{ (Request::is('/', 'home') && auth()->user()->us_role === 'CEO') ||
                        (Request::is('/', 'home', 'branch-Sales', 'branch-detail/*') && auth()->user()->us_role === 'Sales')
                            ? 'bg-indigo-100 text-[#4D55A0]'
                            : '' }}"></i>
                    หน้าแรก
                </a>
            </li>

            <li class="{{ Request::is('map/*') ? 'bg-indigo-100 text-[#4D55A0]' : '' }}">
                <a href="{{ route('map') }}" class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                    <i class="fa-solid fa-globe mr-3"
                        style="color: {{ Request::is('map/*') ? '#4D55A0' : '#595959' }}; vertical-align: middle;"></i>
                    แผนที่
                </a>
            </li>
            @if (auth()->user()->us_role === 'CEO')
                <li
                    class="{{ Request::is('manage-user', 'add-user', 'edit-user/*') ? 'bg-indigo-100 text-[#4D55A0]' : '' }}">
                    <a href="{{ route('manage.user') }}"
                        class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                        <i class="fa-solid fa-circle-user mr-3"
                            style="color: {{ Request::is('manage-user', 'add-user', 'edit-user/*') ? '#4D55A0' : '#595959' }}; vertical-align: middle;"></i>
                        จัดการบัญชี
                    </a>
                </li>
            @endif

            <li
                class="{{ Request::is('order', 'order-detail/*', 'add-order/*', 'editOrder/*') ? 'bg-indigo-100 text-[#4D55A0]' : '' }}">
                <a href="{{ route('order') }}"
                    class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                    <i class="fa-solid fa-chart-column mr-3"
                        style="color: {{ Request::is('order', 'order-detail/*', 'add-order/*', 'editOrder/*') ? '#4D55A0' : '#595959' }}; vertical-align: middle;"></i>
                    ยอดขาย
                </a>
            </li>

            @if (auth()->user()->us_role === 'CEO' || auth()->user()->us_role === 'Sales Supervisor')
                <li
                    class="{{ Request::is('reportCEO', 'branchMyMap', 'branch-detail/*', 'report/sales-team', 'report/sales-team/*', 'report/SalesSup', 'report/SaleSup/Team', 'branch-detail/*') ? 'bg-indigo-100 text-[#4D55A0]' : '' }}">
                    <a href="{{ auth()->user()->us_role === 'CEO'
                        ? route('report_CEO')
                        : (auth()->user()->us_role === 'Sales Supervisor'
                            ? route('report_SalesSupervisor')
                            : '') }}"
                        class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                        <i class="fa-regular fa-calendar-days mr-3"
                            style="color: {{ Request::is('reportCEO', 'branchMyMap', 'branch-detail/*', 'report/sales-team', 'report/sales-team/*', 'report/SalesSup', 'report/SaleSup/Team', 'branch-detail/*') ? '#4D55A0' : '#595959' }}; vertical-align: middle;"></i>
                        รายงาน
                    </a>
                </li>
            @endif


            <hr class="my-2">

            <li class="px-4 py-3 hover:bg-gray-200 flex items-center text-lg font-medium">
                <a href="{{ route('logout') }}">
                    <i class="fa-solid fa-right-from-bracket mr-3" style="color: #595959;"></i> ออกจากระบบ
                </a>

            </li>
        </ul>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40"></div>
</aside>
