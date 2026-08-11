# PRD Teknis: Pemisahan Konten (Artikel/Produk/Halaman Utama) + Payment Gateway Midtrans

> **Versi final** — dokumen ini ditulis ulang untuk mencerminkan kondisi sesudah implementasi + banyak iterasi perbaikan. Kalau project ini dibangun ulang dari nol mengikuti dokumen ini, hasilnya harus konsisten dengan yang sudah berjalan sekarang. Beberapa keputusan di sini **berubah dari draf pertama** (paling signifikan: §7.5 alur akun/checkout, dan produk "Source Code" yang diganti "Jasa Pembuatan Aplikasi") — jangan rujuk versi lama dokumen ini kalau masih ada salinannya di tempat lain.

## 1. Latar Belakang & Masalah

Website Laravel ini awalnya menyimpan seluruh konten (4 artikel, halaman utama, halaman utility) sebagai file Blade standalone yang di-hardcode langsung lewat closure di `routes/web.php` — tidak ada Controller, Model, atau tabel database untuk konten apa pun di luar tabel default Laravel. Admin panel Filament (v4.0) sudah terpasang aktif di `/admin` tapi kosong, belum ada satu Resource pun.

Ditemukan juga artefak lama `alatpesta.blade.php` + `public/alatpesta.js/css` (katalog sewa alat pesta) yang salah sambung routing-nya dan tidak pernah tampil — **dikonfirmasi bukan bisnis pemilik**, dihapus total. Bisnis sebenarnya: **source code (belakangan dipivot jadi jasa pembuatan aplikasi), prompt AI, dan materi ajar sekolah**.

## 2. Tujuan (final)

1. Pisahkan konten jadi struktur: **Halaman Utama**, **Artikel**, **Produk** (submenu berisi: Jasa Pembuatan Aplikasi / Prompt / Materi Ajar).
2. Semua konten dikelola lewat CRUD di admin Filament (`/admin`), bukan hardcode file.
3. Alur jual-beli sungguhan untuk **Prompt** dan **Materi Ajar** via **Midtrans QRIS (sandbox)** — "Jasa Pembuatan Aplikasi" BUKAN produk checkout, itu halaman portofolio dengan CTA WhatsApp.
4. Guest checkout tanpa hambatan, tapi **akun klien dibuat & login otomatis sejak checkout dimulai** (bukan setelah bayar) supaya pembeli yang keluar-masuk sebelum bayar tetap dikenali dan bisa melanjutkan/membatalkan pesanan, bukan bikin order duplikat.

## 3. Non-Goals

- Tidak ada halaman "Daftar Akun" manual — akun klien selalu dibuat otomatis lewat checkout (§7.5).
- Tidak ada fitur "lupa password" self-service — mitigasi lewat WhatsApp ke admin.
- Tidak mengubah konten/desain halaman utama itu sendiri — hanya dipindah lokasi file, nav disamakan dengan halaman lain, dan link artikel hardcode diperbaiki.
- "Jasa Pembuatan Aplikasi" bukan produk checkout — tidak ada harga/beli untuk kategori ini, murni portofolio + CTA WhatsApp.
- Tidak ada level admin bertingkat (role/permission) — hanya 2 tipe akun: `admin` dan `client`.
- Chatbot dan Review Jurnal — awalnya ada sebagai "Tools", **belakangan dihapus total** atas permintaan pemilik (lihat §7.1).

## 3.1 Persiapan sebelum mulai coding

Project awalnya **tidak punya git** — langkah pertama adalah `git init` + commit awal (snapshot kondisi sebelum implementasi) supaya ada titik aman untuk rollback.

Environment note: server ini pakai **PHP 8.5.4**, lebih baru dari yang didukung `nette/schema` (dependensi transitif Laravel). `composer require`/`install`/`update` butuh flag `--ignore-platform-req=php` supaya tidak gagal karena constraint versi PHP itu.

## 4. Skema Database (migrations)

| Tabel | Kolom |
|---|---|
| `users` | (bawaan Laravel) + kolom tambahan **`role`** (string, default `'admin'` di migration — lihat §5.1 kenapa) |
| `articles` | `title`, `slug` (unique), `excerpt` nullable, `content` (longText, HTML mentah termasuk `<style>` asli artikel — lihat §6.1 & §7.2), `author` nullable, `published_at` nullable, timestamps |
| `aplikasis` | `name`, `description` (text), `demo_url` nullable, `image` nullable, `tech_stack` nullable, `order` (unsignedInteger, default 0, untuk urutan tampil). **Tidak ada `price`/`slug`/`file_path`** — ini portofolio, bukan produk checkout. |
| `prompts` | `name`, `slug` (unique), `description` (text), `price` (decimal 12,2), `file_path` (string — path ZIP di storage privat), `prompt_content` (longText nullable — referensi admin, bukan yang dikirim), `example_output` (longText nullable, preview publik), `category` nullable, `image` nullable, timestamps |
| `materi_ajars` | `name`, `slug` (unique), `description` (text), `price` (decimal 12,2), `file_path` (string), `jenjang` (string: SD/SMP/SMA/SMK), `mata_pelajaran` (string), `image` nullable, timestamps |
| `orders` | `order_number` (unique, format `ORD-{Ymd}-{6 char random uppercase}`), `orderable_type`+`orderable_id` (morphTo — **hanya ke Prompt/MateriAjar**, Aplikasi tidak pernah jadi orderable), `user_id` (FK `users`, **diisi sejak Order dibuat**, bukan belakangan — lihat §7.5), `buyer_name`, `buyer_email`, `buyer_phone` nullable, `amount` (decimal 12,2), `status` (pending/paid/failed/expired/cancelled, default pending), `snap_token` nullable, `payment_type` nullable, `download_token` (unique, random 40 char), `account_provisioned_at` nullable timestamp, `paid_at` nullable, timestamps |

