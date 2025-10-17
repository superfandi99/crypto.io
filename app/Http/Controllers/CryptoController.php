<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

class CryptoController extends Controller
{
    public function index()
    {
        return view('crypto');
    }

    public function encrypt(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'key' => 'required|string', // untuk sekarang tidak dipakai oleh Crypt::encryptString
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $message = $request->input('message');
        $imagePath = $request->file('image')->store('uploads', 'public');

        // Enkripsi pesan (menggunakan enkripsi Laravel default)
        $encryptedMessage = Crypt::encryptString($message); // hasil string terenkripsi (bisa mengandung karakter non-printable)

        // Baca gambar sebagai resource GD
        $manager = new ImageManager(new Driver());
        $image = $manager->read(storage_path('app/public/' . $imagePath));
        $gdResource = imagecreatefromstring((string) $image->encode(new PngEncoder()));

        $width = imagesx($gdResource);
        $height = imagesy($gdResource);

        // Ubah pesan terenkripsi jadi binary; tetapi terlebih dahulu simpan panjang (bytes) sebagai header 32-bit
        $messageBytes = strlen($encryptedMessage); // panjang dalam bytes
        $headerBinary = str_pad(decbin($messageBytes), 32, '0', STR_PAD_LEFT); // 32-bit header
        $payloadBinary = $this->stringToBinary($encryptedMessage); // data sebenar dalam bit
        $binaryData = $headerBinary . $payloadBinary;

        $dataLengthBits = strlen($binaryData); // jumlah bit yang harus disisipkan
        $capacityBits = $width * $height; // 1 bit per pixel (menggunakan channel R saja)

        // Cek kapasitas
        if ($dataLengthBits > $capacityBits) {
            imagedestroy($gdResource);
            return back()->withErrors(['message' => "Pesan (terenkripsi) terlalu panjang untuk gambar ini. Kapasitas: {$capacityBits} bit, diperlukan: {$dataLengthBits} bit."]);
        }

        // Sisipkan bit ke LSB channel R
        $index = 0;
        for ($y = 0; $y < $height && $index < $dataLengthBits; $y++) {
            for ($x = 0; $x < $width && $index < $dataLengthBits; $x++) {
                $pixel = imagecolorat($gdResource, $x, $y);
                $r = ($pixel >> 16) & 0xFF;
                $g = ($pixel >> 8) & 0xFF;
                $b = $pixel & 0xFF;

                // set LSB of R to next bit
                $bit = intval($binaryData[$index]);
                $r = ($r & 0xFE) | $bit;
                $index++;

                $newColor = imagecolorallocate($gdResource, $r, $g, $b);
                imagesetpixel($gdResource, $x, $y, $newColor);
            }
        }

        // Simpan hasil ke storage/public/outputs
        $outputPath = 'outputs/' . time() . '_stego.png';
        $fullOutputPath = storage_path('app/public/' . $outputPath);
        if (!file_exists(dirname($fullOutputPath))) {
            mkdir(dirname($fullOutputPath), 0755, true);
        }
        imagepng($gdResource, $fullOutputPath);
        imagedestroy($gdResource);

        $downloadUrl = asset('storage/' . $outputPath);

        return redirect()->to(url()->previous() . '#encrypt')
            ->with('success', 'Enkripsi & penyisipan berhasil.')
            ->with('downloadUrl', $downloadUrl);

    }

    public function decrypt(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        // NOTE: key saat ini tidak dipakai karena menggunakan Crypt::decryptString yang memakai APP_KEY.
        $imagePath = $request->file('image')->store('uploads', 'public');

        $manager = new ImageManager(new Driver());
        $image = $manager->read(storage_path('app/public/' . $imagePath));
        $gdResource = imagecreatefromstring((string) $image->encode(new PngEncoder()));

        $width = imagesx($gdResource);
        $height = imagesy($gdResource);
        $capacityBits = $width * $height;

        // Pertama: baca 32 bit pertama -> header panjang (dalam bytes)
        $headerBits = '';
        $index = 0;
        $stop = false;

        for ($y = 0; $y < $height && strlen($headerBits) < 32; $y++) {
            for ($x = 0; $x < $width && strlen($headerBits) < 32; $x++) {
                $pixel = imagecolorat($gdResource, $x, $y);
                $r = ($pixel >> 16) & 0xFF;
                $headerBits .= ($r & 1);
            }
        }

        if (strlen($headerBits) < 32) {
            imagedestroy($gdResource);
            return back()->withErrors(['image' => 'Gambar tidak berisi header panjang pesan atau rusak.']);
        }

        $messageBytes = bindec($headerBits); // panjang pesan terenkripsi dalam bytes
        $bitsToRead = 32 + ($messageBytes * 8);

        if ($bitsToRead > $capacityBits) {
            imagedestroy($gdResource);
            return back()->withErrors(['image' => 'Header menyatakan panjang pesan melebihi kapasitas gambar — data kemungkinan korup.']);
        }

        // Sekarang baca keseluruhan bits yang dibutuhkan (header sudah kita punya 32 bit pertama)
        $binaryData = $headerBits; // sudah berisi 32 bit header
        for ($y = 0; $y < $height && strlen($binaryData) < $bitsToRead; $y++) {
            for ($x = 0; $x < $width && strlen($binaryData) < $bitsToRead; $x++) {
                // Skip pixel yang sudah dipakai untuk header (pertama 32 pixel)
                $pos = ($y * $width) + $x;
                if ($pos < 32) {
                    continue; // sudah dibaca pada tahap header
                }
                $pixel = imagecolorat($gdResource, $x, $y);
                $r = ($pixel >> 16) & 0xFF;
                $binaryData .= ($r & 1);
            }
        }

        imagedestroy($gdResource);

        // Ambil hanya payload (tanpa header)
        $payloadBits = substr($binaryData, 32, $messageBytes * 8);
        if (strlen($payloadBits) < ($messageBytes * 8)) {
            return back()->withErrors(['image' => 'Jumlah bit payload kurang dari yang diharapkan — data mungkin rusak.']);
        }

        $encryptedMessage = $this->binaryToString($payloadBits);

        try {
            $decryptedMessage = Crypt::decryptString($encryptedMessage);

            return redirect()->to(url()->previous() . '#decrypt')
                ->with('success', 'Dekripsi berhasil.')
                ->with('message', $decryptedMessage);

        } catch (\Exception $e) {
            return redirect()->to(url()->previous() . '#decrypt')
                ->withErrors(['key' => 'Dekripsi gagal. Kunci salah atau data rusak.']);
        }

    }

    // Helper: konversi string -> binary (string of '0'/'1')
    private function stringToBinary($string)
    {
        $binary = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $binary .= str_pad(decbin(ord($string[$i])), 8, '0', STR_PAD_LEFT);
        }
        return $binary;
    }

    // Helper: konversi binary -> string
    private function binaryToString($binary)
    {
        $string = '';
        // pastikan kelipatan 8
        $binary = substr($binary, 0, strlen($binary) - (strlen($binary) % 8));
        for ($i = 0; $i < strlen($binary); $i += 8) {
            $byte = substr($binary, $i, 8);
            $string .= chr(bindec($byte));
        }
        return $string;
    }
}
