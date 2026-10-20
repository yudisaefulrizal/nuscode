# Deploy Nuscode dengan Node.js

Gunakan Node.js 22 atau lebih baru. Arahkan terminal ke root proyek:

```bash
npm ci
cp .env.example .env
```

Isi `ADMIN_TOKEN` dengan token acak minimal 32 karakter dan `WHATSAPP_NUMBER` dengan nomor bisnis. Lalu:

```bash
npm run check
npm test
npm run build
npm start
```

Server default mendengarkan `127.0.0.1:8070`. Pasang reverse proxy Nginx ke port ini dan aktifkan HTTPS. Jalankan `npm start` melalui process manager dengan working directory root proyek. Tidak membutuhkan PHP-FPM, Composer, Artisan, atau Vite.

Pertahankan folder `public`, `dist`, `data`, package manifest, dan `.env` saat deployment. Backup `data/content.json`. Jalankan satu proses Node untuk penyimpanan JSON ini.