**`source_codes` — tabel ini SEMPAT ada lalu di-DROP** (migration `drop_source_codes_table`). Kalau membangun ulang dari nol, **jangan buat tabel ini sama sekali** — produk "Source Code" dipivot total jadi "Jasa Pembuatan Aplikasi" (`aplikasis`, model berbeda, bukan checkout).

**Tidak ada kolom penyimpan password di tabel manapun** (bahkan terenkripsi) — password akun disimpan **hanya dalam bentuk hash** (via `casts()` User), dan dikirim ke pembeli lewat email + response API saat checkout (§7.5), bukan lewat mekanisme cache/reveal-sekali seperti draf awal PRD ini (draf awal pernah pakai `Cache::put/pull` untuk ini — **sudah tidak dipakai lagi**, dihapus saat arsitektur checkout direvisi).

Semua model produk (`Prompt`, `MateriAjar`) implementasikan `getRouteKeyName(): string { return 'slug'; }`. `Aplikasi` **tidak** perlu ini (tidak ada halaman detail per-item, hanya listing).

**Standarisasi `file_path`** (Prompt & MateriAjar saja): produk dikirim sebagai satu file ZIP di storage privat. Alur unduh (`/unduh/{download_token}`) satu logic seragam: `Storage::disk('local')->download($order->orderable->file_path)`. Lihat §6.3 untuk kenapa harus privat.

## 5. Models

`app/Models/Article.php`, `Aplikasi.php`, `Prompt.php`, `MateriAjar.php`, `Order.php` — ikuti gaya `User.php`: `protected $fillable` array polos + PHPDoc `@var list<string>`, cast lewat method `protected function casts(): array` (Laravel 12, bukan property `$casts`).

`Order` punya method tambahan:
- `orderable()` → `morphTo()`
- `user()` → `belongsTo(User::class)`
- `generateOrderNumber()` (static), `generateDownloadToken()` (static)
- `productUrl(): ?string` — URL halaman produk ini (`match($this->orderable_type)` ke route show yang sesuai), dipakai sebagai fallback link.
- `productType(): ?string` — slug tipe produk untuk URL checkout (`prompt` / `materi-ajar`), dipakai tombol "Lanjutkan Bayar" langsung di halaman akun (§7.5) supaya tidak perlu hardcode ulang mapping type↔class di Blade.

`Aplikasi` model: `fillable = ['name', 'description', 'demo_url', 'image', 'tech_stack', 'order']`. Tidak ada relasi ke `Order` (bukan produk checkout).

### 5.1 Wajib: pemisahan akun Admin vs Client

Akun **Admin** (pemilik, akses `/admin`) dan akun **Client** (pembeli, dibuat otomatis saat checkout — §7.5) sama-sama di tabel `users` — tanpa pembeda, klien berpotensi login ke `/admin`.

Perbaikan wajib:
1. Migration tambah kolom `role` (string, default `'admin'` — supaya akun admin yang sudah ada sebelum migration otomatis tetap `admin` tanpa langkah manual tambahan; akun client SELALU di-set eksplisit `role: 'client'` saat dibuat).
2. `User implements Filament\Models\Contracts\FilamentUser`, method `canAccessPanel(Panel $panel): bool { return $this->role === 'admin'; }`.
3. `User::orders()` → `hasMany(Order::class)` (dipakai halaman akun klien, §7.5).
4. `UserResource` Filament (§6.2) di-scope hanya tampilkan `role=admin` (`getEloquentQuery()` override), dan set `role: 'admin'` default saat admin baru dibuat lewat situ.

## 6. Filament Resources (`/admin`)

**Keputusan: tetap pakai Filament** (bukan admin custom) — untuk kebutuhan CRUD sebanyak ini, auth/validasi/upload/tabel sudah otomatis tersedia.

Resource: `ArticleResource`, `AplikasiResource`, `PromptResource`, `MateriAjarResource`, `OrderResource` (list+edit saja, `canCreate()` return `false` — order lahir dari checkout), `UserResource` (§5.1).

Filament v4 pakai `Filament\Schemas\Schema` terpadu, bukan `Forms\Form`/`Infolists\Infolist` v3. Generate resource pakai `php artisan make:filament-resource {Model} --generate` — ini otomatis membaca skema migration dan menghasilkan field yang tepat (termasuk `FileUpload` untuk kolom `image`), tinggal disesuaikan manual untuk hal spesifik (lihat §6.1, §6.3).

### 6.1 Field `content`/`prompt_content` pakai `Textarea`, bukan `RichEditor`

