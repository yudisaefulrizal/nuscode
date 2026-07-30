import os
from PIL import Image
import glob

def resize_and_convert_to_webp(input_folder, output_folder=None, target_width=600, quality=85):
    """
    Mengubah semua gambar di folder menjadi WebP dengan lebar 600px
    (tinggi menyesuaikan rasio asli)
    
    Parameters:
    - input_folder: Folder yang berisi gambar-gambar
    - output_folder: Folder untuk menyimpan hasil (default: folder "produk_webp")
    - target_width: Lebar target dalam pixel (default: 600px)
    - quality: Kualitas WebP (0-100, default: 85)
    """
    
    # Jika output_folder tidak ditentukan, buat folder "produk_webp"
    if output_folder is None:
        output_folder = "produk_webp"
    
    # Buat folder output jika belum ada
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)
    
    # Daftar ekstensi gambar yang didukung
    supported_extensions = ['*.jpg', '*.jpeg', '*.png', '*.bmp', '*.gif', '*.tiff']
    
    # Kumpulkan semua file gambar
    image_files = []
    for ext in supported_extensions:
        image_files.extend(glob.glob(os.path.join(input_folder, ext)))
        # Untuk format JPEG (case insensitive)
        if ext in ['*.jpg', '*.jpeg']:
            image_files.extend(glob.glob(os.path.join(input_folder, ext.upper())))
    
    print(f"📁 Menemukan {len(image_files)} gambar di folder '{input_folder}'")
    print(f"📂 Hasil akan disimpan di: '{output_folder}/'")
    print(f"📏 Ukuran target: lebar {target_width}px (tinggi proporsional)")
    print("-" * 60)
    
    converted_count = 0
    error_count = 0
    
    for image_path in image_files:
        try:
            # Buka gambar
            img = Image.open(image_path)
            
            # Hitung ukuran baru dengan rasio asli
            original_width, original_height = img.size
            
            # Jika gambar sudah lebih kecil dari target width, skip resize
            if original_width <= target_width:
                new_width = original_width
                new_height = original_height
                print(f"⚠️  {os.path.basename(image_path)}: Gambar sudah lebih kecil ({original_width}px), tidak di-resize")
            else:
                # Hitung tinggi baru berdasarkan rasio
                ratio = target_width / original_width
                new_width = target_width
                new_height = int(original_height * ratio)
            
            # Resize gambar
            if original_width > target_width:
                resized_img = img.resize((new_width, new_height), Image.Resampling.LANCZOS)
            else:
                resized_img = img
            
            # Tentukan nama file output
            filename = os.path.basename(image_path)
            name_without_ext = os.path.splitext(filename)[0]
            output_path = os.path.join(output_folder, f"{name_without_ext}.webp")
            
            # Convert ke RGB untuk gambar dengan mode RGBA/P (transparansi)
            if resized_img.mode in ('RGBA', 'P', 'LA'):
                # Buat background putih untuk gambar dengan transparansi
                rgb_img = Image.new('RGB', resized_img.size, (255, 255, 255))
                
                if resized_img.mode == 'RGBA':
                    # Jika ada alpha channel, gunakan sebagai mask
                    rgb_img.paste(resized_img, mask=resized_img.split()[3])
                elif resized_img.mode == 'LA':
                    # Mode LA (Luminance + Alpha)
                    rgb_img.paste(resized_img.convert('RGBA'), mask=resized_img.split()[1])
                else:
                    # Mode P (Palette)
                    rgb_img.paste(resized_img.convert('RGBA'), mask=resized_img.convert('RGBA').split()[3])
                
                final_img = rgb_img
            else:
                final_img = resized_img.convert('RGB') if resized_img.mode != 'RGB' else resized_img
            
            # Simpan sebagai WebP
            final_img.save(output_path, 'WEBP', quality=quality, optimize=True)
            
            # Tampilkan info resize
            print(f"✅ {filename}")
            print(f"   Ukuran asli: {original_width}×{original_height}px")
            print(f"   Ukuran baru: {new_width}×{new_height}px ({new_width/original_width*100:.1f}% dari asli)")
            print(f"   Disimpan sebagai: {name_without_ext}.webp")
            print(f"   Ukuran file: {os.path.getsize(output_path) / 1024:.1f} KB")
            print()
            
            converted_count += 1
            
        except Exception as e:
            print(f"❌ Gagal memproses {os.path.basename(image_path)}: {str(e)}")
            error_count += 1
    
    print("=" * 60)
    print("📊 RINGKASAN KONVERSI:")
    print(f"   Total gambar ditemukan: {len(image_files)}")
    print(f"   Berhasil dikonversi: {converted_count}")
    print(f"   Gagal dikonversi: {error_count}")
    print(f"   Folder output: {output_folder}/")
    
    if converted_count > 0:
        print("\n🎉 Semua gambar telah berhasil dikonversi ke WebP (600px width)!")

