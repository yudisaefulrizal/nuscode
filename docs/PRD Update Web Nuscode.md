# PRD Teknis: Pemisahan Konten (Artikel/Produk/Halaman Utama/Tools) + Payment Gateway Midtrans

## 1. Latar Belakang & Masalah

Website Laravel ini saat ini menyimpan seluruh konten (4 artikel, halaman utama, halaman utility) sebagai file Blade standalone yang di-hardcode langsung lewat closure di `routes/web.php` — tidak ada Controller, Model, atau tabel database untuk konten apa pun di luar tabel default Laravel (`users`, `cache`, `jobs`, dst, yang sudah diimpor dari backup produksi). Admin panel Filament (v4.0) sudah terpasang aktif di `/admin` tapi foldernya kosong, belum ada satu Resource pun.

Selain itu ditemukan artefak lama: `alatpesta.blade.php` + `public/alatpesta.js` + `public/alatpesta.css` (katalog sewa alat pesta, 10 item hardcode) yang salah sambung routing-nya (`/alatpesta` di `routes/web.php:11` malah memanggil `view('welcome')`) sehingga tidak pernah tampil. **Dikonfirmasi oleh pemilik: ini bukan bisnisnya** — bisnis sebenarnya adalah menjual **source code, prompt AI, dan materi ajar sekolah (SD/SMP/dst per mata pelajaran)**. Artefak ini akan dihapus total, tidak dipakai ulang.

## 2. Tujuan

1. Pisahkan konten jadi 4 kelompok terstruktur: **Artikel**, **Produk** (3 jenis: Source Code / Prompt / Materi Ajar), **Halaman Utama**, **Tools** (chatbot, review-jurnal).
2. Artikel & Produk dikelola penuh lewat CRUD di admin Filament (`/admin`), bukan hardcode file.
3. Alur jual-beli sungguhan via **Midtrans (sandbox)**: guest checkout (tanpa akun), verifikasi pembayaran server-side, pengiriman produk digital via halaman sukses + email cadangan.

## 3. Non-Goals (di luar scope PRD ini)

- Checkout tetap **guest** (tidak perlu login untuk beli) — akun klien dibuat **otomatis setelah bayar** (lihat §7.7), bukan lewat form registrasi manual; tidak ada halaman "Daftar Akun" untuk pembeli.
- Tidak membangun fitur "lupa password" self-service untuk area klien di tahap ini — kalau pembeli lupa password akun otomatisnya, jalur mitigasinya adalah WhatsApp ke admin (§7.7).
- Tidak mengubah konten/desain halaman utama (`welcome.blade.php`) — hanya dipindah lokasi file dan diperbaiki link artikelnya.
- Tidak membangun sistem email marketing/notifikasi lanjutan — hanya email transaksional dasar (lihat §7.7 untuk isinya).

## 3.1 Persiapan sebelum mulai coding

Project ini **belum punya git** — sebelum menyentuh file apa pun untuk PRD ini, jalankan `git init` + commit awal (snapshot kondisi sekarang) supaya ada titik aman untuk rollback kalau ada langkah yang salah di tengah implementasi besar ini.

## 4. Skema Database (migrations)

| Tabel | Kolom |
|---|---|
| `articles` | `title`, `slug` (unique), `excerpt` nullable, `content` (longText, HTML mentah termasuk `<style>` asli — lihat §6.1), `author` nullable, `published_at` nullable, timestamps |
| `source_codes` | `name`, `slug` (unique), `description` (text), `price` (decimal 12,2), **`file_path`** (string — path zip di storage privat, lihat §6.3), `demo_url` nullable (link demo publik, beda dari file_path), `tech_stack` (string/JSON via `TagsInput`), `image` nullable, timestamps |
| `prompts` | `name`, `slug` (unique), `description` (text), `price` (decimal 12,2), **`file_path`** (string — path zip di storage privat, isi prompt lengkap dikemas di sini), `prompt_content` (longText, opsional — cuma referensi internal/preview admin, bukan yang dikirim ke pembeli), `example_output` (longText nullable, preview publik di halaman produk), `category` nullable, `image` nullable, timestamps |
| `materi_ajars` | `name`, `slug` (unique), `description` (text), `price` (decimal 12,2), **`file_path`** (string — path zip di storage privat), `jenjang` (string: SD/SMP/SMA/SMK), `mata_pelajaran` (string), `image` nullable, timestamps |
| `orders` | `order_number` (unique — format `ORD-{tanggal}-{random 6 karakter}`, dijamin unik untuk `order_id` Midtrans), `orderable_type`+`orderable_id` (morphTo ke SourceCode/Prompt/MateriAjar), `user_id` nullable (FK ke `users`, diisi saat akun klien diprovisi), `buyer_name`, `buyer_email`, `buyer_phone` nullable, `amount` (decimal 12,2), `status` (pending/paid/failed/expired/cancelled, default pending), `snap_token` nullable, `payment_type` nullable, `download_token` (unique, random), `account_provisioned_at` nullable timestamp, `paid_at` nullable, timestamps |

**Catatan**: tabel `orders` **TIDAK** punya kolom untuk menyimpan password apa pun (bahkan terenkripsi) — password baru yang di-generate (§7.7) hanya lewat `Cache` sementara (TTL pendek), tidak pernah disentuh ke tabel manapun secara permanen. Ini konsisten dengan prinsip §7.8: tidak ada password yang tersimpan dalam bentuk bisa-dibaca-ulang di database.

Semua model produk implementasikan `getRouteKeyName(): string { return 'slug'; }`.