Artikel menyimpan HTML mentah + `<style>` custom (diagram, sitasi akademik). `RichEditor` (Tiptap) akan mensanitasi dan merusak markup ini. `Textarea` menyimpan apa adanya — dipakai juga untuk `prompt_content`.

Field `image` (thumbnail, boleh publik) pakai `FileUpload::make('image')->image()->disk('public')`.

Tambahan kecil yang terbukti berguna: `TextInput::make('name'/'title')->live(onBlur: true)->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))` + `TextInput::make('slug')->unique(ignoreRecord: true)` di semua form yang punya slug (Article/Prompt/MateriAjar) — auto-generate slug dari nama, admin tidak perlu ketik manual.

`MateriAjarForm`: `jenjang` pakai `Select` (opsi SD/SMP/SMA/SMK), bukan `TextInput` bebas.

### 6.2 Fitur admin

- **Dashboard widget** (`app/Filament/Widgets/DashboardStats.php`, `StatsOverviewWidget`): jumlah artikel, jumlah produk (Prompt+MateriAjar count), order pending, order lunas hari ini, pendapatan hari ini.
- **OrderResource**: Filament Action kustom "Kirim Ulang Email" per baris (`->visible(fn ($record) => $record->status === 'paid')`, kirim ulang `OrderPaid` mailable) — untuk kasus email gagal terkirim (SMTP belum aktif, `MAIL_MAILER=log`).
- **UserResource**: khusus kelola akun admin (§5.1), `modelLabel` diset `'Admin'`.
- Log aktivitas & export laporan — opsional, belum diimplementasikan, tidak blocking.

### 6.3 Penyimpanan file produk (ZIP) — wajib privat

Kolom `file_path` (Prompt & MateriAjar) pakai `FileUpload::make('file_path')->disk('local')->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])->maxSize(102400)`.

**Penting (gotcha nyata yang pernah bikin bug 500 saat implementasi)**: disk `'local'` Laravel 11+ **root-nya `storage/app/private/`**, BUKAN `storage/app/` langsung — kalau taruh file test manual di path yang salah, `Storage::disk('local')->download()` akan gagal. Kalau file ZIP disimpan di disk `public`, produk bisa diunduh gratis tanpa bayar — pelanggaran keamanan serius. `/unduh/{download_token}` satu-satunya jalan resmi, wajib cek `Order::where('download_token', $token)->where('status','paid')->firstOrFail()` dulu.

**Gotcha kedua yang juga pernah bikin 500**: `Storage::disk('local')->download()` mengembalikan `Symfony\Component\HttpFoundation\StreamedResponse`, **bukan** `Illuminate\Http\Response` — pastikan type-hint return method controller sesuai (`StreamedResponse`), atau PHP akan lempar `TypeError`.

## 7. Routes (`routes/web.php`) — daftar final

```
Home:      GET /                                        → view('pages.welcome')                          name: home
Articles:  GET /artikel                                  → ArticleController@index                        name: articles.index
           GET /artikel/{article:slug}                   → ArticleController@show                         name: articles.show
Produk:    GET /produk                                   → view('products.index') (hub kecil)              name: products.index
           GET /jasa-pembuatan-aplikasi                  → AplikasiController@index                        name: aplikasi.index
           GET /produk/prompt                            → PromptController@index                          name: products.prompt.index
           GET /produk/prompt/{prompt:slug}              → PromptController@show                           name: products.prompt.show
           GET /produk/materi-ajar                       → MateriAjarController@index                      name: products.materi-ajar.index
           GET /produk/materi-ajar/{materiAjar:slug}     → MateriAjarController@show                       name: products.materi-ajar.show
Checkout:  POST /checkout/{type}/{slug}                  → CheckoutController@store (throttle:10,1)        name: checkout.store
           POST /midtrans/notification                   → MidtransController@notification                name: midtrans.notification
           GET /api/order/{order:order_number}/status     → OrderStatusController@show (throttle:30,1)     name: order.status
           GET /unduh/{order:download_token}              → PaymentController@download                     name: unduh.show
Akun:      GET  /akun/login                               → AuthController@showLogin (middleware: guest)    name: akun.login
           POST /akun/login                               → AuthController@login (guest, throttle:5,1)      name: akun.login.attempt
           POST /akun/logout                              → AuthController@logout (auth)                    name: akun.logout
           GET  /akun/produk-saya                         → DashboardController@index (auth)                name: akun.produk-saya
           POST /akun/pesanan/{order:order_number}/batalkan → DashboardController@cancel (auth)              name: akun.pesanan.batalkan
Lainnya:   POST /remove-background, GET /api/image/test  → ImageProcessingController (tidak terkait konten/produk sama sekali)
```

**`{type}` di checkout** = `prompt` | `materi-ajar` **saja** (map manual ke class di `CheckoutController::TYPE_MAP`, bukan route-model-binding karena beda tabel). **Tidak ada `source-code`** sebagai type checkout — dihapus total.

**Dihapus dari desain awal**: `/chatbot`, `/review-jurnal`, dan seluruh route/view/file pendukungnya (`public/js/review_jurnal.js`, `public/css/style_review_jurnal.css`) — dihapus atas permintaan eksplisit pemilik setelah sempat dibangun. `/produk/source-code*` juga dihapus total (lihat §7.1 pivot Aplikasi).

