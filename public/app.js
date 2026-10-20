const root = document.querySelector('#app');
const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let data, config;
const contact = title => config.whatsapp ? `<a class="button" href="https://wa.me/${config.whatsapp}?text=${encodeURIComponent('Halo Nuscode, saya tertarik dengan '+title)}" target="_blank" rel="noopener">Hubungi via WhatsApp ↗</a>` : '<p class="muted">Kontak pemesanan belum tersedia.</p>';
function cards(kind) {
 const items = data[kind];
 if (!items.length) return '<p class="empty">Belum ada konten tersedia.</p>';
 return `<div class="grid">${items.map(item => `<article class="card"><h3>${escape(item.title)}</h3><p>${escape(item.description)}</p>${kind === 'aplikasi' ? `${item.url ? `<a href="${escape(item.url)}" target="_blank" rel="noopener">Lihat demo ↗</a>` : ''}${contact(item.title)}` : `<a href="/artikel/${encodeURIComponent(item.slug)}">Baca artikel →</a>`}</article>`).join('')}</div>`;
}
function admin() {
 root.innerHTML = '<h1>Kelola portofolio</h1><p>Masukkan token admin untuk mengubah konten. Token hanya disimpan selama halaman ini terbuka.</p><form id="editor"><label>Token admin<input id="token" type="password" required autocomplete="off"></label><label>Jenis<select id="kind"><option value="aplikasi">Portofolio aplikasi</option></select></label><label>Pilih konten<select id="existing"></select></label><label>Slug<input id="slug" required pattern="[a-z0-9]+(-[a-z0-9]+)*"></label><label>Judul<input id="title" required maxlength="200"></label><label>Ringkasan<textarea id="description" maxlength="2000"></textarea></label><label>Isi lengkap<textarea id="content" maxlength="100000"></textarea></label><label>URL demo<input id="url" type="url"></label><button>Simpan</button><button id="delete" type="button" class="secondary">Hapus</button><p id="status" role="status"></p></form>';
 const field = id => document.getElementById(id);
 const refresh = () => { field('existing').innerHTML = '<option value="">Konten baru</option>' + data[field('kind').value].map(x=>`<option value="${escape(x.slug)}">${escape(x.title)}</option>`).join(''); fill(); };
 const fill = () => { const item = data[field('kind').value].find(x=>x.slug === field('existing').value); for (const key of ['slug','title','description','content','url']) field(key).value = item?.[key] ?? ''; field('slug').readOnly = Boolean(item); };
 field('kind').onchange = refresh; field('existing').onchange = fill;
 async function mutate(method) {
  field('status').textContent = 'Menyimpan…';
  const body = Object.fromEntries(['slug','title','description','content','url'].map(k=>[k,field(k).value]));
  try {
   const response = await fetch(`/api/admin/${field('kind').value}${method === 'DELETE' ? '/'+encodeURIComponent(body.slug) : ''}`, {method,headers:{'Content-Type':'application/json',Authorization:'Bearer '+field('token').value},...(method === 'POST' ? {body:JSON.stringify(body)} : {})});
   if (!response.ok) throw Error((await response.json().catch(()=>({}))).error || 'Permintaan gagal.');
   data = await (await fetch('/api/content')).json(); refresh(); field('status').textContent='Perubahan berhasil disimpan.';
  } catch(error) { field('status').textContent=error.message; }
 }
 field('editor').onsubmit = event => { event.preventDefault(); mutate('POST'); };
 field('delete').onclick = () => { if(field('existing').value && confirm('Hapus konten ini?')) mutate('DELETE'); }; refresh();
}
async function start() {
 [data,config] = await Promise.all(['/api/content','/api/config'].map(async url=>{const response=await fetch(url);if(!response.ok)throw Error('Gagal memuat konten.');return response.json();}));
 const path = location.pathname.replace(/\/$/,'') || '/';
 document.querySelectorAll('nav a').forEach(link => { if (link.getAttribute('href') === path || (link.getAttribute('href') === '/artikel' && path.startsWith('/artikel/'))) link.setAttribute('aria-current', 'page'); });
 if (path === '/admin') { admin(); return; }
 if (path === '/') root.innerHTML = `<section class="hero"><div class="hero-inner"><div class="hero-copy"><h1>Kode dari Nusantara,<br>untuk Dunia.</h1><p>Wujudkan ide menjadi aplikasi yang berguna.</p><a class="button" href="#karya">Lihat portofolio →</a></div><div class="hero-visual"><img src="/images/nuscode-ai-developer-hero.webp" alt="Ilustrasi pengembang Nuscode dengan laptop dan jaringan teknologi" width="1280" height="854" fetchpriority="high"></div></div></section><section id="karya"><h2>Karya & portofolio</h2>${cards('aplikasi')}</section><section class="direction"><picture><source media="(max-width: 480px)" srcset="/images/nuscode-simple-digital-solutions-mobile.png"><img src="/images/nuscode-simple-digital-solutions.png" alt="Ilustrasi solusi digital untuk bisnis, pendidikan, dan masyarakat" width="1200" height="724" loading="lazy"></picture></section>`;
 else if(path === '/jasa-pembuatan-aplikasi') root.innerHTML = `<h1>Ide Anda, aplikasi nyata.</h1><p>Pengembangan aplikasi sesuai kebutuhan. Diskusikan ruang lingkup dan kebutuhan Anda bersama Nuscode.</p>${contact('jasa pembuatan aplikasi')}<h2>Portofolio</h2>${cards('aplikasi')}`;
 else root.innerHTML = '<h1>Halaman tidak ditemukan</h1><a href="/">Kembali ke beranda</a>';
}
start().catch(error=>{root.textContent=error.message;});
