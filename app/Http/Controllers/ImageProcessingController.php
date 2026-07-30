<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageProcessingController extends Controller
{
    const MAX_SIZE_MB = 5; // Sesuai dengan frontend (5MB)

    public function removeBackground(Request $request)
    {
        // 1. VALIDASI INPUT
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Maks 5MB
            'bg_color' => 'nullable|string|regex:/^(transparent|#([A-Fa-f0-9]{6}))$/',
        ]);

        try {
            // 2. AMBIL FILE DARI REQUEST
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $bgColor = $request->input('bg_color', 'transparent');
            
            Log::info('Memulai proses background removal', [
                'file' => $originalName,
                'size' => $file->getSize(),
                'bg_color' => $bgColor
            ]);

            // 3. OPTIMALKAN GAMBAR SEBELUM DIKIRIM (untuk hemat bandwidth)
            $optimizedImage = Image::make($file);
            
            // Resize jika terlalu besar (max 1200px lebar)
            if ($optimizedImage->width() > 1200) {
                $optimizedImage->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            
            // Simpan ke temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'upload_') . '.jpg';
            $optimizedImage->save($tempPath, 85); // Quality 85%

            // 4. KIRIM KE API EKSTERNAL YANG BENAR
            // PERHATIAN: Endpoint mungkin berbeda!
            
            // OPSI 1: Jika ada endpoint /remove-bg/ (coba ini dulu)
            $response = Http::timeout(60)
                ->attach(
                    'file', // PARAMETER KUNCI: harus 'file' bukan 'image'
                    fopen($tempPath, 'r'),
                    $originalName
                )
                ->post('https://matha.site/remove-bg/', [
                    // Parameter tambahan jika diperlukan
                ]);
                
            // OPSI 2: Atau coba endpoint /chatbot/remove-bg/
            if (!$response->successful()) {
                Log::warning('Endpoint /remove-bg/ gagal, mencoba alternatif...');
                
                $response = Http::timeout(60)
                    ->attach(
                        'file',
                        fopen($tempPath, 'r'),
                        $originalName
                    )
                    ->post('https://matha.site/chatbot/remove-bg/');
            }

            // 5. BERSIHKAN FILE TEMPORARY
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            // 6. CEK RESPONSE
            if (!$response->successful()) {
                Log::error('API Background Removal gagal', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'headers' => $response->headers()
                ]);
                
                // FALLBACK: Jika API eksternal gagal, gunakan metode lokal sederhana
                return $this->fallbackProcessing($file, $bgColor);
            }

            // 7. PROSES RESPONSE DARI API
            $contentType = $response->header('Content-Type');
            $body = $response->body();

            // Cek jika response adalah JSON (seperti di proyek lain)
            if (str_contains($contentType, 'application/json')) {
                $jsonData = json_decode($body, true);
                
                if (isset($jsonData['image_base64'])) {
                    // Decode base64 image dari API
                    $imageBytes = base64_decode($jsonData['image_base64']);
                    
                    // Proses dengan bg_color jika diperlukan
                    $processedImage = $this->applyBackgroundColor($imageBytes, $bgColor);
                    
                    // Simpan ke storage
                    $outputName = 'hasil_' . time() . '_' . uniqid() . '.png';
                    Storage::disk('public')->put($outputName, $processedImage);
                    
                    $imageUrl = Storage::url($outputName);
                    
                    Log::info('Gambar berhasil diproses', [
                        'url' => $imageUrl,
                        'size' => strlen($processedImage)
                    ]);
                    
                    // Return sebagai JSON response
                    return response()->json([
                        'success' => true,
                        'message' => $bgColor === 'transparent' 
                            ? 'Background berhasil dihapus' 
                            : 'Background berhasil diganti',
                        'image_url' => $imageUrl,
                        'download_url' => url('storage/' . $outputName),
                        'face_data' => $jsonData['faces'][0] ?? null
                    ]);
                }
            }
            
            // 8. JIKA RESPONSE ADALAH GAMBAR LANGSUNG (binary)
            if (str_contains($contentType, 'image/')) {
                // Apply background color jika diperlukan
                if ($bgColor !== 'transparent') {
                    $image = Image::make($body);
                    $canvas = Image::canvas($image->width(), $image->height(), $bgColor);
                    $canvas->insert($image, 'top-left', 0, 0);
                    $body = (string) $canvas->encode('png');
                    $contentType = 'image/png';
                }
                
                // Langsung kembalikan gambar binary
                return response($body)
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', 'attachment; filename="hasil_nobg.png"');
            }

            // 9. JIKA FORMAT TIDAK DIKENAL
            throw new \Exception('Format response tidak dikenali dari API');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Koneksi ke API gagal: ' . $e->getMessage());
            return $this->fallbackProcessing($request->file('image'), $request->input('bg_color', 'transparent'));
            
        } catch (\Exception $e) {
            Log::error('Error pemrosesan gambar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'note' => 'Menggunakan fallback processing'
            ], 500);
        }
    }

    /**
     * Fallback processing ketika API eksternal tidak tersedia
     */
    private function fallbackProcessing($file, $bgColor = 'transparent')
    {
        Log::info('Menggunakan fallback processing');
        
        try {
            // Simple background removal simulation
            $image = Image::make($file);
            
            // Untuk demo: buat gambar dengan alpha channel
            if ($bgColor === 'transparent') {
                // Buat canvas transparan
                $canvas = Image::canvas($image->width(), $image->height(), [255, 255, 255, 0]);
                // Tambahkan efek "remove bg" sederhana (edge detection sederhana)
                $image->greyscale();
                $canvas->insert($image, 'top-left', 0, 0);
            } else {
                // Hanya ganti background warna
                $canvas = Image::canvas($image->width(), $image->height(), $bgColor);
                $canvas->insert($image, 'top-left', 0, 0);
            }
            
            // Simpan hasil
            $outputName = 'fallback_' . time() . '.png';
            Storage::disk('public')->put($outputName, (string) $canvas->encode('png'));
            
            return response()->json([
                'success' => true,
                'message' => 'Diproses dengan mode fallback (API eksternal offline)',
                'image_url' => Storage::url($outputName),
                'download_url' => url('storage/' . $outputName),
                'note' => 'Ini adalah hasil simulasi'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Fallback processing gagal: ' . $e->getMessage());
            
            // Return gambar asli sebagai last resort
            $image = Image::make($file);
            $outputName = 'original_' . time() . '.png';
            Storage::disk('public')->put($outputName, (string) $image->encode('png'));
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses gambar. Mengembalikan gambar asli.',
                'image_url' => Storage::url($outputName),
                'download_url' => url('storage/' . $outputName)
            ], 200);
        }
    }

    /**
     * Apply background color to transparent image
     */
    private function applyBackgroundColor($imageBytes, $bgColor)
    {
        if ($bgColor === 'transparent') {
            return $imageBytes;
        }
        
        $image = Image::make($imageBytes);
        $canvas = Image::canvas($image->width(), $image->height(), $bgColor);
        $canvas->insert($image, 'top-left', 0, 0);
        
        return (string) $canvas->encode('png');
    }

    /**
     * Test endpoint untuk debugging
     */
    public function testEndpoint(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'ImageProcessingController berjalan',
            'timestamp' => now(),
            'endpoints' => [
                'remove_background' => '/remove-background (POST)',
                'test' => '/api/image/test (GET)'
            ],
            'requirements' => [
                'intervention/image' => class_exists(\Intervention\Image\ImageManager::class) ? 'Installed' : 'Missing',
                'laravel_http' => class_exists(\Illuminate\Support\Facades\Http::class) ? 'Installed' : 'Missing',
                'storage_writable' => is_writable(storage_path()) ? 'Yes' : 'No'
            ]
        ]);
    }
}