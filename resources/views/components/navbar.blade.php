<!-- resources/views/components/navbar.blade.php -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="#" class="text-2xl font-bold text-blue-600">🔐 AES App</a>

        <!-- Tombol hamburger (mobile) -->
        <button id="menu-toggle"
            class="md:hidden text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-600 rounded"
            aria-label="Toggle menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Menu -->
        <div id="menu"
            class="hidden md:flex flex-col md:flex-row md:items-center absolute md:static top-16 left-0 w-full md:w-auto bg-white md:bg-transparent shadow-md md:shadow-none px-6 py-4 md:py-0 space-y-4 md:space-y-0 md:space-x-6">

            <!-- Menu Link -->
            <a href="#home" class="block hover:text-blue-600 font-bold transition">Home</a>
            <a href="#about" class="block hover:text-blue-600 font-bold transition">About</a>
            <a href="#encrypt" class="block hover:text-blue-600 font-bold transition">E/D</a>
            <a href="#faq" class="block hover:text-blue-600 font-bold transition">FAQ</a>

            <!-- Auth Links -->
            @auth
                <!-- Profile Dropdown -->
                <div class="relative w-full md:w-auto">
                    <button id="profile-btn"
                        class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-between">
                        Profile
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div id="profile-dropdown"
                        class="absolute right-0 mt-2 w-full md:w-48 bg-white border rounded-lg shadow-lg hidden z-50 md:mt-2">
                        <div class="px-4 py-2 text-gray-800 font-semibold border-b">
                            {{ Auth::user()->name }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100 rounded-b-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center md:text-left">
                    Login
                </a>
            @endguest
        </div>
    </div>
</nav>

<script>
    // Toggle menu responsif (mobile)
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');

    menuToggle.addEventListener('click', () => {
        menu.classList.toggle('hidden'); // cukup toggle hidden
    });

    // Toggle dropdown profile
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    profileBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // supaya klik dropdown tidak menutup menu
        profileDropdown.classList.toggle('hidden');
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', (e) => {
        if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });
</script>
