<section id="decrypt"
    class="min-h-screen flex justify-center items-center bg-gradient-to-br from-green-50 to-green-100 p-6">
    <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl p-8 hover:shadow-3xl transition-all duration-300">
        <h2 class="text-3xl font-bold mb-8 text-green-600 text-center">🧩 Dekripsi Pesan dari Foto</h2>

        <form action="{{ route('decrypt') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label for="key_decrypt" class="block text-gray-700 font-semibold mb-1">Kunci AES:</label>
                <input type="text" name="key" id="key_decrypt"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" required>
            </div>

            <div>
                <label for="image_decrypt" class="block text-gray-700 font-semibold mb-1">Unggah Foto yang Dimodifikasi
                    (PNG):</label>
                <input type="file" name="image" id="image_decrypt" accept="image/png"
                    class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring focus:ring-green-300"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300">
                Dekripsi
            </button>
        </form>
    </div>
</section>
