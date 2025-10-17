<!-- resources/views/components/about.blade.php -->
<section id="about"
    class="relative h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-blue-50 overflow-hidden px-6">
    <!-- Dekorasi background -->
    <div
        class="absolute top-0 left-0 w-96 h-96 bg-blue-300 opacity-20 rounded-full blur-3xl -translate-x-20 -translate-y-20">
    </div>
    <div
        class="absolute bottom-0 right-0 w-80 h-80 bg-blue-500 opacity-20 rounded-full blur-3xl translate-x-20 translate-y-20">
    </div>

    <!-- Konten -->
    <div class="relative max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center animate-fadeIn">
        <!-- Bagian teks -->
        <div class="text-center md:text-left space-y-6">
            <h2 class="text-4xl md:text-5xl font-extrabold text-blue-700 leading-tight">
                About
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                Aplikasi ini menggabungkan kekuatan algoritma <strong>AES</strong> untuk melindungi pesan rahasia,
                dan teknik <strong>Steganografi</strong> untuk menyembunyikan ciphertext ke dalam gambar PNG.
                Solusi keamanan data yang <span class="text-blue-600 font-semibold">aman</span>,
                <span class="text-blue-600 font-semibold">cepat</span>, dan <span
                    class="text-blue-600 font-semibold">mudah digunakan</span>.
            </p>
            <a href="#encrypt"
                class="inline-block bg-blue-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-blue-700 shadow-lg transition transform hover:scale-105">
                Coba Sekarang
            </a>
        </div>

        <!-- Bagian ilustrasi -->
        <div class="flex justify-center">
            <img src="{{ asset('images/Encryption.png') }}" alt="Ilustrasi keamanan data"
                class="w-80 md:w-[400px] drop-shadow-lg hover:scale-105 transition-transform duration-300">
        </div>
    </div>
</section>