**Standarisasi `file_path`**: ketiga tabel produk (`source_codes`, `prompts`, `materi_ajars`) sama-sama punya kolom `file_path` — produk dikirim ke pembeli sebagai **satu file ZIP** yang disimpan di server, bukan link eksternal. Ini menyederhanakan alur unduh (`/unduh/{download_token}`) jadi satu logic seragam untuk ketiga jenis produk: `Storage::disk('local')->download($order->orderable->file_path)`, tanpa percabangan per jenis produk. Lihat §6.3 untuk alasan kenapa harus di storage privat, bukan `public/`.

## 5. Models

`app/Models/Article.php`, `SourceCode.php`, `Prompt.php`, `MateriAjar.php`, `Order.php` — ikuti gaya `User.php` existing persis: `protected $fillable` array polos + PHPDoc `@var list<string>`, cast lewat method `protected function casts(): array` (konvensi Laravel 12 project ini, bukan property `$casts`).

### 5.1 Wajib: pemisahan akun Admin vs Client (celah keamanan yang harus ditutup)

Akun **Admin** (pemilik, akses `/admin`) dan akun **Client** (pembeli, auto-provisioned saat checkout, §7.7) sama-sama disimpan di satu tabel `users` — tanpa pembeda, seorang pembeli yang baru dapat password lewat email berpotensi mencoba login ke `/admin`, dan **Filament secara default tidak otomatis menolaknya** kalau tidak diberi pembatas.

Perbaikan wajib:
1. Migration tambahan: `ALTER TABLE users ADD COLUMN role VARCHAR DEFAULT 'client'` (nilai: `admin` atau `client`).
2. `User.php` implementasikan interface `Filament\Models\Contracts\FilamentUser`: method `canAccessPanel(Panel $panel): bool { return $this->role === 'admin'; }` — ini yang benar-benar mencegah akun client login ke `/admin`, bukan sekadar asumsi.
3. Provisioning akun client di webhook (§7.7 langkah 3) **wajib eksplisit set** `role: 'client'` saat `User::create()`.
4. Akun admin yang sudah ada sekarang (pemilik) perlu di-set `role = 'admin'` secara manual (lewat `php artisan tinker` atau seeder sekali-jalan) setelah migration jalan, supaya tidak ikut ter-lock keluar dari `/admin` miliknya sendiri.
5. `UserResource` (§6.2, untuk kelola admin lain) otomatis set `role: 'admin'` saat admin baru dibuat lewat situ.

`Order::orderable()` → `morphTo()`, `Order::user()` → `belongsTo(User::class)` (nullable, diisi saat provisioning akun, §7.7). `Order::generateDownloadToken()` → random 40 karakter, dipanggil saat `Order` dibuat.

## 6. Filament Resources (`/admin`)

**Keputusan: tetap pakai Filament** (bukan admin custom) — untuk kebutuhan CRUD sebanyak ini (Artikel + 3 jenis Produk + Order), Filament lebih efisien karena auth, validasi form, upload file, dan tabel search/sort/pagination sudah otomatis tersedia; admin custom akan butuh menulis ulang semua itu dari nol.

Resource untuk `Article`, `SourceCode`, `Prompt`, `MateriAjar`, `Order` (Order: list + view detail status saja, tanpa create manual — order lahir dari alur checkout, bukan input admin).

Filament v4 pakai `Filament\Schemas\Schema` terpadu (`public static function form(Schema $schema): Schema`), bukan `Forms\Form`/`Infolists\Infolist` terpisah seperti v3.

### 6.1 Keputusan teknis: field `content`/`prompt_content` pakai `Textarea`, bukan `RichEditor`

Artikel lama menyimpan HTML mentah + `<style>` custom per artikel (diagram OSI layer, blok sitasi akademik). `RichEditor` Filament (berbasis Tiptap) akan mensanitasi dan merusak markup ini saat disimpan. `Textarea` menyimpan string apa adanya tanpa dependensi baru — dipakai juga untuk `prompt_content` agar isi prompt tidak diubah otomatis.

Field upload `image` (thumbnail produk, boleh publik) pakai `FileUpload` standar Filament dengan disk `public` (`storage/app/public/...`, symlink `public/storage`, sudah ada di `.gitignore:18`).

### 6.2 Halaman/fitur admin yang direkomendasikan

- **Dashboard** (Filament widget) — jumlah artikel, jumlah produk per jenis, order pending/paid hari ini, total pendapatan, order terbaru.
- **OrderResource**: tambahkan Filament Action kustom "Kirim Ulang Email Link Unduh" di tiap baris — penting karena email transaksional masih `log`-only (§7.4) sampai SMTP asli aktif, admin butuh cara manual kirim link ke pembeli kalau email gagal.
- **Manajemen Admin User**: pakai `UserResource` bawaan Filament (perlu dibuat, saat ini belum ada Resource untuk `User`) supaya bisa kelola akun admin lain jika lebih dari satu orang mengelola web ini nantinya.
- **Log aktivitas** (opsional, pertimbangkan `spatie/laravel-activitylog` + plugin Filament-nya) — catat siapa mengubah harga/produk apa kapan, penting untuk audit begitu transaksi uang sungguhan terlibat.
- **Export laporan** — tombol export Order ke CSV (Filament punya built-in export action) untuk pembukuan.

### 6.3 Penyimpanan file produk (ZIP) — wajib privat, bukan `public/`

Kolom `file_path` (ketiga tabel produk) **HARUS** pakai `FileUpload::make('file_path')->disk('local')->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])->maxSize(102400)` (disk privat default Laravel, folder `storage/app/`, TIDAK di-symlink ke `public/storage`; batasi tipe file ke ZIP saja + maks ±100MB supaya admin panel tidak jadi celah upload file sembarangan, meski risikonya rendah karena hanya admin yang punya akses). Kalau file ZIP disimpan di disk `public`, siapa pun yang tahu atau menebak URL-nya bisa mengunduh produk berbayar **tanpa bayar sama sekali** — ini pelanggaran keamanan serius untuk toko digital. `/unduh/{download_token}` (§7) adalah **satu-satunya** cara file ini boleh diakses, dan route itu wajib cek `Order::where('download_token', $token)->where('status', 'paid')->firstOrFail()` sebelum melayani file-nya (`Storage::disk('local')->download(...)`), bukan `response()->file()` langsung ke path publik.

