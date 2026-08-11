# Panduan Deploy Laravel ke aaPanel

Panduan ini disusun dari satu proses deploy nyata: project Laravel 12 + Filament 4, web server Apache, akses publik lewat Cloudflare Tunnel (port custom, HTTPS di-terminate di Cloudflare). Bagian "Langkah Deploy" adalah alur inti yang selalu dijalankan. Bagian "Masalah yang Pernah Ditemukan" adalah hal kondisional — hanya relevan kalau gejalanya muncul, dirujuk dari langkah terkait.

## Prasyarat

- aaPanel sudah terpasang dengan PHP, MySQL, Apache.
- Akses SSH ke server dengan user yang punya `sudo`.
- Repo Git private di GitHub.

## Langkah Deploy

### 0. Analisis kode

Sebelum menyentuh Git atau server, pelajari project secara menyeluruh:

- **Struktur project**: `app/`, `routes/`, `config/`, `database/migrations/` — paham fitur apa saja yang ada (admin panel, payment gateway, job queue, scheduled task, dst) dan langkah deploy tambahan yang dibutuhkan masing-masing.
- **`composer.json`** seluruhnya: versi PHP di `"php"`, seluruh `"require"` untuk package yang mensyaratkan extension atau setup khusus di server, seluruh `"ext-*"`.
- **`package.json`** dan pemakaian asetnya di `resources/views/` (`@vite`) — apakah build Node.js/npm dibutuhkan.
- **`.env.example`** seluruh isinya: jenis `DB_CONNECTION` dan implikasinya, tiap variabel yang dibaca oleh `config/*.php` custom.
- **Middleware & route** (`bootstrap/app.php`, `routes/`): asumsi soal HTTPS, domain, atau proxy.

Hasil dari membaca semua ini dipakai sebagai checklist di langkah-langkah berikutnya.

### 1. Bersihkan & push repo

```bash
git add -A
git commit -m "..."
```

Kalau belum ada remote:
```bash
git remote add origin https://github.com/<user>/<repo>.git
```

Push pakai Personal Access Token (PAT) sebagai password, dipasang inline di URL untuk sekali pakai:
```bash
git push https://<user>:<token>@github.com/<user>/<repo>.git main
```
Token dibuat di github.com/settings/tokens (classic, scope `repo`, expiration singkat).

*(Lihat "File SQLite ter-commit tanpa sengaja" di bagian masalah kalau `.env.example` langkah 0 menunjukkan `DB_CONNECTION=sqlite`.)*

### 2. Setup situs & database di aaPanel

Lewat aaPanel UI (Website → Add site): buat domain, pilih PHP sesuai field `"php"` di `composer.json` (langkah 0), centang Create database untuk generate database MySQL + user.

Build Node.js/npm dibutuhkan kalau `grep -rl "@vite" resources/views/` (langkah 0) ada hasilnya. Kalau kosong, tidak diperlukan.

### 3. Clone project ke server

Folder yang dibuat aaPanel dari "Add Site" **dimiliki user `www`**, bukan user SSH biasa — clone langsung ke situ gagal `Permission denied`. Clone ke folder sementara, lalu `rsync` ke folder tujuan:

```bash
sudo git clone <repo-url> /www/wwwroot/<folder-sementara>
sudo rsync -a /www/wwwroot/<folder-sementara>/ /www/wwwroot/<domain>/
sudo rm -rf /www/wwwroot/<folder-sementara>
sudo chown -R www:www /www/wwwroot/<domain>
```

Repo private minta username + password saat clone — pakai username GitHub dan PAT sebagai password saat prompt muncul.

*(Lihat "Git ownership error" di bagian masalah kalau `git pull`/`composer install` menolak dengan "dubious ownership".)*

### 4. Cek dependensi server

Sebelum menjalankan `composer install`:

```bash
# 1. versi Composer di server — bandingkan dengan versi 2.x terbaru
composer --version

# 2. daftar extension PHP yang di-require project
grep '"ext-' composer.json

# 3. extension yang sudah aktif di server — cocokkan satu-satu dengan hasil poin 2
php -m
```

Cara membaca hasilnya:
- Kalau poin 1 menunjukkan versi lama (bukan 2.x terbaru), Composer perlu diupgrade — lihat "Composer usang" di bagian masalah.
- Extension dari poin 2 yang **tidak muncul** di poin 3 berarti belum aktif dan harus diinstall dulu — lihat "Extension PHP hilang" di bagian masalah.
- Kalau semua sudah sesuai, lanjut langsung ke langkah 5.

