import express from 'express';
import { timingSafeEqual } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import { collections, createStore, validateEntry, type Collection } from './store.js';
export function createApp(options: { file?: string; token?: string; whatsapp?: string } = {}) {
  const app = express();
  const store = createStore(options.file);
  app.disable('x-powered-by');
  app.use((_req, res, next) => {
    res.set({ 'X-Content-Type-Options': 'nosniff', 'Referrer-Policy': 'same-origin', 'Content-Security-Policy': "default-src 'self'; style-src 'self'; script-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'" }); next();
  });
  app.use(express.json({ limit: '128kb' }));
  app.get('/api/config', (_req, res) => res.json({ whatsapp: /^\d{8,15}$/.test(options.whatsapp || '') ? options.whatsapp : '' }));
  app.get('/api/content', (_req, res) => res.json(store.read()));
  app.use('/api/admin', (req, res, next) => {
    const token = options.token || '';
    const supplied = req.get('authorization')?.replace(/^Bearer /, '') || '';
    if (token.length < 32) { res.status(503).json({ error: 'Admin belum dikonfigurasi.' }); return; }
    const a = Buffer.from(token), b = Buffer.from(supplied);
    if (a.length !== b.length || !timingSafeEqual(a, b)) { res.status(401).json({ error: 'Token admin tidak valid.' }); return; }
    next();
  });
  app.post('/api/admin/:collection', (req, res) => {
    const collection = req.params.collection as Collection;
    if (!collections.includes(collection)) { res.sendStatus(404); return; }
    const entry = validateEntry(req.body);
    if (!entry) { res.status(422).json({ error: 'Data tidak valid. Periksa slug, judul, dan URL.' }); return; }
    const data = store.read();
    const index = data[collection].findIndex(item => item.slug === entry.slug);
    if (index < 0) data[collection].push(entry); else data[collection][index] = entry;
    store.save(data); res.json(entry);
  });
  app.delete('/api/admin/:collection/:slug', (req, res) => {
    const collection = req.params.collection as Collection;
    if (!collections.includes(collection)) { res.sendStatus(404); return; }
    const data = store.read();
    if (!data[collection].some(item => item.slug === req.params.slug)) { res.sendStatus(404); return; }
    data[collection] = data[collection].filter(item => item.slug !== req.params.slug);
    store.save(data); res.sendStatus(204);
  });
  const articleDirectory = fileURLToPath(new URL('../public/artikel', import.meta.url));
  app.get('/artikel', (_req, res) => res.sendFile(`${articleDirectory}/index.html`));
  // Old addresses of articles.
  app.get('/artikel/Cyber-Security-Digital-Twin', (_req, res) => res.redirect(301, '/artikel/cyber-security-digital-twin'));
  app.get(['/artikel/lampu-lalu-lintas-cerdas', '/artikel/smart-city'], (_req, res) => res.redirect(301, '/artikel/simulasi-kota-cerdas-berbasis-big-data-analytics-untuk-optimasi-lampu-lalu-lintas-adaptif'));
  app.use('/artikel', express.static(articleDirectory, { extensions: ['html'], index: false, redirect: false }));
  app.get('/produk', (_req, res) => res.redirect(301, '/#karya'));
  app.use(express.static(fileURLToPath(new URL('../public', import.meta.url))));
  app.get(['/', '/jasa-pembuatan-aplikasi', '/admin'], (_req, res) => res.sendFile(fileURLToPath(new URL('../public/index.html', import.meta.url))));
  app.use((_req, res) => res.status(404).json({ error: 'Halaman tidak ditemukan.' }));
  app.use((error: Error, _req: express.Request, res: express.Response, _next: express.NextFunction) => {
    const status = (error as Error & { status?: number }).status;
    res.status(status === 400 || status === 413 ? status : 500).json({ error: status === 400 ? 'JSON tidak valid.' : status === 413 ? 'Data terlalu besar.' : 'Terjadi kesalahan server.' });
  });
  return app;
}
