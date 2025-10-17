<section id="encrypt" class="min-h-screen flex justify-center items-center bg-gradient-to-br from-blue-50 to-blue-100 p-6">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-300">
        <h2 class="text-3xl font-bold mb-8 text-blue-600 text-center">🔐 Enkripsi Pesan ke Foto</h2>

        @if (session('success'))
            <div class="bg-green-100 p-4 rounded mb-4 text-green-800">
                {{ session('success') }}
                @if (session('downloadUrl'))
                    <a href="{{ session('downloadUrl') }}" download class="text-blue-500 underline ml-1">Unduh Gambar</a>
                @endif
                @if (session('message'))
                    <p class="mt-2 font-semibold">Pesan Tersembunyi: {{ session('message') }}</p>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 p-4 rounded mb-4 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('encrypt') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label for="message" class="block text-gray-700 font-semibold mb-1">Pesan Rahasia:</label>
                <textarea name="message" id="message" rows="3"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300"
                    required>{{ old('message') }}</textarea>
            </div>

            <div>
                <label for="key" class="block text-gray-700 font-semibold mb-1">Kunci AES:</label>
                <input type="text" name="key" id="key"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300" required>
            </div>

            <div>
                <label for="image" class="block text-gray-700 font-semibold mb-1">Unggah Foto (PNG):</label>
                <input type="file" name="image" id="image" accept="image/png"
                    class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring focus:ring-blue-300" required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300">
                Enkripsi & Sisipkan
            </button>
        </form>
    </div>
</section>