## 7. Routes (`routes/web.php`, restrukturisasi total)

```
Home:      GET /                                      → view('pages.welcome')
Articles:  GET /artikel                                → ArticleController@index
           GET /artikel/{article:slug}                 → ArticleController@show
Produk:    GET /produk                                 → view('products.index') (hub kecil, 3 kartu link kategori)
           GET /produk/source-code                     → SourceCodeController@index
           GET /produk/source-code/{sourceCode:slug}    → SourceCodeController@show
           GET /produk/prompt                           → PromptController@index
           GET /produk/prompt/{prompt:slug}             → PromptController@show
           GET /produk/materi-ajar                      → MateriAjarController@index
           GET /produk/materi-ajar/{materiAjar:slug}    → MateriAjarController@show
Checkout:  POST /checkout/{type}/{slug}                → CheckoutController@store (`{type}` = `source-code`|`prompt`|`materi-ajar`, di-map manual ke model SourceCode/Prompt/MateriAjar dalam controller, bukan lewat route-model-binding karena beda tabel; cek email terdaftar dulu — lihat §7.8; butuh parameter `confirm_new_password` saat pembeli sudah pilih "Lanjutkan & Kirim Password Baru" supaya tidak cek ulang di submit kedua)
           POST /midtrans/notification                 → MidtransController@notification (webhook)
           GET /api/order/{order:order_number}/status   → OrderStatusController@show (dipoll dari modal)
           GET /unduh/{order:download_token}            → PaymentController@download
Akun:      GET/POST /akun/login                          → AuthController@login (customer-facing, terpisah dari /admin Filament)
           POST /akun/logout                             → AuthController@logout
           GET /akun/produk-saya                         → DashboardController@index (auth middleware; list semua Order milik user login)
Tools:     GET /chatbot, GET /review-jurnal              (pindah folder saja, tanpa DB)
```

Keputusan: tiap jenis produk punya controller & halaman sendiri (`SourceCodeController`, `PromptController`, `MateriAjarController`), bukan satu `ProductController` gabungan dengan tab — lebih konsisten dengan 3 tabel terpisah (§4) dan lebih baik untuk SEO per audiens (developer/pengguna prompt/guru punya kebutuhan beda). `/produk` tetap ada sebagai hub kecil berisi link ke 3 kategori.

Dihapus: 4 closure artikel lama, route `/alatpesta` (`routes/web.php:11`), group `cors` kosong yang tidak dipakai (`routes/web.php:28-30`).

`bootstrap/app.php:13-15` — tambahkan pengecualian CSRF untuk webhook: `$middleware->validateCsrfTokens(except: ['midtrans/notification']);` (POST eksternal dari Midtrans tidak membawa token CSRF Laravel).

**Rate limiting (belum pernah dibahas — gap yang ditemukan di audit final, konsisten dengan temuan serupa di endpoint `/remove-background` pada audit sebelumnya):** tambahkan `throttle` middleware pada endpoint baru yang bisa disalahgunakan kalau tidak dibatasi:
- `POST /checkout/{type}/{slug}` — mis. `throttle:10,1` (maks 10 percobaan/menit per IP) — mencegah spam pembuatan `Order` yang bisa memanggil Midtrans API berkali-kali (biaya/limit API).
- `GET /api/order/{order_number}/status` — mis. `throttle:30,1` — endpoint ini dipoll otomatis tiap beberapa detik, batasi supaya tidak disalahgunakan untuk polling agresif di luar alur normal.
- `POST /akun/login` — mis. `throttle:5,1` — standar mencegah brute-force password (Laravel punya helper `RateLimiter`/`throttle` bawaan untuk ini).

### 7.1 Cleanup artefak alatpesta

Hapus total: `resources/views/alatpesta.blade.php`, `public/alatpesta.js`, `public/alatpesta.css`, seluruh entri `/alatpesta/*` di `public/sitemap.xml`, rule redirect root→`/alatpesta` di `public/.htaccess:16-17`.

### 7.2 Struktur Views

Folder baru: `resources/views/{layouts,articles,products,pages,tools,checkout}/`.
- `pages/welcome.blade.php` — pindahan `welcome.blade.php`; perbaiki link artikel hardcode (baris ~569-570) jadi `route('articles.show', 'osi-layers')` dst.
- `articles/index.blade.php`, `articles/show.blade.php` (render `{!! $article->content !!}`, tanpa escape).
- `products/index.blade.php` (hub kecil, 3 kartu link kategori), `products/source-code/index.blade.php` + `show.blade.php`, `products/prompt/index.blade.php` + `show.blade.php`, `products/materi-ajar/index.blade.php` + `show.blade.php` — tiap halaman show punya tombol "Beli" → form checkout.
- `checkout/form.blade.php` (nama/email/HP) + Snap.js: `<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">`.
- `pages/payment-status.blade.php`, `pages/download.blade.php`.
- `tools/chatbot.blade.php`, `tools/review-jurnal.blade.php` — pindah apa adanya.

**Risiko CSS artikel**: jangan paksa satukan 4 blok `<style>` artikel jadi satu stylesheet bersama (belum terverifikasi identik — ada CSS diagram spesifik per artikel). Simpan seluruh `<style>` asli tiap artikel sebagai bagian `content` di DB, dirender apa adanya — menerima duplikasi demi nol regresi visual.

