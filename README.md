# Nuscode — Node.js

Website portofolio Nuscode mengikuti pola `nc-wa`: Node.js 22+, Express 5, TypeScript, serta script dev/build/start/check/test. Backend dan frontend satu server, tanpa PHP, Vite, atau database server.

Fitur: beranda, portofolio aplikasi, halaman jasa pembuatan aplikasi, daftar/detail artikel, kontak WhatsApp, dan CRUD admin di `/admin`. Portofolio disimpan di `data/content.json` dengan penulisan atomik. Konten awal kosong. Seluruh runtime menggunakan Node.js; tidak membutuhkan PHP, Composer, Laravel, atau Filament.

## Menjalankan

```bash
npm install
cp .env.example .env
# Isi ADMIN_TOKEN (minimal 32 karakter) dan WHATSAPP_NUMBER di .env
npm run dev
```

Buka http://127.0.0.1:8070. Token acak bisa dibuat dengan `node -e "console.log(require('node:crypto').randomBytes(32).toString('hex'))"`. Masukkan token tersebut di `.env` lalu di formulir `/admin`. Jangan bagikan token; gunakan HTTPS jika server diakses lewat jaringan publik. Token admin tidak disimpan oleh antarmuka.

```bash
npm run check
npm test
npm run build
npm start
```

Jalankan perintah dari root proyek. Build hanya mengompilasi server ke `dist/`; folder `public/` tetap diperlukan. `HOST` default `127.0.0.1`, `PORT` default `8070`. Gunakan reverse proxy untuk deployment publik.

## Pengelolaan konten

Buka `/admin`, isi formulir portofolio lalu Simpan. Pilih konten yang sudah ada untuk mengedit atau menghapus. Slug menjadi identitas permanen. URL demo hanya menerima HTTP/HTTPS.

API publik: `GET /api/content`, `GET /api/config`. API admin membutuhkan `Authorization: Bearer <ADMIN_TOKEN>`: `POST /api/admin/aplikasi` dan `DELETE /api/admin/aplikasi/{slug}`. POST menambah atau memperbarui berdasarkan slug.

## Ruang lingkup dan data lama

Kontak kolaborasi menggunakan WhatsApp. Midtrans, checkout, akun pembeli, unduhan berbayar, email transaksi, image processing, dan Filament tidak disertakan. Pembayaran dan unduhan lama belum dimigrasikan. Database versi sebelumnya tidak dibaca otomatis; pindahkan konten yang masih relevan melalui admin. Jangan menyalin `.env` Laravel ke proyek baru.

Backup `data/content.json`. Penyimpanan JSON ditujukan untuk satu proses Node; gunakan database sebelum menjalankan beberapa proses/replica. Server ini belum dilengkapi rate limiting admin. Tidak ada proyek atau artikel contoh yang dipublikasikan otomatis.

## Artikel statis (manual)

Artikel tidak menggunakan database, API konten, atau admin. Setiap artikel adalah file HTML terpisah.

1. Salin `docs/article-template.html` ke `public/artikel/judul-artikel.html`.
2. Edit judul, meta description, dan isi HTML secara manual.
3. Tambahkan kartu tautan pada `public/artikel/index.html`, misalnya:

```html
<article class="card">
  <h3>Judul Artikel</h3>
  <p>Ringkasan singkat.</p>
  <a href="/artikel/judul-artikel">Baca artikel →</a>
</article>
```

Letakkan kartu di dalam `.grid`, lalu hapus pesan “Belum ada artikel tersedia.” saat artikel pertama ditambahkan. Halaman tersedia di `/artikel/judul-artikel` tanpa perlu build ulang. Menu dan footer tiap artikel dapat diedit langsung di file HTML. Artikel lama dalam JSON tidak ditampilkan; jika ada, pindahkan isinya secara manual ke file HTML.