### 5. Install dependencies

```bash
cd /www/wwwroot/<domain>
composer install --no-dev --optimize-autoloader
```

Idealnya dijalankan sebagai user `www` (`sudo -u www composer install ...`) supaya kepemilikan file konsisten dengan PHP-FPM. Kalau terpaksa dijalankan sebagai `root`, wajib `chown -R www:www` di langkah 8.

### 6. Setup `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` manual (`nano .env`), set `DB_CONNECTION=mysql` beserta host/port/nama db/user/password dari database yang dibuat di langkah 2, plus `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<domain>`, dan variabel `config/*.php` custom yang teridentifikasi di langkah 0.

*(Lihat "Password database salah" di bagian masalah kalau `migrate` gagal dengan `Access denied for user`.)*

### 7. Migrate & cache

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Fix ownership & permission

Wajib kalau langkah 5 sempat dijalankan sebagai `root`:

```bash
chown -R www:www /www/wwwroot/<domain>
chmod -R 775 storage bootstrap/cache
```

### 9. Document root

Lewat aaPanel UI (Website → domain → Settings → Site Directory): ubah dari `/` ke `/public`.

Setelah ini situs seharusnya bisa diakses. Kalau ada masalah, lihat bagian di bawah.

## Masalah yang Pernah Ditemukan

Setiap entri: gejala, penyebab, cara penyelesaian, status.

### File SQLite ter-commit tanpa sengaja

**Gejala**: ada file tanpa ekstensi (nama file = nilai `DB_DATABASE` di `.env.example`) ikut ter-commit ke repo, ketahuan lewat `ls -la` di folder project setelah clone ke server.

**Penyebab**: `.env.example` punya `DB_CONNECTION=sqlite`, file database SQLite lokal developer tidak ter-ignore di `.gitignore`.

**Penyelesaian**:
```bash
git rm --cached <nama-file-sqlite>
# tambahkan ke .gitignore: /<nama-file-sqlite> dan *.sqlite
git add .gitignore
git commit -m "Remove accidentally committed local SQLite database file"
git push ...
```
Lalu di server: `git pull` untuk menghapus file itu dari working tree juga. File tetap ada di riwayat commit lama — untuk repo private, rewrite history bisa dilewati kalau risikonya diterima.

**Status**: berhasil, terverifikasi — file hilang dari server setelah `git pull`.

---

### Git ownership error ("dubious ownership")

**Gejala**: `git pull` atau `composer install` menolak dengan pesan `detected dubious ownership in repository`.

**Penyebab**: command dijalankan oleh user yang bukan pemilik folder (folder dimiliki `www`, dijalankan oleh user SSH lain atau `root`).

**Penyelesaian**: dijalankan terpisah untuk tiap user yang butuh, karena config tersimpan di home directory masing-masing.
```bash
sudo git config --global --add safe.directory /www/wwwroot/<domain>
git config --global --add safe.directory /www/wwwroot/<domain>   # untuk user non-root juga
```

**Status**: berhasil, terverifikasi.

---

### Composer usang

**Gejala**: `composer install` gagal dengan "Your lock file does not contain a compatible set of packages", padahal `composer.lock` valid.

**Penyebab**: Composer bawaan aaPanel versi lama (ditemukan: 2.0.14 dari 2021), tidak kompatibel dengan lock file Laravel 12/Filament 4. PHP CLI-nya sendiri sudah benar — masalah murni di Composer.

**Penyelesaian**:
```bash
sudo composer self-update
```

**Status**: berhasil, terverifikasi — upgrade ke 2.10.2 langsung menyelesaikan sebagian masalah `composer install` berikutnya.

---

### Extension PHP hilang (contoh: `zip`)

**Gejala**: `composer install` gagal dengan `requires ext-<nama> * -> it is missing from your system`.

**Penyebab**: extension belum aktif di PHP server. Di kasus nyata: `openspout` (dipakai Filament untuk fitur export) butuh `ext-zip`, belum aktif.

**Penyelesaian, coba urutan ini:**

1. **aaPanel UI** — PHP → versi terkait → Install extensions, cari nama extension, install, restart PHP.
   - *Status di kasus nyata*: **gagal** — daftar kosong, tidak ada apa pun yang muncul meskipun sudah Refresh List. UI tidak bisa dipakai untuk kasus ini.

2. **Compile manual dari source PHP resmi** (dipakai kalau opsi 1 gagal, dan PHP server adalah build custom aaPanel — cek dengan `ls /www/server/php/<versi>/bin/ | grep -E "phpize|php-config"`, ada berarti compile-an aaPanel, bukan dari `apt`):