### 7.3 Migrasi konten artikel

Command sekali-jalan `php artisan make:command MigrateArticleContent` — baca isi asli tiap file (region body+style yang sudah dipetakan: osilayer baris 779-1127, Cyber-Security-Digital-Twin 738-1038, monitoring 739-980, rancang-bangun 191-692, plus `<style>` block masing-masing), insert ke `Article::create()` dengan title/author/slug asli. Jalankan sekali, lalu hapus 4 file Blade artikel lama.

### 7.4 Integrasi Midtrans (sandbox)

`.env` tambahan:
```
MIDTRANS_MERCHANT_ID=G969520341
MIDTRANS_CLIENT_KEY=SB-Mid-client-k5f38v4C1IyVeVLG
MIDTRANS_SERVER_KEY=SB-Mid-server-Q3edJ1giEp_qL3w8MX44sfBN
MIDTRANS_IS_PRODUCTION=false
```
(Sandbox/testing, aman di `.env` lokal — `.env` sudah masuk `.gitignore:3`.) Package: `composer require midtrans/midtrans-php`.

`config/midtrans.php` (baru):
```php
return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
];
```

**Metode bayar: QRIS dinamis saja.** Saat membuat transaksi Snap, set `enabled_payments: ['other_qris']` supaya popup Snap hanya menampilkan opsi QRIS (bisa dipindai GoPay/OVO/Dana/ShopeePay/m-banking apa pun yang support QRIS) — tidak menampilkan VA/transfer bank/kartu/e-wallet spesifik lain. Ini mempermudah scope: satu metode bayar, satu pengalaman, dan karena QRIS selalu instan (dibayar dalam sesi yang sama), tidak perlu cabang "tunggu lama" seperti VA — lihat §7.5 yang sudah disederhanakan.

### 7.5 Alur checkout — dioptimalkan untuk kemudahan client (tanpa bertele-tele)

Prinsip: pembeli **tidak pernah pindah halaman** dari halaman produk sampai dapat link unduh — semua lewat modal + AJAX + popup Snap, bukan navigasi antar-halaman terpisah.

1. Di halaman show produk, klik "Beli" → buka **modal** (bukan pindah halaman). Form hanya **email (wajib)** + nama (wajib) + **no HP (opsional, untuk follow-up WA)** — minimal field demi mengurangi friksi.
   - Form auto-terisi dari `localStorage` (nama/email tersimpan dari pembelian sebelumnya di browser yang sama) — pembeli ulang tidak perlu ketik dari nol.
2. Submit form via `fetch()` (AJAX, tanpa reload) → `POST /checkout/{type}/{slug}` → `CheckoutController` buat `Order` (status `pending`, `amount` dari harga produk, `order_number` unik, `download_token` random) → panggil Midtrans Snap API → kembalikan `snap_token` sebagai JSON.
3. Frontend langsung buka popup Snap.js di halaman yang sama (tanpa redirect) memakai token itu.
4. Di dalam modal yang sama, tunjukkan QR code (Snap menampilkan ini langsung di popup) + tampilkan status berdasarkan callback Snap.js:
   - Pembeli scan QRIS pakai app apa pun (GoPay/OVO/Dana/ShopeePay/m-banking) → bayar → Snap `onSuccess` terpicu.
   - Setelah `onSuccess`, mulai **polling ringan** (mis. tiap 2-3 detik, maksimal ~30 detik) ke endpoint status Order kecil (`GET /api/order/{order_number}/status`) — begitu backend konfirmasi `paid` (lewat webhook, lihat poin 5, biasanya tiba dalam hitungan detik untuk QRIS), tombol **"Unduh Sekarang"** langsung muncul di modal yang sama, tanpa pindah halaman sama sekali.
5. Midtrans POST ke `/midtrans/notification` (webhook, server-to-server) → **wajib verifikasi signature** (`sha512(order_id+status_code+gross_amount+ServerKey)`) sebelum update status — jangan pernah percaya callback client-side untuk menandai lunas. Saat status → `paid`: set `paid_at`, kirim Mailable berisi link `/unduh/{download_token}` (sebagai cadangan/bukti, bukan jalur utama karena poin 4 sudah menampilkan link langsung).
6. `/unduh/{download_token}` tetap ada sebagai halaman mandiri (untuk link di email atau kalau pembeli tutup browser sebelum sempat klik "Unduh Sekarang"), tapi dalam flow normal QRIS pembeli sudah dapat produknya langsung dari modal tanpa perlu buka email.

**Catatan penting**: `.env` saat ini `MAIL_MAILER=log` — email belum benar-benar terkirim sampai SMTP asli dikonfigurasi. Karena QRIS instan dan jalur utama (modal → unduh langsung) tidak bergantung pada email, ini **tidak blocking** untuk go-live — SMTP tetap direkomendasikan disiapkan untuk cadangan/bukti pembelian, tapi bukan prasyarat mutlak seperti kalau pakai VA/transfer bank.

#### 7.5.1 Alur gagal/batal/kedaluwarsa (belum pernah dibahas sebelumnya — gap yang ditemukan di audit final)

Semua desain checkout sejauh ini fokus ke jalur sukses. Client juga perlu pengalaman jelas kalau bayar **tidak selesai**:

