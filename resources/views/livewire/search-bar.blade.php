<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md z-50 flex justify-between items-center">

        <!-- Navbar -->
        <nav class="fixed top-0 left-0 w-full bg-white shadow-md z-10 flex justify-between items-center px-4 py-2">
            <!-- Hamburger Menu Button -->
            <button id="menuToggle" class="text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <!-- Search -->
            <div class="flex-1 mx-4 relative" wire:key="search-bar-container">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" placeholder="ค้นหา" wire:model="searchTerm"
                    oninput="Livewire.dispatch('updateSearchTerm', { value: this.value })"
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-sm focus:outline-none" />
                @if (!empty($searchResults) && !empty($searchTerm))
                    <ul id="searchDropdown" class="absolute bg-white border rounded-md mt-1 w-full z-50">
                        @foreach ($searchResults as $result)
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                wire:click="selectLocation(@js($result['lat']), @js($result['long']), @js($result['name']), @js($result['address']));">
                                <label for="">{{ $result['name'] }}</label> <label class="text-gray-800 text-sm"
                                    for="">{{ $result['address'] }}</label>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Filter & Profile -->
            <div class="flex items-center space-x-2">
                <button class="w-9 h-9 flex items-center justify-center border rounded-md">
                    <i class="fa-solid fa-filter text-gray-600"></i>
                </button>
                <button class="w-9 h-9 flex items-center justify-center border rounded-full">
                    @if (isset(Auth::user()->us_image))
                        <img src="{{ Auth::user()->us_image }}" alt="Profile" class="w-9 h-9 rounded-full">
                    @else
                        <i class="fa-solid fa-user text-gray-600"></i>
                    @endif
                </button>
            </div>
        </nav>
    </nav>
    @include('components.sitebar')
    <!-- Script เปิด/ปิดเมนู -->
    <script>
        $(document).ready(function() {
            $("#menuToggle").click(function() {
                $("#sidebar").removeClass("-translate-x-full");
                $("#overlay").removeClass("hidden");
            });

            $("#closeMenu, #overlay").click(function() {
                $("#sidebar").addClass("-translate-x-full");
                $("#overlay").addClass("hidden");
            });
        });
    </script>

    @once
        <script>
            document.addEventListener('pointerdown', function(event) {
                const input = document.getElementById('searchInput');
                const dropdown = document.getElementById('searchDropdown');

                if (dropdown && !input.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });

            document.getElementById('searchInput')?.addEventListener('focus', function() {
                const dropdown = document.getElementById('searchDropdown');
                if (dropdown) dropdown.style.display = 'block';
            });
        </script>
    @endonce
</div>