```bash
sudo apt-get install -y lib<nama-ext>-dev

# <versi-lengkap> didapat dari `php -v` (di kasus ini: 8.3.32)
cd /tmp && wget https://www.php.net/distributions/php-<versi-lengkap>.tar.gz
tar -xzf php-<versi-lengkap>.tar.gz && cd php-<versi-lengkap>/ext/<nama-ext>

sudo /www/server/php/<versi>/bin/phpize
# phpize menampilkan "Zend Module Api No: <api-no>" (di kasus ini: 20230831) — dipakai di bawah
./configure --with-php-config=/www/server/php/<versi>/bin/php-config --with-<nama-ext>
make -j$(nproc)

sudo cp modules/<nama-ext>.so /www/server/php/<versi>/lib/php/extensions/no-debug-non-zts-<api-no>/
```

Aktifkan di **dua file `.ini` terpisah** — `php-cli.ini` (dipakai `composer`/`artisan`) dan `php.ini` (dipakai PHP-FPM/website):

```bash
php --ini   # cek lokasi file yang dipakai CLI
ls /www/server/php/<versi>/etc/   # cek file yang dipakai FPM

sudo sed -i 's|^;extension = <nama-ext>.so|extension = /www/server/php/<versi>/lib/php/extensions/no-debug-non-zts-<api-no>/<nama-ext>.so|' /www/server/php/<versi>/etc/php-cli.ini
sudo sed -i 's|^;extension = <nama-ext>.so|extension = /www/server/php/<versi>/lib/php/extensions/no-debug-non-zts-<api-no>/<nama-ext>.so|' /www/server/php/<versi>/etc/php.ini
```

Restart PHP-FPM, lalu verifikasi:
```bash
sudo /etc/init.d/php-fpm-<versi> restart
php -m | grep -i <nama-ext>
```

**Status**: berhasil, terverifikasi untuk `ext-zip` di kasus nyata ini. Pola untuk extension lain (nama library dev sistem beda-beda, mis. `gd` → `libgd-dev`/`libpng-dev`/`libjpeg-dev`, `intl` → `libicu-dev`) belum diuji — sesuaikan nama library saat dipakai.

---

### Password database salah

**Gejala**: `php artisan migrate` gagal dengan `Access denied for user`.

**Penyebab**: kredensial di `.env` tidak sesuai dengan yang di-generate aaPanel.

**Penyelesaian**: reset password lewat aaPanel (Databases → Access Password), update `.env`, lalu wajib `php artisan config:clear` sebelum coba lagi (Laravel bisa cache `.env` lama).

**Status**: berhasil, terverifikasi.

---

### Redirect loop HTTPS (`ERR_TOO_MANY_REDIRECTS`)

**Gejala**: situs sudah live tapi akses via domain publik menghasilkan redirect loop di browser.