- **Pembeli tutup popup Snap tanpa scan/bayar** (callback `onClose`) → modal tetap terbuka, tampilkan pesan *"Pembayaran dibatalkan. Coba lagi?"* + tombol "Coba Lagi" yang memanggil ulang popup Snap pakai `snap_token` yang sama (selama belum kedaluwarsa) — tidak perlu submit form checkout dari nol lagi.
- **QR code kedaluwarsa** (default Midtrans ±15 menit untuk QRIS) tanpa dibayar → Midtrans kirim notifikasi `expire` ke webhook → `Order.status = 'expired'`. Kalau pembeli masih di halaman (jarang, tapi mungkin), polling (§7.5 poin 4) mendeteksi status ini dan ganti pesan jadi *"Waktu pembayaran habis. Coba lagi?"* + tombol "Coba Lagi" (bikin `Order` baru dengan `snap_token` baru, produk & harga sama).
- **Pembayaran ditolak/dibatalkan** (callback `onError`, atau webhook kirim status `deny`/`cancel`) → `Order.status` diupdate sesuai (`failed`/`cancelled`), modal tampilkan *"Pembayaran gagal. Coba lagi atau hubungi kami via WhatsApp"* (+ link WA §7.7, untuk kasus pembeli sudah kepotong saldo tapi status di sistem tidak sesuai — biar admin bisa bantu cek manual).
- Untuk semua status non-`paid` ini: **tidak ada** provisioning akun/reset password/pengiriman email produk — hanya `Order.status` yang berubah.

### 7.6 Konsistensi desain

Halaman baru (produk, checkout modal, status) pakai token desain yang sudah ada di `welcome.blade.php:13-21`, bukan gaya baru: `--primary:#343434`, `--secondary:#039f5b`, `--accent:#ffcc33`, font **Poppins** (400/600/700) + Font Awesome, gaya card shadow-lembut seperti `.value-card`/`.project-card`, header sticky blur. Tujuannya: produk/checkout terasa menyatu dengan situs, bukan seperti toko online yang ditempel terpisah.

**Responsif mobile** (belum disebut eksplisit sebelumnya): `welcome.blade.php` sudah punya pola mobile menu (`.menu-toggle`, breakpoint responsive di CSS-nya) — semua halaman baru (produk, modal checkout, akun) wajib pakai pola responsive yang sama, bukan cuma didesain untuk desktop lalu dicek belakangan. Modal checkout khususnya harus dites tampil wajar di layar HP kecil (form + QR code Snap harus muat tanpa scroll horizontal), karena QRIS di Indonesia mayoritas dipakai lewat HP.

**State kosong/error yang perlu didesain juga** (bukan cuma happy path): halaman `/produk/{jenis}` kalau belum ada produk sama sekali (misal sebelum seed data §8 diisi) — tampilkan pesan "Belum ada produk tersedia" yang rapi, bukan tabel/grid kosong polos; halaman 404 default Laravel untuk slug produk/artikel yang tidak ditemukan cukup dipakai apa adanya (tidak perlu desain custom, di luar scope).

### 7.7 Akun Klien Otomatis & 3 Jalur Pengambilan Produk

Setelah pembayaran QRIS terkonfirmasi (`Order.status` → `paid`), pembeli bisa ambil produknya lewat **3 jalur**: (1) email, (2) halaman "Produk Saya" di area member, (3) WhatsApp langsung ke admin sebagai mitigasi kalau 2 jalur lain bermasalah.

**Webhook `MidtransController@notification` — urutan lengkap & guard idempotensi (Midtrans bisa kirim notifikasi lebih dari sekali untuk Order yang sama, webhook harus aman dipanggil berkali-kali):**

0. Verifikasi signature (§7.5 poin 5) — kalau tidak valid, tolak (jangan proses apa pun).
1. `if ($order->status === 'paid') { return response('OK'); }` — **guard paling awal**: kalau Order ini sudah pernah diproses jadi `paid` sebelumnya, langsung berhenti di sini, jangan ulangi langkah apa pun di bawah (mencegah email terkirim dobel/password ter-generate ulang kalau Midtrans retry notifikasi).
2. Update `status` → `paid`, `payment_type`, `paid_at`.
3. **Provisioning akun** (dilewati sepenuhnya kalau `orders.user_id` sudah terisi sejak checkout — artinya pembeli sudah login saat beli, skenario B §7.8):
   - Cek `User::where('email', $order->buyer_email)->first()`.
   - **Belum ada** (pembeli baru) → buat User baru dengan `role: 'client'` (§5.1 — wajib, supaya akun ini tidak bisa akses `/admin`), generate password (`Str::random(10)`), simpan **hanya dalam bentuk hash** lewat `casts()` (tidak pernah disimpan dalam bentuk bisa-dibaca-ulang — lihat §7.8).
   - **Sudah ada** dan pembeli sebelumnya memilih "Lanjutkan & Kirim Password Baru" di checkout (§7.8 skenario A) → generate password baru, reset (`$user->update(['password' => $newPassword])`) — reset ini hanya terjadi karena pembeli **memilih sendiri** opsi itu.
   - Simpan password plaintext yang baru saja dibuat ke **`Cache::put("order-password:{$order->order_number}", $newPassword, now()->addMinutes(10))`** — **bukan** kolom tabel apa pun, murni cache sementara dengan kedaluwarsa otomatis 10 menit sebagai jembatan singkat antara webhook (server-to-server) dan endpoint status yang dipoll browser (§7.5 poin 4). Kalau order ini dari pembeli yang sudah login (tidak ada password baru), langkah ini dilewati.
   - Set `orders.user_id` dan `orders.account_provisioned_at = now()`.
4. **Kirim email** (memakai password plaintext yang masih ada di memory saat ini juga, bukan dari cache) — isi: link unduh, WhatsApp, dan (kalau ada password baru di langkah ini) email+password akun. Kalau pembeli sudah login (tidak ada password baru) → email jadi struk biasa, hanya link unduh, tanpa info akun.