`bootstrap/app.php`: `$middleware->validateCsrfTokens(except: ['midtrans/notification']);` (webhook eksternal tidak bawa CSRF token) **dan** `$middleware->redirectGuestsTo('/akun/login');` — **wajib**, karena default Laravel redirect guest ke route bernama `login` yang tidak ada di project ini (custom login diberi nama `akun.login`), tanpa ini middleware `auth` akan 500 "Route [login] not defined" (bug nyata yang ditemukan saat testing).

**Rate limiting**: `throttle:10,1` di checkout, `throttle:30,1` di polling status, `throttle:5,1` di login attempt.

### 7.1 Cleanup & Pivot Produk

**Alatpesta** (artefak awal): hapus `resources/views/alatpesta.blade.php`, `public/alatpesta.js/css`, entri `/alatpesta/*` di `sitemap.xml`, redirect di `.htaccess`.

**Chatbot & Review Jurnal**: awalnya dipindah ke `resources/views/tools/{chatbot,review-jurnal}.blade.php` sebagai kategori "Tools" di nav — **belakangan pemilik minta dihapus total, bukan cuma disembunyikan dari nav**. Hapus route, view, dan file JS/CSS pendukungnya. Kalau membangun ulang dari nol: **jangan bangun kategori "Tools" sama sekali**, langsung skip ke struktur final (Halaman Utama/Artikel/Produk).

**Source Code → Jasa Pembuatan Aplikasi (pivot penting)**: rencana awal PRD ini punya produk "Source Code" (checkout, harga, file ZIP) sebagai salah satu dari 3 jenis produk. **Pemilik memutuskan pivot**: dihapus total (model, migration/tabel via migration drop, controller, Filament resource, view, route, entri di `CheckoutController::TYPE_MAP`), diganti fitur berbeda **"Jasa Pembuatan Aplikasi"** — halaman portofolio (model `Aplikasi`, §4/§5) yang menampilkan aplikasi yang sudah dibuat + tombol CTA WhatsApp "Pesan Jasa Pembuatan Aplikasi", **bukan produk yang dibeli lewat checkout**.

### 7.2 Struktur Views

```
resources/views/
├── layouts/app.blade.php       (layout bersama: produk, artikel, akun — lihat §7.6 untuk gotcha CSS)
├── pages/welcome.blade.php     (halaman utama, pakai layouts.app juga — lihat catatan di bawah)
├── articles/{index,show}.blade.php
├── products/
│   ├── index.blade.php         (hub kecil /produk, 3 kartu: Aplikasi/Prompt/MateriAjar)
│   ├── _checkout-modal.blade.php  (partial dipakai di show.blade.php Prompt & MateriAjar — lihat §7.5)
│   ├── prompt/{index,show}.blade.php
│   └── materi-ajar/{index,show}.blade.php
├── jasa-aplikasi/index.blade.php   (portofolio Aplikasi, CTA WhatsApp — BUKAN di dalam folder products/)
├── akun/{login,produk-saya}.blade.php   (nama file tetap "produk-saya", tapi judul halaman & label nav "Aset Saya" — lihat §7.7.1)
└── emails/{order-paid,account-credentials}.blade.php
```

`pages/welcome.blade.php` — **awalnya standalone dengan nav/header/footer sendiri (duplikat dari `layouts/app.blade.php`)**, sempat jadi sumber inkonsistensi berulang (nav dropdown beda padding karena bug specificity CSS, perlu ubah 2 tempat tiap ganti nav). **Sudah di-refactor**: sekarang `pages/welcome.blade.php` juga `@extends('layouts.app')` — header/nav/footer jadi satu sumber kebenaran. CSS unik halaman utama (hero, value-card, portfolio grid, dst — cukup banyak, ~300 baris) dikirim lewat `@push('styles')` (raw CSS, **tanpa** tag `<style>` pembungkus — `@stack('styles')` di `layouts/app.blade.php` sudah ada di dalam tag `<style>` miliknya sendiri, membungkusnya lagi bikin nested `<style>` yang merusak parsing CSS, pernah kejadian saat refactor). JS scroll-reveal (`[data-animate]`) dikirim lewat `@push('scripts')`.

**Gotcha tambahan yang ditemukan saat refactor ini**: `layouts/app.blade.php` punya rule generik `h1, h2 { color: var(--primary); }` (dipakai judul halaman produk/artikel/dll). Karena CSS *inheritance* kalah oleh deklarasi langsung pada elemen (terlepas dari specificity induk), `.hero h1` di halaman utama **wajib** set `color: white;` secara eksplisit — kalau tidak, teks hero yang seharusnya putih di atas gradient akan ikut jadi warna gelap `--primary` dari rule generik itu.

**Risiko CSS artikel**: 4 artikel lama masing-masing standalone HTML dengan `<style>` sendiri (500-700+ baris), termasuk selector generik `header`/`footer`/`body`/`nav` yang **tidak identik** antar file. Solusi final: `articles/show.blade.php` **memakai `layouts.app`** (bukan standalone lagi seperti draf awal) — konten artikel di database sudah "dilucuti" dari HTML header/nav/footer/script lama-nya (hanya style unik diagram + body `.article-header`/`.article-content` yang disimpan), lalu dirender di dalam `@section('content')`. Lihat §7.6 untuk gotcha specificity CSS yang muncul akibat penyatuan ini.