**Penyebab**: server (Apache) memaksa redirect HTTP→HTTPS lewat `public/.htaccess`, padahal HTTPS sudah di-terminate di reverse proxy/tunnel (Cloudflare) di depan origin, dan traffic ke origin memang HTTP polos. Origin tidak (dan tidak perlu) punya SSL certificate sendiri. Hanya relevan kalau situs diakses lewat reverse proxy/tunnel yang men-terminate HTTPS — kalau origin diakses langsung dengan SSL sendiri (Let's Encrypt di aaPanel), masalah ini tidak muncul.

Debug dari server dulu, pakai port yang di-set di aaPanel Website settings untuk situs ini:
```bash
curl -I http://localhost:<port>
```
Hasil `301 Moved Permanently` ke `https://localhost:<port>/` mengonfirmasi penyebab di atas.

**Penyelesaian**: di `public/.htaccess`, nonaktifkan rule HTTPS Enforcement, dan ubah rule WWW→non-WWW dari hardcode `https://` jadi `http://` (kalau rule itu ada):
```apache
# 1. WWW to NON-WWW
RewriteCond %{HTTP_HOST} ^www\.<domain> [NC]
RewriteRule ^(.*)$ http://<domain>/$1 [L,R=301]

# 2. HTTPS Enforcement (disabled — HTTPS is terminated at Cloudflare)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [L,R=301]
```
Commit → push → `git pull` di server. Verifikasi ulang dengan `curl -I http://localhost:<port>` sampai hasilnya `200 OK`.

**Status**: berhasil, terverifikasi.

---

### Mixed Content pada asset

**Gejala**: browser menampilkan warning `Mixed Content: The page at 'https://<domain>/' was loaded over HTTPS, but requested an insecure image 'http://<domain>/images/...'`.

**Penyebab**: view pakai helper `asset()` Laravel dengan benar, tapi Laravel men-generate URL `http://` karena request yang diterimanya dari reverse proxy/tunnel memang berupa HTTP (penyebab sama seperti redirect loop di atas) — Laravel tidak tahu request aslinya HTTPS di sisi pengunjung.

**Penyelesaian**: di `bootstrap/app.php`, tambahkan `trustProxies`:
```php
use Illuminate\Http\Request;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(
        at: '*',
        headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
    );
    // ... middleware lain yang sudah ada
})
```
`at: '*'` dipakai (bukan IP range spesifik Cloudflare) dengan pertimbangan origin ini hanya diakses lewat tunnel, tidak ada exposure langsung ke internet di port itu — IP range spesifik belum diuji.

Pastikan juga `APP_URL` di `.env` sudah `https://`. Setelah commit → push → `git pull` di server, rebuild cache:
```bash
php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan view:clear && php artisan view:cache
```

**Status**: berhasil, terverifikasi — warning Mixed Content hilang, gambar tampil normal.

---

### Redirect ke URL lama yang sudah dihapus

**Gejala**: buka domain, browser redirect ke path lama yang sudah dihapus dari kode (halaman/view yang memang sengaja dibuang saat pembersihan project).

**Penyebab**: bukan bug aplikasi — **cache browser** menyimpan redirect 301 lama dari versi situs sebelumnya (301 memang didesain browser untuk di-cache permanen).

**Cara memastikan** (sebelum menyimpulkan ini penyebabnya):
```bash
grep -rn "<nama-path-lama>" .          # tidak ada match di seluruh project
curl -IL http://localhost:<port>/      # dari server, langsung 200 OK, tidak ada redirect
```
Dan/atau dites di Incognito/Private window — kalau normal di situ, terkonfirmasi cache browser.

**Penyelesaian**: clear cache/cookies domain tersebut di browser biasa. Tidak ada perubahan kode yang diperlukan.

**Status**: berhasil, terverifikasi.

## Panduan Umum

Penjelasan istilah dan perintah yang dipakai berulang di seluruh dokumen ini.

### Git — commit

**Apa itu**: commit adalah "snapshot" dari perubahan file di repo, disimpan dengan pesan penjelas. Setiap commit jadi titik yang bisa dilihat/dibalik lagi nanti.

**Tujuannya**: mencatat perubahan kode secara bertahap dan terlacak, supaya ada riwayat siapa mengubah apa dan kenapa — beda dengan sekadar menyimpan file di folder.

**Caranya**:
```bash
git add -A              # tandai semua file yang berubah untuk di-commit
git commit -m "pesan"   # simpan sebagai satu commit dengan pesan penjelas
```

### Git — push

**Apa itu**: mengirim commit yang ada di komputer/server lokal ke repo remote (GitHub).

**Tujuannya**: supaya kode tersimpan di tempat terpusat yang bisa diakses dari server lain (mis. untuk di-`clone`/`pull` ke server aaPanel), dan sebagai backup di luar komputer sendiri.

**Caranya**:
```bash
git remote add origin https://github.com/<user>/<repo>.git   # sekali saja, kalau remote belum ada
git push https://<user>:<token>@github.com/<user>/<repo>.git main
```
Repo private butuh autentikasi — pakai username GitHub dan Personal Access Token (PAT) sebagai password (dibuat di github.com/settings/tokens, scope `repo`).

### Git — clone

**Apa itu**: menyalin seluruh repo (kode + riwayat commit) dari remote ke folder baru di komputer/server lain.

**Tujuannya**: mengambil kode project ke server untuk pertama kali, supaya server punya salinan kerja dari kode yang sudah di-push.

**Caranya**:
```bash
git clone <repo-url> <folder-tujuan>
```

### Git — pull

**Apa itu**: mengambil commit terbaru dari remote dan menggabungkannya ke folder kerja lokal.

**Tujuannya**: menyinkronkan kode di server dengan perubahan terbaru yang sudah di-push — dipakai tiap kali ada update kode setelah deploy pertama.

**Caranya**:
```bash
git pull origin main
```

### SSH

**Apa itu**: protokol untuk mengakses terminal server dari jarak jauh secara aman (terenkripsi).

**Tujuannya**: menjalankan command di server aaPanel (install, konfigurasi, dst) tanpa perlu akses fisik ke mesinnya.

**Caranya**: pakai aplikasi terminal dengan `ssh user@alamat-server`, atau lewat fitur SSH Terminal bawaan aaPanel di web UI.

### `sudo`

**Apa itu**: menjalankan satu command dengan hak akses administrator (root), bukan hak akses user biasa.

**Tujuannya**: sebagian besar operasi di server (install package, ubah file `.ini` PHP milik sistem, restart service) butuh hak akses lebih tinggi dari user SSH biasa.

**Caranya**: tambahkan `sudo` di depan command, mis. `sudo apt-get install ...`. Akan diminta password user tersebut.

### `chown` dan `chmod`

**Apa itu**: `chown` mengubah **pemilik** file/folder (user dan grup); `chmod` mengubah **izin akses** (siapa boleh baca/tulis/eksekusi).

**Tujuannya**: PHP-FPM (yang melayani website) berjalan sebagai user tertentu (biasanya `www` di aaPanel) — file yang dimiliki user lain atau punya izin terlalu ketat bisa bikin website gagal baca/tulis file (log, cache, upload).

**Caranya**:
```bash
chown -R www:www /path/ke/folder     # ganti pemilik jadi user:grup "www", -R = rekursif ke semua isi folder
chmod -R 775 /path/ke/folder         # izin baca/tulis/eksekusi untuk pemilik & grup, baca/eksekusi untuk lainnya
```

### Composer & `composer.json`

**Apa itu**: Composer adalah package manager untuk PHP — mengelola library/dependency yang dipakai project Laravel. `composer.json` mendaftarkan dependency yang dibutuhkan; `composer.lock` mengunci versi persis yang terinstall.

**Tujuannya**: memastikan semua library yang dibutuhkan project (Laravel framework sendiri, Filament, dst) terinstall dengan versi yang kompatibel.

**Caranya**:
```bash
composer install --no-dev --optimize-autoloader
```
`--no-dev` melewati package yang hanya dibutuhkan saat development (testing, debugging), tidak perlu di production.

### Extension PHP

**Apa itu**: modul tambahan yang memperluas kemampuan inti PHP (mis. `zip` untuk baca/tulis file ZIP, `gd`/`imagick` untuk olah gambar). Beberapa package Composer mensyaratkan extension tertentu aktif di server (ditandai `"ext-*"` di `composer.json`).

**Tujuannya**: tanpa extension yang dibutuhkan aktif, `composer install` akan gagal atau fitur yang bergantung padanya (export Excel, resize gambar, dst) tidak akan jalan.

**Caranya**: cek aktif/tidaknya dengan `php -m`, aktifkan lewat aaPanel UI (PHP → Install extensions) atau compile manual — detail di bagian "Extension PHP hilang" di "Masalah yang Pernah Ditemukan".

### File `.env`

**Apa itu**: file konfigurasi berisi nilai yang berbeda-beda antar environment (lokal, staging, production) — kredensial database, API key, mode debug, dst. `.env.example` adalah template tanpa nilai rahasia, ikut di-commit ke Git; `.env` yang sebenarnya **tidak** boleh ikut ter-commit.

**Tujuannya**: memisahkan konfigurasi sensitif/spesifik-environment dari kode aplikasi, supaya kode yang sama bisa jalan di environment berbeda hanya dengan `.env` yang berbeda.

**Caranya**:
```bash
cp .env.example .env    # salin template jadi file aktif
php artisan key:generate # generate APP_KEY unik untuk environment ini
```
Lalu edit `.env` manual untuk isi nilai sesuai environment (database, dsb).

## Cara memandu proses ini (untuk agen/asisten)

- **Satu perintah per giliran.** Saat memandu user menjalankan command di server via SSH, berikan satu perintah, tunggu user paste hasilnya, baru lanjut ke perintah berikutnya. Jangan gabungkan beberapa command sekaligus untuk dijalankan mandiri oleh user — kalau salah satu gagal di tengah, titik kegagalan jadi sulit dilacak.
- **Analisis dulu sebelum tanya.** Ketika muncul error atau perilaku aneh (file asing, redirect tak terduga, mixed content), telusuri dulu sendiri pakai tool yang tersedia — grep di codebase, baca file config, jalankan command diagnostik (`curl`, `file`, `ls`) — sebelum melempar pertanyaan ke user tentang kemungkinan penyebabnya.
- **Jangan menulis langkah sebagai "sudah teruji" kalau sebenarnya belum dijalankan.** Kalau menambahkan sesuatu yang belum diuji, tandai eksplisit sebagai asumsi, atau jangan dimasukkan sama sekali.