**Tampilan halaman/modal setelah bayar sukses** (kanal kedua, tambahan — bukan pengganti email di atas, TIDAK bergantung satu sama lain):
- Link unduh langsung (§7.5).
- Endpoint status (`GET /api/order/{order_number}/status`) baca password dari cache pakai **`Cache::pull("order-password:{$order->order_number}")`** (`pull` = ambil-lalu-hapus dalam satu operasi atomik — begitu dibaca sekali, otomatis hilang dari cache, tidak perlu logic null-manual terpisah). Kalau ada nilainya, tampilkan di modal dengan catatan "info yang sama juga sudah dikirim ke email Anda". Kalau cache sudah kosong (baik karena sudah pernah dibaca, atau lewat 10 menit, atau memang tidak ada password baru untuk order ini) → tidak tampilkan apa-apa soal akun, cukup link unduh saja.
- Tombol/link **WhatsApp**: `https://wa.me/6281319952059?text=Halo%2C+saya+butuh+bantuan+untuk+order+{{ $order->order_number }}` (nomor +62 813-1995-2059, format wa.me tanpa spasi/strip/plus).

**Halaman "Produk Saya" (`/akun/produk-saya`, protected `auth` middleware):**
- List semua `Order` milik `auth()->user()` dengan status `paid`, tiap baris tampilkan nama produk + link unduh (`/unduh/{download_token}`, tidak pernah kedaluwarsa selama akun aktif).
- Login (`/akun/login`) pakai auth Laravel standar (`Auth::attempt` terhadap tabel `users`) — **terpisah dari login Filament `/admin`** (beda guard/beda tujuan: satu untuk admin kelola konten, satu untuk pembeli lihat produk yang sudah dibeli).

### 7.7.1 Tampilan halaman client (`resources/views/akun/`)

Belum dirinci sebelumnya — folder baru: `resources/views/akun/{login,produk-saya}.blade.php`. Desain pakai token yang sama seperti §7.6 (warna/font Nuscode), bukan tampilan Filament — ini halaman customer-facing, bukan admin.

- **`akun/login.blade.php`**: form sederhana 2 field (email, password) + tombol submit. Di bawah form, teks kecil: *"Lupa password? Hubungi kami via WhatsApp"* dengan link `wa.me` yang sama polanya seperti §7.7 (tanpa fitur "lupa password" self-service, sesuai §3 Non-Goals) — ini jalur mitigasi utama untuk kasus lupa password di halaman login (beda dari §7.8 yang mitigasinya di titik checkout).
- **`akun/produk-saya.blade.php`**: daftar produk yang sudah dibeli (nama produk, jenis — badge Source Code/Prompt/Materi Ajar, tanggal beli, tombol "Unduh" langsung ke `/unduh/{download_token}`), diurutkan dari pembelian terbaru. Kalau belum pernah beli apa pun (edge case: akun ada tapi list kosong — jarang terjadi tapi mungkin kalau akun dibuat manual), tampilkan pesan kosong yang ramah, bukan halaman kosong polos. Ada tombol/link logout (`POST /akun/logout`) di halaman ini.
- **Navigasi**: tambahkan link kecil "Login" / "Produk Saya" (tergantung status login) di header situs (`pages/welcome.blade.php` dan semua halaman publik yang pakai layout serupa) — supaya pembeli yang sudah pernah beli tahu ke mana harus kembali untuk lihat produknya, tidak hanya mengandalkan link dari email.

### 7.8 Dua Skenario Tambahan pada Checkout

**A. Guest checkout, tapi email yang diketik ternyata sudah terdaftar sebagai akun (dari pembelian sebelumnya):**

Alasan kenapa ini butuh langkah tambahan (bukan reset password otomatis diam-diam seperti desain sebelumnya): password akun **tidak boleh disimpan dalam bentuk yang bisa dibaca ulang** (harus tetap hash satu-arah, praktik keamanan standar — kalau disimpan bisa-dibaca-ulang/terenkripsi-2-arah, kebocoran `APP_KEY` akan membocorkan semua password pembeli sekaligus). Karena password lama tidak bisa "diambil lagi" untuk dikirim ulang, sistem perlu tahu dulu apakah pembeli ini **mau login** (kalau masih ingat password) atau **rela dapat password baru** (kalau lupa) — makanya perlu satu langkah konfirmasi eksplisit:

**Catatan risiko (ditemukan di audit final, diterima sebagai trade-off sadar)**: fitur "cek email sudah terdaftar" ini secara teknis membuka celah *email enumeration* — orang lain bisa mencoba banyak email di form checkout untuk mengetahui email mana saja yang terdaftar sebagai pembeli di situs ini (privasi pembeli). Untuk skala bisnis kecil ini risikonya rendah, dan sudah sedikit dimitigasi oleh rate limiting (`throttle:10,1` di atas). Kalau ke depannya privasi pembeli jadi prioritas lebih tinggi, opsi lebih aman adalah selalu tampilkan pesan generik yang sama tanpa membedarkan "terdaftar/tidak" — tapi itu mengorbankan kemudahan (pembeli tidak akan tahu harus login) — untuk sekarang kemudahan tetap diprioritaskan sesuai arahan awal.

1. Pembeli isi form checkout guest (nama/email/HP) → submit.
2. Backend cek `User::where('email', ...)->exists()` **sebelum** membuat `Order`/memanggil Midtrans:
   - **Belum terdaftar** → tidak ada interupsi, langsung lanjut ke pembayaran seperti biasa (§7.5).
   - **Sudah terdaftar** → modal checkout tampilkan notifikasi: *"Email Anda sudah terdaftar. Login untuk melanjutkan, atau lanjutkan pembayaran dan terima email dengan password baru."* dengan dua tombol:
     - **"Login"** → form guest berganti jadi form login singkat (email+password) di modal yang sama; setelah login sukses, lanjut sebagai skenario B di bawah (tidak reset password, email cuma link unduh).
     - **"Lanjutkan & Kirim Password Baru"** → frontend submit ulang checkout dengan parameter `confirm_new_password=true` (§7) supaya backend tidak menanyakan ulang di percobaan kedua ini, lalu `Order` dibuat seperti alur guest biasa (§7.5) — `orders.user_id` tetap kosong saat dibuat. Karena satu-satunya cara mencapai kondisi "Order guest (`user_id` kosong) dengan email yang cocok User yang sudah ada" adalah lewat tombol ini, webhook (§7.7 langkah 3) otomatis tahu untuk reset password begitu mendeteksi kondisi itu — tidak perlu menyimpan flag konfirmasi terpisah di tabel `orders`. Setelah bayar sukses, email berisi password baru + link unduh, dan halaman sukses menampilkan pesan yang sama.

