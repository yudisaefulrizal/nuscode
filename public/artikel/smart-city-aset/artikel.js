// Bagian interaktif artikel: alur big data dan contoh baris histori.
const $=id=>document.getElementById(id);
const el=(tag,attrs={},text)=>{const e=document.createElement(tag);for(const [k,v] of Object.entries(attrs))e.setAttribute(k,v);if(text!=null)e.textContent=text;return e;};

// ---- Pilihan berbentuk tombol (satu yang aktif).
function pills(box,items,label,onPick){
  const buttons=items.map((it,i)=>{const b=el('button',{type:'button','aria-pressed':'false'},label(it,i));b.addEventListener('click',()=>pick(i));return b;});
  box.replaceChildren(...buttons);
  function pick(i){buttons.forEach((b,k)=>b.setAttribute('aria-pressed',String(k===i)));onPick(items[i],i);}
  pick(0);
}

// ---- Alur big data.
const STEPS=[
  {img:'cam',name:'Kamera',speed:'per detik',text:'Menghitung kendaraan per arah dan hanya mengirimkan data agregat.'},
  {img:'gw',name:'Gateway',speed:'per detik',text:'Meneruskan data ke pengendali simpang dan pusat data.'},
  {img:'kafka',name:'Kafka',speed:'detik',text:'Menampung aliran data seluruh kota secara andal tanpa kehilangan data.'},
  {img:'spark',name:'Spark',speed:'per menit',text:'Mengagregasi antrean dan arus kendaraan per menit.'},
  {img:'db',name:'ClickHouse & Iceberg',speed:'menit–tahun',text:'Menyimpan ringkasan untuk dasbor serta data histori untuk pelatihan model.'},
  {img:'airflow',name:'Airflow',speed:'harian',text:'Menjadwalkan pelatihan ulang model dan mendistribusikan parameter baru ke lampu lalu lintas.'},
  {img:'ctrl',name:'Pengendali simpang',speed:'per detik',text:'Menerapkan parameter baru dengan pengaman konflik sebagai lapisan proteksi terakhir.'},
];
pills($('steps'),STEPS,(s,i)=>`${i+1} · ${s.name}`,(s,i)=>{$('step-meta').textContent=`TAHAP ${i+1} · ${s.speed}`;$('step-name').textContent=s.name;$('step-text').textContent=s.text;const img=$('step-img');img.src=`/artikel/smart-city-aset/alur-${s.img}.jpg`;img.alt=`Diagram 3D: ${s.name}`;});

// ---- Contoh baris histori.
const LABEL='jam 6 · 20 Okt 2026 · 9 Jumadilawal 1448';
const TABLES=[
  {name:'arah',cols:['Waktu','Label','Versi model','Id simpang','Id kamera','Lewat garis henti','Di zona'],
    rows:[['06:00:05',LABEL,'3','n0','o3','2','5'],['06:00:05',LABEL,'3','n0','o6','0','3'],['06:00:10',LABEL,'3','n0','o3','1','6']]},
  {name:'simpang',cols:['Waktu','Label','Versi model','Id simpang','Fase menyala','Lama hijau','Hijau habis'],
    rows:[['06:00:05',LABEL,'3','n0','2','14 dtk','−'],['06:00:05',LABEL,'3','n1','1','8 dtk','−'],['06:00:10',LABEL,'3','n0','2','19 dtk','fase 2']]},
  {name:'kota',cols:['Waktu','Label','Versi model','Waktu antre total'],
    rows:[['06:00:05',LABEL,'3','412 dtk'],['06:00:10',LABEL,'3','398 dtk'],['06:00:15',LABEL,'3','420 dtk']]},
];
pills($('tables'),TABLES,t=>`Baris ${t.name}`,t=>{
  const head=el('tr');for(const c of t.cols)head.append(el('th',{scope:'col'},c));
  const thead=el('thead');thead.append(head);
  const tbody=el('tbody');for(const r of t.rows){const tr=el('tr');for(const v of r)tr.append(el('td',{},v));tbody.append(tr);}
  $('table').replaceChildren(thead,tbody);
});