### 7.3 Migrasi konten artikel

Command sekali-jalan (sudah dijalankan & dihapus filenya) baca isi asli tiap file Blade lama (region `<style>` + body `.article-header`/`.article-content`, TANPA header/nav/footer/script lama), insert ke `Article::create()`. Kalau membangun ulang: yang penting cuma **hasil akhirnya** (4 row di tabel `articles` dengan `content` = style+body saja, tanpa chrome lama) — command generatornya sendiri sudah tidak relevan lagi karena file Blade sumbernya sudah dihapus.

### 7.4 Integrasi Midtrans (sandbox)

`.env`: `MIDTRANS_MERCHANT_ID`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_SERVER_KEY`, `MIDTRANS_IS_PRODUCTION=false`, `WHATSAPP_ADMIN_NUMBER` (format `62xxx`, dipakai di seluruh link `wa.me`). Package: `composer require midtrans/midtrans-php` (perlu `--ignore-platform-req=php`, §3.1).

`config/midtrans.php`:
```php
return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'whatsapp_admin_number' => env('WHATSAPP_ADMIN_NUMBER'),
];
```

**Metode bayar: QRIS dinamis saja** (`enabled_payments: ['other_qris']` saat `Snap::getSnapToken()`). Satu metode, satu pengalaman, selalu instan (tidak perlu cabang "tunggu lama" seperti VA).

### 7.5 Alur Checkout — akun dibuat SEJAK checkout, bukan setelah bayar (arsitektur final)

> **Ini beda paling signifikan dari draf pertama PRD.** Draf awal: akun klien dibuat di webhook (`MidtransController`) setelah pembayaran sukses, password disimpan sementara di `Cache` untuk ditampilkan sekali di halaman sukses. **Alasan direvisi**: kalau pembeli keluar dari halaman setelah dapat QR tapi belum bayar, lalu kembali lagi nanti, sistem tidak mengenali mereka sama sekali dan checkout ulang akan membuat `Order` baru yang duplikat — tidak ada cara "lanjutkan pembayaran". Solusinya: resolusi akun (buat baru/cari yang ada) dan login sesi terjadi **di awal checkout**, sebelum Snap dibuka sama sekali.

**`CheckoutController@store(Request $request, string $type, string $slug)`:**

1. Resolve `$modelClass` dari `TYPE_MAP` (`prompt`→`Prompt`, `materi-ajar`→`MateriAjar`), 404 kalau selain itu. Ambil `$product` by slug.
2. **Resolusi identitas pembeli** (sebelum apa pun lainnya):
   - `Auth::check()` → pakai `$user = Auth::user()` langsung, tidak ada `$newPassword`.
   - Guest → validasi `name`/`email`/`phone` (phone nullable). Cek `User::where('email', ...)->first()`:
     - Ada akun **dan** request **tidak** bawa `confirm_new_password=true` → return `{'email_exists': true}` (belum bikin apa pun — Order/User/email belum tersentuh). Frontend (§7.8 skenario A) tampilkan pilihan Login vs Lanjutkan.
     - Kalau tidak ada akun, ATAU ada akun + `confirm_new_password=true` → `$newPassword = Str::random(10)`; kalau belum ada akun, `User::create([..., 'role' => 'client'])`; kalau sudah ada, `$existingUser->update(['password' => $newPassword])` (reset — ini HANYA terjadi kalau pembeli sendiri yang pilih opsi itu, §7.8).
     - `Auth::login($user); $request->session()->regenerate();` — **login langsung saat ini juga**.
     - `Mail::to($user->email)->send(new AccountCredentials($user, $newPassword));` — **email dikirim langsung di sini, saat checkout, BUKAN ditunda sampai bayar sukses.**
3. **Deteksi order pending untuk resume** (mencegah duplikat kalau pembeli checkout ulang produk yang sama): `Order::where('user_id', $user->id)->where('orderable_type', $modelClass)->where('orderable_id', $product->id)->where('status', 'pending')->latest()->first()`. Kalau ketemu, **pakai order yang sama** (order_number tidak berubah) — cukup minta `snap_token` baru untuk order_id yang sama ke Midtrans (Midtrans mengizinkan ini selama transaksi belum mencapai status final). Kalau tidak ketemu, baru buat `Order` baru dengan `user_id` **langsung terisi saat create**, bukan nullable-menunggu.
4. `Snap::getSnapToken([...])` (item_details, customer_details dari `$user`/`$product`), simpan `snap_token` ke `$order`.
5. Response JSON: `{'snap_token', 'order_number', 'new_password'}` — **password (kalau ada) langsung dikembalikan di response ini**, ditampilkan segera oleh frontend (§7.5.2), tidak lagi lewat mekanisme `Cache::put/pull` seperti draf awal (dihapus, tidak diperlukan lagi karena password sudah pasti diketahui backend saat response ini dibuat, tidak perlu jembatan ke request lain).

**Resume lintas sesi/device**: karena Order dicari berdasar `user_id` (bukan session), pembeli yang checkout dari device/browser berbeda dengan email yang sama akan tetap disambungkan ke order pending yang sama (lewat alur email-exists → Login/Lanjutkan di langkah 2) — **teruji**: 3 percobaan checkout berturut-turut (device sama, device beda dengan email sama via confirm_new_password) untuk produk yang sama tetap menghasilkan **1 Order**, bukan 3.

**Webhook `MidtransController@notification`** (jauh lebih sederhana dari draf awal, karena provisioning akun sudah pindah ke langkah checkout):
0. Verifikasi signature `sha512(order_id+status_code+gross_amount+ServerKey)` — tolak (403) kalau tidak cocok.
1. Guard idempotensi: `if ($order->status === 'paid') return response('OK');` — Midtrans bisa kirim notifikasi retry.
2. `settlement`/`capture` → update `status='paid'`, `payment_type`, `paid_at`, kirim `Mail::to($order->buyer_email)->send(new OrderPaid($order))` — **email ini sekarang cuma struk sederhana** (link unduh + WA), **tidak lagi membawa info akun/password** (itu sudah dikirim terpisah di langkah checkout).
3. `expire` → `status='expired'`. `deny`/`cancel` → `status='failed'`/`'cancelled'`.

**`OrderStatusController@show`** (dipoll dari modal): return `{'status', 'download_url', 'whatsapp_url'}` — **tidak ada field `password` lagi** (dihapus bersamaan dengan mekanisme Cache).

#### 7.5.1 Alur gagal/batal/kedaluwarsa

- Popup Snap ditutup tanpa bayar (`onClose`) → pesan "Pembayaran dibatalkan. Anda bisa lanjutkan lagi lewat halaman Aset Saya atau klik Beli lagi."
- QR kedaluwarsa (±15 menit) → webhook `expire` → polling deteksi → pesan "Waktu pembayaran habis."
- Ditolak/dibatalkan Midtrans (`onError`, atau webhook `deny`/`cancel`) → pesan gagal + link WhatsApp.
- Untuk semua status non-`paid`: **tidak ada** pengiriman email produk (`OrderPaid` cuma terkirim saat `paid`).

#### 7.5.2 Password ditampilkan langsung di modal (bukan dari cache)

Begitu `startPayment()` (JS) menerima response checkout dan `data.new_password` ada isinya, **langsung tampilkan** di area hasil modal ("Akun Anda dibuat. Password: **xxx** ...") **sebelum** `snap.pay()` dipanggil — tidak perlu menunggu status `paid` seperti draf awal, karena password sudah pasti ada di response checkout itu sendiri (dikirim di langkah 2 §7.5, independen dari kapan/apakah pembayaran selesai).

#### 7.5.3 Batalkan Pesanan

`DashboardController@cancel(Order $order)`: cek `$order->user_id === Auth::id()` (403 kalau bukan pemilik) dan `$order->status === 'pending'` (422 kalau bukan pending) → panggil `\Midtrans\Transaction::cancel($order->order_number)` (dibungkus try/catch — kalau transaksi belum pernah dibuka Snap-nya sama sekali di sisi Midtrans, tetap lanjut batalkan lokal) → `$order->update(['status' => 'cancelled'])` → redirect balik ke `/akun/produk-saya` dengan flash `session('status', 'Pesanan berhasil dibatalkan.')`.

#### 7.5.4 "Lanjutkan Bayar" — langsung buka Snap, tanpa pindah halaman

Di halaman "Aset Saya" (§7.7.1), tombol pada order pending **tidak** link ke halaman produk (itu keputusan draf awal, sudah direvisi) — tombol memanggil `ResumePayment.start(type, slug, orderNumber)` (JS, dideklarasikan inline di `produk-saya.blade.php`, memuat `snap.js` di halaman itu juga) yang langsung `POST /checkout/{type}/{slug}` (body kosong, karena sudah `Auth::check()`), dapat `snap_token` (order yang sama, resume — §7.5 langkah 3), lalu langsung `snap.pay(...)`. Sesudah `paid`, `location.reload()` supaya order pindah ke daftar "Produk Dibeli".

### 7.6 Konsistensi Desain & Nav Dropdown

Token desain (warna `--primary:#343434`/`--secondary:#039f5b`/`--accent:#ffcc33`, font Poppins, gaya card) dipakai konsisten di semua halaman baru.