**B. Pembeli sudah login (sesi aktif di area klien) saat mau beli produk lain:**
- Form checkout **tidak menanyakan nama/email/HP lagi** — langsung pakai data dari `auth()->user()`, modal langsung menuju tombol "Bayar dengan QRIS" (satu langkah lebih pendek dari alur guest).
- `CheckoutController` set `orders.user_id = auth()->id()` beserta `buyer_email`/`buyer_name` dari akun langsung saat `Order` dibuat (bukan dari input form).
- Di webhook, proses provisioning akun (§7.7 langkah 3) **dilewati** kalau `orders.user_id` sudah terisi sejak awal — tidak perlu cek/buat `User` lagi, tidak ada password baru yang di-generate. Langkah 4 (kirim email) tetap jalan seperti biasa, hanya isinya jadi struk/tanda-terima biasa (link unduh saja, tanpa info akun karena pembeli sudah tahu kredensialnya).
- Pembelian baru ini otomatis langsung muncul di `/akun/produk-saya` tanpa perlu refresh/login ulang.

## 8. Seed Data Produk

**Menunggu daftar produk asli dari pemilik** (source code/prompt/materi ajar apa saja yang mau langsung dimasukkan) — belum diberikan saat PRD ini ditulis. Jangan mengarang data produk; tunggu daftar tersebut sebelum menjalankan seeder produk.

## 9. Verifikasi / Test Plan

1. `php artisan migrate` — 5 tabel baru (articles, source_codes, prompts, materi_ajars, orders) jalan tanpa error.
2. `php artisan storage:link` jika belum ada (untuk disk `public`, khusus thumbnail `image` — bukan untuk `file_path` yang memang harus tetap privat, §6.3).
3. Jalankan command migrasi artikel → `Article::count()` = 4, isi HTML utuh.
4. `php artisan serve` → cek render `/`, `/artikel`, `/artikel/{slug}`, `/produk`, `/produk/source-code`, `/produk/prompt`, `/produk/materi-ajar`, `/chatbot`, `/review-jurnal` (khususnya CSS diagram artikel tidak rusak).
5. **Test checkout QRIS end-to-end** pakai simulator sandbox Midtrans (bukan kartu — metode bayar cuma QRIS, §7.4) → pastikan webhook `/midtrans/notification` diterima, `Order.status` → `paid`, tombol "Unduh Sekarang" muncul otomatis di modal lewat polling, link `/unduh/{token}` bisa diakses.
6. **Test 3 skenario akun** (§7.7-7.8): (a) email baru → akun+password dibuat, email masuk, password juga muncul di modal; (b) email guest yang sudah terdaftar → muncul pilihan Login/Lanjutkan, coba kedua cabangnya; (c) beli sambil sudah login → form checkout lebih pendek, tidak ada email akun baru, order langsung muncul di `/akun/produk-saya`.
7. **Test keamanan file produk**: coba akses langsung URL file ZIP di `storage/app/...` dari browser tanpa lewat `/unduh/{token}` — pastikan **tidak bisa** diakses publik (§6.3). Juga coba akses `/unduh/{token}` dengan `token` acak/Order yang belum `paid` — pastikan ditolak (404/403).
7a. **Test pemisahan akun**: login pakai akun client (hasil provisioning otomatis) lalu coba akses `/admin` — pastikan **ditolak** (§5.1). Login pakai akun admin ke `/admin` — pastikan tetap bisa masuk seperti biasa.
8. **Test idempotensi webhook**: kirim ulang payload notifikasi Midtrans yang sama 2x (simulasi retry) → pastikan email tidak terkirim dobel dan password tidak ter-generate ulang (§7.7 guard).
9. Buka `/admin` → cek semua Resource (Article/SourceCode/Prompt/MateriAjar/Order/User) muncul & CRUD jalan (kecuali create Order), tombol "Kirim Ulang Email" di OrderResource berfungsi.
10. Cek `/alatpesta` sudah 404, `sitemap.xml`/`.htaccess` tidak lagi menyebut alatpesta.
11. Cek tombol WhatsApp di halaman sukses membuka `wa.me` dengan nomor order sudah terisi otomatis di pesan.
12. **Test alur gagal**: tutup popup Snap tanpa bayar → cek pesan "Coba Lagi" muncul; biarkan QR kedaluwarsa (atau simulasikan webhook `expire`) → cek pesan waktu habis muncul; simulasikan webhook `deny` → cek `Order.status` jadi `failed` dan tidak ada email/akun ter-provisi.
13. **Test rate limiting**: kirim request bertubi-tubi ke `/checkout/{type}/{slug}` dan `/akun/login` → pastikan kena `429 Too Many Requests` setelah batas terlampaui.
14. **Test tampilan mobile**: buka modal checkout & halaman produk di layar HP (atau devtools mobile emulation) → pastikan form dan QR code muat tanpa scroll horizontal.
15. Cek halaman kategori produk yang belum ada isinya menampilkan pesan "belum ada produk", bukan tampilan kosong/rusak.