def batch_process_with_stats(input_folder, output_folder="produk_webp", target_width=600):
    """
    Versi batch processing dengan statistik lebih detail
    """
    from PIL import Image
    import os
    import glob
    
    if not os.path.exists(input_folder):
        print(f"❌ Folder '{input_folder}' tidak ditemukan!")
        return
    
    # Buat folder output
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)
    
    # Ekstensi yang didukung
    extensions = ['*.jpg', '*.jpeg', '*.png', '*.bmp', '*.gif', '*.tiff']
    image_files = []
    
    for ext in extensions:
        image_files.extend(glob.glob(os.path.join(input_folder, ext)))
        if ext in ['*.jpg', '*.jpeg']:
            image_files.extend(glob.glob(os.path.join(input_folder, ext.upper())))
    
    if not image_files:
        print("⚠️  Tidak ada gambar yang ditemukan!")
        return
    
    print(f"🔄 Memproses {len(image_files)} gambar...")
    print(f"   Lebar target: {target_width}px")
    print("-" * 60)
    
    success_count = 0
    skipped_small = 0
    failed_files = []
    total_size_reduction = 0
    
    for i, img_path in enumerate(image_files, 1):
        filename = os.path.basename(img_path)
        print(f"[{i}/{len(image_files)}] Memproses: {filename}")
        
        try:
            with Image.open(img_path) as img:
                original_size = os.path.getsize(img_path)
                original_width, original_height = img.size
                
                # Hitung ukuran baru
                if original_width <= target_width:
                    new_width, new_height = original_width, original_height
                    resize_info = " (tidak di-resize - sudah kecil)"
                    skipped_small += 1
                else:
                    ratio = target_width / original_width
                    new_width = target_width
                    new_height = int(original_height * ratio)
                    resize_info = f" (di-resize ke {new_width}px)"
                
                # Resize jika diperlukan
                if original_width > target_width:
                    img_resized = img.resize((new_width, new_height), Image.Resampling.LANCZOS)
                else:
                    img_resized = img
                
                # Konversi mode warna
                if img_resized.mode in ('RGBA', 'P', 'LA'):
                    rgb_img = Image.new('RGB', img_resized.size, (255, 255, 255))
                    
                    if img_resized.mode == 'RGBA':
                        rgb_img.paste(img_resized, mask=img_resized.split()[3])
                    elif img_resized.mode == 'LA':
                        rgb_img.paste(img_resized.convert('RGBA'), mask=img_resized.split()[1])
                    else:
                        rgb_img.paste(img_resized.convert('RGBA'), mask=img_resized.convert('RGBA').split()[3])
                    
                    final_img = rgb_img
                else:
                    final_img = img_resized.convert('RGB') if img_resized.mode != 'RGB' else img_resized
                
                # Simpan sebagai WebP
                output_name = os.path.splitext(filename)[0] + ".webp"
                output_path = os.path.join(output_folder, output_name)
                
                # Optimasi kualitas berdasarkan ukuran
                if original_width < 1000:
                    quality = 90  # Kualitas tinggi untuk gambar kecil
                elif original_width < 2000:
                    quality = 85  # Kualitas sedang
                else:
                    quality = 80  # Kualitas lebih rendah untuk gambar besar
                
                final_img.save(output_path, 'WEBP', quality=quality, optimize=True)
                
                # Hitung pengurangan ukuran
                new_size = os.path.getsize(output_path)
                size_reduction = original_size - new_size
                total_size_reduction += size_reduction
                
                print(f"   ✓ Selesai{resize_info}")
                print(f"     Ukuran: {original_width}×{original_height} → {new_width}×{new_height}")
                print(f"     File: {original_size/1024:.1f}KB → {new_size/1024:.1f}KB (hemat {size_reduction/1024:.1f}KB)")
                print()
                
                success_count += 1
                
        except Exception as e:
            print(f"   ✗ Error: {str(e)}")
            failed_files.append((filename, str(e)))
            print()
    
    # Tampilkan ringkasan
    print("=" * 60)
    print("📈 RINGKASAN DETAIL:")
    print(f"   Total diproses: {len(image_files)} gambar")
    print(f"   Berhasil: {success_count}")
    print(f"   Gagal: {len(failed_files)}")
    print(f"   Tidak di-resize (sudah kecil): {skipped_small}")
    
    if total_size_reduction > 0:
        print(f"\n💾 PENGHEMATAN UKURAN FILE:")
        print(f"   Total penghematan: {total_size_reduction / 1024:.1f} KB")
        print(f"   Rata-rata per file: {total_size_reduction / success_count / 1024:.1f} KB")
    
    if failed_files:
        print(f"\n⚠️  File yang gagal:")
        for filename, error in failed_files:
            print(f"   - {filename}: {error}")

def simple_convert(input_folder="produk", target_width=600):
    """
    Fungsi sederhana untuk konversi cepat
    """
    import os
    
    if not os.path.exists(input_folder):
        print(f"⚠️  Folder '{input_folder}' tidak ditemukan!")
        print(f"📌 Pastikan struktur folder:")
        print(f"   {os.path.abspath('.')}")
        print(f"   ├── script.py")
        print(f"   └── {input_folder}/")
        print(f"       ├── gambar1.jpg")
        print(f"       └── ...")
        return
    
    output_folder = f"{input_folder}_webp"
    
    print("🖼️  KONVERTER GAMBAR ke WEBP (600px width)")
    print("=" * 50)
    
    resize_and_convert_to_webp(
        input_folder=input_folder,
        output_folder=output_folder,
        target_width=target_width,
        quality=85
    )

if __name__ == "__main__":
    # ============================================
    # PILIH SALAH SATU METODE DI BAWAH INI:
    # ============================================
    
    # 1. METODE SEDERHANA (rekomendasi untuk pemula)
    simple_convert("produk", target_width=1080)
    
    # 2. METODE DENGAN STATISTIK DETAIL
    # batch_process_with_stats("produk", "produk_webp", target_width=600)
    
    # 3. METODE MANUAL DENGAN PENGATURAN KHUSUS
    # resize_and_convert_to_webp(
    #     input_folder="produk",
    #     output_folder="produk_webp_600px",
    #     target_width=600,
    #     quality=85
    # )