**Struktur nav final**: bukan daftar flat semua kategori, tapi **main nav "Produk" (dropdown) + sub nav (Jasa Pembuatan Aplikasi/Prompt/Materi Ajar)**, plus "Artikel", plus "Aset Saya"/"Login". Tidak ada link teks "Beranda" (logo NUSCODE sudah link ke home).

HTML dropdown:
```html
<div class="nav-dropdown">
  <a href="{{ route('products.index') }}" class="nav-dropdown-toggle">Produk <i class="fas fa-chevron-down"></i></a>
  <div class="nav-dropdown-menu">
    <a href="{{ route('aplikasi.index') }}">Jasa Pembuatan Aplikasi</a>
    <a href="{{ route('products.prompt.index') }}">Prompt</a>
    <a href="{{ route('products.materi-ajar.index') }}">Materi Ajar</a>
  </div>
</div>
```
CSS: `.nav-dropdown-menu` hidden by default, `display:block` on `.nav-dropdown:hover` (desktop). Di mobile (`@media max-width:768px`), `.nav-dropdown-menu` di-`display:block !important` + `position:static` (selalu terlihat terindentasi, tidak perlu toggle JS terpisah).

**Gotcha CSS specificity (bug nyata yang muncul 2x saat development, harus diwaspadai kalau menambah CSS baru ke `layouts/app.blade.php`)**: `layouts/app.blade.php` memberi `id="site-header"`/`id="site-footer"` pada elemen `<header>`/`<footer>` supaya stylingnya **selalu menang** dibanding sisa-sisa selector generik `header`/`footer` yang mungkin masih terbawa dari konten artikel lama (§7.2). Akibatnya, **setiap CSS baru di dalam `#site-header` yang perlu meng-override style link nav dasar (`#site-header nav a { padding/margin/... }`) WAJIB diberi prefix `#site-header` juga** (mis. `#site-header .nav-dropdown-menu a { ... }`), **bukan cuma `.nav-dropdown-menu a`** — kalau tidak, aturan class biasa (specificity lebih rendah dari ID) akan KALAH dan diam-diam tidak diterapkan meski tidak ada error apa pun (bug persis ini pernah terjadi: padding submenu di halaman produk berbeda dari halaman utama karena lupa prefix ini).