## 10. Open Items sebelum implementasi dimulai

- [ ] Daftar produk nyata (source code/prompt/materi ajar) dari pemilik untuk seed data (§8).
- [ ] Konfirmasi final apakah PRD ini sudah sesuai, atau ada bagian yang perlu direvisi sebelum coding dimulai.

## 11. Checklist Implementasi

Legenda status: ✅ Selesai · ⚠️ Selesai dengan catatan/masalah (isi kolom Catatan) · ❌ Tidak bisa dikerjakan (isi kolom Catatan dengan alasan) · kosong = belum dikerjakan. Agent yang mengeksekusi PRD ini wajib mengisi kolom Status+Catatan per baris, jangan tandai ✅ tanpa benar-benar memverifikasi (lihat §9 Test Plan untuk cara verifikasi tiap bagian).

| # | Item | Status | Catatan |
|---|---|---|---|
| 1 | `git init` + commit awal (§3.1) | | |
| 2 | `composer require midtrans/midtrans-php` | | |
| 3 | `.env` + `config/midtrans.php` (§7.4) | | |
| 4 | Migration `articles` (§4) | | |
| 5 | Migration `source_codes` (§4) | | |
| 6 | Migration `prompts` (§4) | | |
| 7 | Migration `materi_ajars` (§4) | | |
| 8 | Migration `orders` (§4) | | |
| 9 | `php artisan migrate` sukses | | |
| 10 | Model `Article` (§5) | | |
| 11 | Model `SourceCode` (§5) | | |
| 12 | Model `Prompt` (§5) | | |
| 13 | Model `MateriAjar` (§5) | | |
| 14 | Model `Order` (+ relasi, generateDownloadToken) (§5) | | |
| 14a | Migration tambah kolom `users.role` + `User implements FilamentUser` + `canAccessPanel()` (§5.1) | | |
| 14b | Set `role='admin'` manual untuk akun admin existing setelah migration (§5.1) | | |
| 15 | `ArticleResource` Filament (§6) | | |
| 16 | `SourceCodeResource` Filament (§6) | | |
| 17 | `PromptResource` Filament (§6) | | |
| 18 | `MateriAjarResource` Filament (§6) | | |
| 19 | `OrderResource` Filament + action "Kirim Ulang Email" (§6.2) | | |
| 20 | `UserResource` Filament untuk admin management (§6.2) | | |
| 21 | Dashboard widget Filament (§6.2) | | |
| 22 | `file_path` pakai disk privat `local`, bukan `public` (§6.3) | | |
| 23 | Cleanup: hapus `alatpesta.blade.php`/`.js`/`.css` (§7.1) | | |
| 24 | Cleanup: `sitemap.xml` dibersihkan dari entri alatpesta (§7.1) | | |
| 25 | Cleanup: `.htaccess` redirect alatpesta dihapus (§7.1) | | |
| 26 | `ArticleController` (index+show) (§7) | | |
| 27 | `SourceCodeController`/`PromptController`/`MateriAjarController` (§7) | | |
| 28 | `CheckoutController` (+ cek email exists, §7.8) | | |
| 29 | `MidtransController` (webhook + verifikasi signature + idempotensi, §7.7) | | |
| 30 | `OrderStatusController` (polling status, §7.5) | | |
| 31 | `PaymentController` (download privat, §6.3) | | |
| 32 | `AuthController` (login/logout area klien, §7.7) | | |
| 33 | `DashboardController` (`/akun/produk-saya`, §7.7) | | |
| 34 | `bootstrap/app.php` — CSRF exception untuk webhook (§7) | | |
| 35 | Views: `pages/welcome.blade.php` (pindah + fix link artikel) (§7.2) | | |
| 36 | Views: `articles/index` + `articles/show` (§7.2) | | |
| 37 | Views: `products/index` (hub) + 3× index/show per jenis produk (§7.2) | | |
| 38 | Views: modal checkout (form + Snap.js + polling + tombol WA + tampilan akun) (§7.5, §7.7) | | |
| 38a | Alur gagal/batal/kedaluwarsa QRIS (onClose/onError/expire) + tombol "Coba Lagi" (§7.5.1) | | |
| 38b | Rate limiting: `throttle` di `/checkout`, `/api/order/*/status`, `/akun/login` (§7) | | |
| 38c | Responsif mobile untuk modal checkout + halaman produk/akun (§7.6) | | |
| 38d | State kosong "belum ada produk" di halaman kategori produk (§7.6) | | |
| 39 | Views: `tools/chatbot`, `tools/review-jurnal` (pindah) (§7.2) | | |
| 40 | Views: `akun/login`, `akun/produk-saya` + link nav "Login"/"Produk Saya" di header situs (§7.7.1) | | |
| 41 | Command `MigrateArticleContent` + dijalankan + file lama dihapus (§7.3) | | |
| 42 | Konsistensi desain (warna/font situs lama dipakai di halaman baru) (§7.6) | | |
| 43 | Seed data produk nyata (§8) — **menunggu daftar dari pemilik** | | |
| 44 | Test: render semua halaman publik (§9.4) | | |
| 45 | Test: checkout QRIS end-to-end sandbox (§9.5) | | |
| 46 | Test: 3 skenario akun (baru/email-terdaftar/sudah-login) (§9.6) | | |
| 47 | Test: file ZIP tidak bisa diakses publik langsung (§9.7) | | |
| 48 | Test: idempotensi webhook (retry notifikasi 2x) (§9.8) | | |
| 49 | Test: CRUD semua Filament Resource (§9.9) | | |
| 50 | Test: `/alatpesta` 404, sitemap/htaccess bersih (§9.10) | | |
| 51 | Test: tombol WhatsApp buka `wa.me` dengan nomor order terisi (§9.11) | |