`pages/welcome.blade.php` sekarang **ikut memakai `#site-header`** juga (sejak refactor unifikasi di atas) — jadi cukup satu sumber CSS nav/dropdown untuk seluruh situs, tidak ada lagi duplikasi.

**Responsif mobile**: modal checkout & QR harus muat tanpa scroll horizontal di layar HP kecil (mayoritas QRIS dipakai lewat HP).

**State kosong**: halaman kategori produk yang belum ada isinya tampilkan "Belum ada produk tersedia", bukan grid kosong polos.

### 7.7 Tiga Jalur Pengambilan Produk & Halaman "Aset Saya"

Setelah pembayaran QRIS `paid`, pembeli ambil produk lewat 3 jalur: **(1) email** (`OrderPaid`, dikirim otomatis di webhook), **(2) halaman "Aset Saya"** (`/akun/produk-saya`, auth-protected), **(3) WhatsApp** ke admin (link `wa.me/{{ config('midtrans.whatsapp_admin_number') }}?text=...` dengan nomor order sudah terisi otomatis, tersedia di email DAN di modal checkout untuk kasus gagal/butuh bantuan).

#### 7.7.1 Halaman "Aset Saya" (nama tampilan — file & route tetap `produk-saya`)

> **Penamaan**: label yang tampil ke pengguna adalah **"Aset Saya"** (nav, judul halaman, isi email) — ini rename dari "Produk Saya" di iterasi terakhir. **Nama file (`akun/produk-saya.blade.php`), nama route (`akun.produk-saya`), dan URL (`/akun/produk-saya`) SENGAJA tidak ikut diubah** (hanya teks yang tampil ke pengguna) — kalau membangun ulang dari nol, boleh langsung pakai nama "Aset Saya" konsisten di semua lapisan sejak awal, tidak perlu ikut mempertahankan inkonsistensi penamaan file/route ini.

`DashboardController@index`: ambil `Auth::user()->orders()` dua kelompok — `where('status','paid')` (dengan `orderable`, `latest('paid_at')`) dan `where('status','pending')` (`latest()`).

View `akun/produk-saya.blade.php`:
- Section **"Menunggu Pembayaran"**: tiap card tampilkan nama produk, tanggal dibuat, harga, tombol **"Lanjutkan Bayar"** (§7.5.4, langsung buka Snap) + tombol **"Batalkan Pesanan"** (§7.5.3, form POST dengan `confirm()` JS sebelum submit).
- Section **"Produk Dibeli"**: nama produk, tanggal beli, tombol "Unduh" (`route('unduh.show', $order->download_token)`).
- Kalau kosong sama sekali: pesan ramah + link ke `/produk`.
- Tombol Logout (`POST /akun/logout`).

`akun/login.blade.php`: form email+password + teks kecil "Lupa password? Hubungi kami via WhatsApp" (link `wa.me`, karena tidak ada fitur reset password self-service — §3).

**Navigasi**: link "Aset Saya" (login)/"Login" (guest) muncul di nav `layouts/app.blade.php` — **home page (`pages/welcome.blade.php`) TIDAK punya link ini** (nav home sengaja lebih ringkas, sudah dikonfirmasi pemilik — hanya "Produk" dropdown + "Artikel").

### 7.8 Dua Skenario Tambahan pada Checkout

**A. Guest checkout, email sudah terdaftar**: karena password tidak boleh disimpan bisa-dibaca-ulang (§4), sistem tidak bisa "kirim ulang password lama" — perlu konfirmasi eksplisit. Modal tampilkan: *"Email Anda sudah terdaftar. Login untuk melanjutkan, atau lanjutkan pembayaran dan terima email dengan password baru."* + tombol **Login** (form mini email+password di modal yang sama, lanjut sebagai skenario B) / **Lanjutkan & Kirim Password Baru** (submit ulang dengan `confirm_new_password=true`).

**Catatan risiko (diterima sadar)**: fitur cek "email sudah terdaftar" ini secara teknis membuka celah *email enumeration* (bisa dipakai menebak email mana yang terdaftar). Risiko rendah untuk skala bisnis ini, sedikit dimitigasi rate limiting checkout (`throttle:10,1`). Trade-off yang disengaja demi kemudahan pembeli.

**B. Pembeli sudah login saat checkout produk lain**: form tidak menanyakan nama/email/HP lagi, langsung tombol "Bayar dengan QRIS". Tidak ada password baru/email akun (sudah tahu kredensialnya) — email yang terkirim saat `paid` cuma struk biasa.

## 8. Seed Data Produk

**Data contoh/placeholder sudah diisi** untuk keperluan development lokal (bukan produk/portofolio asli — ganti lewat `/admin` begitu ada data sebenarnya dari pemilik):
- **4 Prompt**: Prompt Menulis Artikel SEO, Prompt Coding Assistant, Prompt Copywriting Marketing, Prompt Analisis Data.
- **4 Materi Ajar**: Matematika Kelas 5 SD, IPA Kelas 8 SMP, Bahasa Indonesia Kelas 4 SD, Fisika Kelas 10 SMA.
- **4 Aplikasi** (portofolio): Sistem Informasi Sekolah, Aplikasi Kasir Toko (POS), Website Company Profile, Aplikasi Manajemen Inventori.

File ZIP untuk Prompt/MateriAjar contoh ini juga cuma placeholder teks, bukan produk sungguhan.

## 9. Verifikasi / Test Plan (semua item di bawah sudah dijalankan & lulus saat implementasi)

1. `php artisan migrate` — tabel `articles`, `aplikasis`, `prompts`, `materi_ajars`, `orders` + kolom `users.role` jalan tanpa error; `source_codes` ter-drop bersih.
2. Render semua halaman publik 200: `/`, `/artikel`, `/artikel/{slug}`, `/produk`, `/jasa-pembuatan-aplikasi`, `/produk/prompt`, `/produk/materi-ajar`, `/akun/login`. `/chatbot`, `/review-jurnal`, `/produk/source-code` → 404 (memang sudah dihapus).
3. **Checkout QRIS end-to-end** (browser sungguhan via Playwright + Chrome, bukan cuma curl): klik Beli → isi form → QR asli dari sandbox Midtrans muncul di popup → simulasi webhook `settlement` (signature valid) → modal poll & tampilkan "Unduh Sekarang" → klik → file ZIP asli terunduh.
4. **Resume order pending**: checkout produk yang sama 2-3× (sesi sama, dan sesi/device berbeda dengan email sama) tanpa bayar → tetap 1 `Order`, bukan duplikat. Terverifikasi lewat query DB langsung.
5. **3 skenario akun**: (a) email baru → akun+password dibuat, email `AccountCredentials` terkirim, password juga di response checkout; (b) email sudah terdaftar tanpa login → `{email_exists:true}`, lanjut dengan `confirm_new_password` → password direset, order lama tetap dipakai (tidak duplikat); (c) sudah login → form lebih pendek, tidak ada password baru, resume order lama otomatis.
6. **Batalkan pesanan**: order pending → cancel → status jadi `cancelled`, hilang dari kedua daftar (pending maupun paid); coba cancel order yang bukan pending → 422; tanpa login → ditolak.
7. **Lanjutkan Bayar langsung buka Snap**: diverifikasi via Playwright — klik tombol di halaman Aset Saya membuka QR Snap tanpa pindah halaman sama sekali.
8. **Keamanan file produk**: akses langsung path storage privat → 403/404; `/unduh/{token}` dengan token acak/order belum `paid` → ditolak; hanya lewat token yang sah setelah `paid` → 200 + file valid.
9. **Pemisahan akun admin/client**: akun client akses `/admin` → 403; akun admin → berhasil masuk.
10. **Idempotensi webhook**: kirim payload sama 2× → email `OrderPaid` cuma terkirim 1×, status tidak berubah dobel.
11. **Nav dropdown**: hover "Produk" di desktop dan tap di mobile menampilkan submenu dengan style identik di semua halaman (setelah perbaikan bug specificity CSS, §7.6) — diverifikasi lewat `getComputedStyle()` di Playwright, bukan cuma visual sekilas.

## 10. Status Akhir & Yang Masih Perlu Diperhatikan

- [ ] **Data produk/portofolio masih placeholder** (§8) — ganti dengan data asli lewat `/admin` kapan saja.
- [ ] **Akun admin default** (`listmenarikyt@gmail.com`) — password awal dibuat sekali saat setup, **segera ganti** lewat halaman profil bawaan Filament.
- [ ] `MAIL_MAILER=log` di `.env` — email transaksional (`AccountCredentials`, `OrderPaid`) baru masuk log, belum benar-benar terkirim sampai SMTP asli dikonfigurasi. Tidak blocking untuk alur utama (password & link unduh juga tampil langsung di UI), tapi perlu diaktifkan untuk pengalaman email yang sesungguhnya.
- [ ] Kredensial Midtrans di `.env` masih **sandbox** — ganti ke kredensial production + `MIDTRANS_IS_PRODUCTION=true` saat benar-benar go-live.
- [ ] Belum ada log aktivitas admin / export laporan (§6.2, opsional, belum diimplementasikan).
