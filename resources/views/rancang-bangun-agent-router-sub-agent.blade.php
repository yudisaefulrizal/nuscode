
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Keunggulan Rancang Bangun Agent Router + Sub-Agent</title>
  <meta name="description" content="Dari sistem monolitik menuju orkestrasi cerdas berbasis spesialisasi dan konteks — panduan teknis arsitektur AI multi-agent." />
  <link rel="icon" type="image/png" href="nuscode.png" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary: #343434;
      --secondary: #039f5b;
      --accent: #ffcc33;
      --text-dark: #222;
      --text-light: #555;
      --bg: #ffffff;
      --box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background: #f8fafc; color: var(--text-dark); scroll-behavior: smooth; overflow-x: hidden; line-height: 1.7; }

    /* HEADER */
    header { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem 5%; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid rgba(0,0,0,0.05); flex-wrap: wrap; }
    header img.logo { height: 45px; width: auto; transition: transform 0.3s ease; }
    header img.logo:hover { transform: scale(1.05); }
    .menu-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: var(--primary); cursor: pointer; z-index: 1001; }
    header nav { display: flex; align-items: center; transition: all 0.3s ease; }
    header nav a { text-decoration: none; color: var(--text-dark); margin-left: 1.5rem; font-weight: 600; font-size: 0.95rem; padding: 0.5rem 0; position: relative; transition: all 0.3s ease; white-space: nowrap; }
    header nav a:hover { color: var(--secondary); }
    header nav a::after { content: ""; position: absolute; width: 0; height: 3px; background: linear-gradient(90deg, var(--secondary), var(--accent)); bottom: 0; left: 0; transition: width 0.4s cubic-bezier(0.22,0.61,0.36,1); border-radius: 2px; }
    header nav a:hover::after { width: 100%; }

    /* ARTICLE HEADER */
    .article-header { background: linear-gradient(135deg, #1a1a1a, #2d3748); color: white; padding: 4rem 5% 3rem; text-align: center; position: relative; overflow: hidden; }
    .article-header::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(3,159,91,0.1)"/></svg>'); background-size: cover; }
    .article-header h1 { font-size: 2.2rem; margin-bottom: 1.2rem; font-weight: 800; line-height: 1.3; position: relative; z-index: 1; text-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    .article-subtitle { font-size: 1.1rem; opacity: 0.9; margin-bottom: 1.5rem; position: relative; z-index: 1; font-weight: 400; line-height: 1.6; }
    .meta { display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap; position: relative; z-index: 1; }
    .meta-item { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 0.6rem 1.2rem; border-radius: 50px; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s ease; flex-wrap: wrap; justify-content: center; }
    .meta-item:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

    /* ARTICLE CONTENT */
    .article-content { max-width: 1200px; margin: 0 auto; padding: 3rem 5% 4rem; }
    .section-title { color: var(--primary); font-size: 1.7rem; margin: 3rem 0 1.5rem; padding-bottom: 0.8rem; position: relative; font-weight: 700; }
    .section-title::after { content: ""; position: absolute; bottom: 0; left: 0; width: 60px; height: 4px; background: linear-gradient(90deg, var(--secondary), var(--accent)); border-radius: 2px; }

    /* CONCLUSION BOXES */
    .conclusion-box { background: white; border-radius: 14px; padding: 2rem; margin: 2rem 0; box-shadow: var(--box-shadow); border: 1px solid rgba(0,0,0,0.05); position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); }
    .conclusion-box:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
    .conclusion-box::before { content: ""; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: linear-gradient(180deg, var(--secondary), var(--accent)); }
    .conclusion-box h3 { color: var(--primary); font-size: 1.3rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap; }
    .conclusion-box h3 i { color: var(--secondary); background: rgba(3,159,91,0.1); padding: 0.5rem; border-radius: 8px; font-size: 1.1rem; flex-shrink: 0; }
    .conclusion-box p { color: var(--text-light); margin-bottom: 1rem; font-size: 1rem; line-height: 1.7; }

    .background-box::before { background: linear-gradient(180deg, #4f46e5, #7c3aed); }
    .objective-box::before { background: linear-gradient(180deg, #059669, #10b981); }
    .methodology-box::before { background: linear-gradient(180deg, #dc2626, #ef4444); }
    .data-box::before { background: linear-gradient(180deg, #ea580c, #f97316); }
    .findings-box::before { background: linear-gradient(180deg, #7c3aed, #8b5cf6); }
    .limitations-box::before { background: linear-gradient(180deg, #64748b, #94a3b8); }
    .insights-box::before { background: linear-gradient(180deg, #0ea5e9, #38bdf8); }
    .token-box::before { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
    .scale-box::before { background: linear-gradient(180deg, #10b981, #34d399); }
    .journal-box::before { background: linear-gradient(180deg, #6366f1, #a78bfa); }
    .compare-box::before { background: linear-gradient(180deg, #0891b2, #22d3ee); }

    /* Enhanced list */
    .enhanced-list { list-style: none; padding-left: 0; margin: 1.5rem 0; }
    .enhanced-list li { padding: 0.7rem 0.8rem 0.7rem 2.5rem; margin-bottom: 0.7rem; background: rgba(248,250,252,0.8); border-radius: 8px; position: relative; transition: all 0.3s ease; border-left: 3px solid transparent; color: var(--text-light); }
    .enhanced-list li:hover { background: rgba(3,159,91,0.05); border-left: 3px solid var(--secondary); transform: translateX(3px); }
    .enhanced-list li::before { content: "✓"; position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); color: var(--secondary); font-weight: bold; font-size: 1rem; background: rgba(3,159,91,0.1); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    /* Methodology grid */
    .methodology-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin: 2rem 0; }
    .method-card { background: white; padding: 1.5rem; border-radius: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.07); border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s ease; }
    .method-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .method-card h4 { color: var(--primary); font-size: 1.1rem; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }
    .method-card h4 i { color: var(--secondary); font-size: 1.2rem; flex-shrink: 0; }
    .method-card p { color: var(--text-light); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0.8rem; }

    /* Table */
    .styled-table { width: 100%; border-collapse: collapse; margin: 2rem 0; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); font-size: 0.9rem; }
    .styled-table th { background: linear-gradient(135deg, var(--primary), #2d3748); color: white; padding: 1rem; text-align: left; font-weight: 600; }
    .styled-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; color: var(--text-light); }
    .styled-table tr:last-child td { border-bottom: none; }
    .styled-table tr:hover { background: rgba(3,159,91,0.03); }
    .styled-table tr:nth-child(even) { background: #f8fafc; }

    /* Quote */
    .highlight-quote { background: linear-gradient(135deg, #f0f9ff, #fef7ff); border-radius: 14px; padding: 2rem; margin: 2.5rem 0; position: relative; border-left: 5px solid var(--accent); }
    .highlight-quote::before { content: "❝"; position: absolute; top: -15px; left: 1.5rem; font-size: 3rem; color: rgba(255,204,51,0.2); font-family: serif; }
    .highlight-quote p { font-size: 1.1rem; color: var(--text-dark); font-style: italic; line-height: 1.7; position: relative; z-index: 1; }

    /* Code block */
    .code-block { background: #1e1e2e; color: #cdd6f4; border-radius: 10px; padding: 1.5rem; margin: 1.5rem 0; font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto; line-height: 1.8; border-left: 4px solid var(--secondary); }
    .code-block .key { color: #89b4fa; }
    .code-block .val { color: #a6e3a1; }
    .code-block .comment { color: #6c7086; font-style: italic; }
    .code-block .str { color: #fab387; }

    /* Token stats */
    .token-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
    .token-stat { background: white; border-radius: 12px; padding: 1.2rem; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); }
    .token-stat .number { font-size: 2rem; font-weight: 800; color: var(--secondary); line-height: 1; margin-bottom: 0.3rem; }
    .token-stat .number.red { color: #dc2626; }
    .token-stat .number.blue { color: #0ea5e9; }
    .token-stat .number.amber { color: #f59e0b; }
    .token-stat .number.purple { color: #7c3aed; }
    .token-stat .label { font-size: 0.8rem; color: var(--text-light); line-height: 1.3; }

    /* Domain grid */
    .domain-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
    .domain-card { background: white; border-radius: 10px; padding: 1rem; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.06); border-top: 3px solid var(--secondary); transition: all 0.3s ease; }
    .domain-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .domain-card i { font-size: 1.5rem; color: var(--secondary); margin-bottom: 0.5rem; display: block; }
    .domain-card strong { display: block; font-size: 0.9rem; color: var(--primary); margin-bottom: 0.3rem; }
    .domain-card span { font-size: 0.78rem; color: var(--text-light); }

    /* Architecture diagram */
    .arch-diagram { background: #f8fafc; border-radius: 14px; padding: 2rem 2rem 1.5rem; margin: 1.5rem 0; text-align: center; border: 1px solid rgba(0,0,0,0.06); overflow-x: auto; position: relative; }

    /* Two column */
    .two-column-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

    /* Journal citation badge */
    .journal-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #6366f1, #a78bfa); color: white; padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem; }

    /* Journal comparison grid */
    .journal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1.5rem 0; }
    .journal-col { background: #f8fafc; border-radius: 12px; padding: 1.5rem; border: 1px solid rgba(0,0,0,0.06); }
    .journal-col h4 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .journal-col p { font-size: 0.9rem; color: var(--text-light); line-height: 1.6; margin-bottom: 0.8rem; }
    .journal-col .tag-row { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.5rem; }
    .j-tag { background: white; border: 1px solid rgba(0,0,0,0.08); border-radius: 50px; padding: 0.2rem 0.7rem; font-size: 0.75rem; color: var(--text-light); }

    /* Metric comparison bars */
    .metric-row { display: flex; align-items: center; gap: 0.8rem; margin: 0.6rem 0; font-size: 0.85rem; }
    .metric-label { min-width: 160px; color: var(--text-light); }
    .metric-bar-wrap { flex: 1; background: #e2e8f0; border-radius: 4px; height: 10px; }
    .metric-bar { height: 10px; border-radius: 4px; }
    .metric-val { min-width: 50px; text-align: right; font-weight: 600; color: var(--primary); font-size: 0.8rem; }

    /* Footer */
    footer { background: linear-gradient(135deg, #0d1b2a, #1b263b); color: #e2e8f0; padding: 3rem 5% 2rem; text-align: center; position: relative; overflow: hidden; }
    footer::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); }
    footer img { height: 45px; margin-bottom: 1.2rem; filter: brightness(0) invert(1); opacity: 0.9; width: auto; }
    footer p { font-size: 0.9rem; opacity: 0.8; line-height: 1.6; margin: 0.5rem 0; }
    .footer-tags { display: flex; justify-content: center; gap: 0.8rem; flex-wrap: wrap; margin-top: 1.5rem; }
    .tag { background: rgba(255,255,255,0.1); padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.8rem; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease; }
    .tag:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }

    /* Responsive */
    @media (max-width: 768px) {
      .menu-toggle { display: block; }
      header nav { display: none; position: fixed; top: 70px; left: 0; right: 0; width: 100%; flex-direction: column; align-items: center; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); padding: 1.5rem 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 999; }
      header nav.active { display: flex; }
      header nav a { margin: 0.7rem 0; margin-left: 0; width: 90%; padding: 0.8rem 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: center; font-size: 1rem; }
      .article-header h1 { font-size: 1.8rem; }
      .section-title { font-size: 1.5rem; }
      .methodology-grid { grid-template-columns: 1fr; }
      .two-column-grid { grid-template-columns: 1fr; }
      .journal-grid { grid-template-columns: 1fr; }
      .token-stats { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
      .article-header h1 { font-size: 1.5rem; }
      .token-stats { grid-template-columns: 1fr; }
      .domain-grid { grid-template-columns: 1fr 1fr; }
    }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.6s ease-out forwards; }
  </style>
</head>
<body>

  <header>
    <img src="/nuscode.png" alt="Logo" class="logo" />
    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
    <nav id="mainNav">
      <a href="/">Home</a>
      <a href="/artikel/osi-layers">OSI Layers</a>
      <a href="/review-jurnal">Review Jurnal Apps</a>
      <a href="/chatbot"><i class="fas fa-robot"></i> Chatbot</a>
    </nav>
  </header>

  <div class="article-header">
    <h1>Rancang Bangun Agent Router + Sub-Agent</h1>
    <p class="article-subtitle">Dari sistem monolitik menuju orkestrasi cerdas berbasis spesialisasi dan konteks — diperkuat temuan riset multi-agent global</p>
    <div class="meta">
      <div class="meta-item"><i class="fas fa-tags"></i><span>AI Architecture • Multi-Agent System • n8n • LLM Orchestration</span></div>
      <div class="meta-item"><i class="fas fa-flask"></i><span>Disertai Perbandingan Jurnal Ilmiah</span></div>
    </div>
  </div>

  <div class="article-content">
    <!-- DAFTAR PUSTAKA / REFERENSI -->
    <div class="conclusion-box" style="background: #f8fafc;">
      <h3><i class="fas fa-book"></i> Referensi Jurnal Ilmiah</h3>
      <p>Berikut adalah daftar pustaka dari 3 jurnal yang direview dan dijadikan pembanding dalam artikel ini.</p>
      
      <div style="margin: 1.5rem 0 0.5rem 0;">
        <div style="display: flex; align-items: baseline; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.6rem;">
          <span style="background: #6366f1; color: white; padding: 0.2rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600;">[1]</span>
          <div style="flex: 1;">
            <strong>Axetorn, J., Edholm, F., Dobslaw, F., & Gren, L.</strong> (2026). 
            <em>Addressing trust requirements in the design of an open-source multiagent LLM-based domain-specific chatbot</em>. 
            Requirements Engineering, 31, 1–32. 
            <span style="color: #059669; font-size: 0.85rem;">https://doi.org/10.1007/s00766-026-00457-w</span>
          </div>
        </div>

        <div style="display: flex; align-items: baseline; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.6rem;">
          <span style="background: #0891b2; color: white; padding: 0.2rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600;">[2]</span>
          <div style="flex: 1;">
            <strong>Jiang, Z., Rashik, M., Panchal, K., Jasim, M., Sarvghad, A., Riahi, P., Dewitt, E., Thurber, F., & Mahyar, N.</strong> (2023). 
            <em>CommunityBots: Creating and Evaluating A Multi-Agent Chatbot Platform for Public Input Elicitation</em>. 
            Proceedings of the ACM on Human-Computer Interaction (CSCW), 7(CSCW1), Article 36, 1–32. 
            <span style="color: #059669; font-size: 0.85rem;">https://doi.org/10.1145/3579469</span>
          </div>
        </div>

        <div style="display: flex; align-items: baseline; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.6rem;">
          <span style="background: #7c3aed; color: white; padding: 0.2rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600;">[3]</span>
          <div style="flex: 1;">
            <strong>Jiang, X., Hu, T., Qin, Y., Wang, G., Zhou, H., Chen, K., Huang, G., Lu, R., & Tang, S.</strong> (2025). 
            <em>ChatMap: Mining Human Thought Processes for Customer Service Chatbots via Multi-Agent Collaboration</em>. 
            In Findings of the Association for Computational Linguistics (ACL Findings 2025). 
            <span style="color: #059669; font-size: 0.85rem;">https://arxiv.org/abs/2501.00332</span>
          </div>
        </div>
      </div>

      <div style="background: rgba(255,193,7,0.08); padding: 1rem; border-radius: 10px; margin-top: 1rem; border-left: 4px solid var(--accent);">
        <p style="margin-bottom: 0; font-size: 0.85rem; color: var(--text-light);">
          <i class="fas fa-check-circle" style="color: var(--secondary); margin-right: 0.5rem;"></i>
          <strong>Ringkasan:</strong> Ketiga jurnal di atas secara independen memvalidasi keunggulan arsitektur multi-agent — 
          pemisahan peran agent, spesialisasi domain, serta manajemen konteks yang efisien — yang sejalan dengan pendekatan yang diuraikan dalam artikel ini.
        </p>
      </div>
    </div>
    <!-- 01 -->
    <h2 class="section-title">01 — Apa Itu Arsitektur Agent Router?</h2>
    <div class="conclusion-box background-box">
      <h3><i class="fas fa-robot"></i> Paradigma Baru Sistem AI Percakapan</h3>
      <p>Dalam ekosistem kecerdasan buatan percakapan, mayoritas sistem dibangun dengan satu model bahasa besar yang menangani seluruh spektrum permintaan pengguna — dari sapaan sederhana, pertanyaan teknis, hingga pengambilan keputusan kompleks. Pendekatan ini tampak mudah diimplementasikan, namun menyimpan biaya tersembunyi yang signifikan seiring bertambahnya kompleksitas domain.</p>
      <p><strong>Arsitektur Agent Router + Sub-Agent</strong> menawarkan paradigma yang berbeda. Sebuah komponen sentral — disebut <em>Router</em> — bertugas tunggal: membaca niat pengguna, merekonstruksi query secara semantik, lalu mendelegasikan ke satu dari beberapa Sub-Agent yang masing-masing ahli dalam domain tertentu.</p>
      <div class="highlight-quote">
        <p>Analogi: Bayangkan sebuah rumah sakit. Pasien tidak langsung ditangani oleh seluruh dokter sekaligus — ada triase yang mengarahkan pasien ke spesialis yang tepat. Router adalah triase; sub-agent adalah para spesialis.</p>
      </div>
    </div>

    <!-- 02 -->
    <h2 class="section-title">02 — Masalah Sistem Monolitik</h2>
    <div class="conclusion-box methodology-box">
      <h3><i class="fas fa-exclamation-circle"></i> Tiga Tekanan Struktural</h3>
      <p>Sistem AI percakapan berbasis model tunggal menghadapi tiga tekanan struktural yang semakin berat seiring pertumbuhan cakupan layanan.</p>
      <ul class="enhanced-list">
        <li><strong>Prompt Membengkak:</strong> Ketika satu model harus melayani banyak domain, system prompt-nya harus mencakup semua pengetahuan, semua aturan, dan semua konteks dari setiap domain. Sebuah sistem layanan dengan 8 bidang bisa menghasilkan system prompt ribuan token hanya untuk instruksi dasar — sebelum satu kata pengguna pun masuk ke konteks.</li>
        <li><strong>Degradasi Fokus:</strong> Model bahasa besar mengalami penurunan performa ketika instruksi dalam konteks terlalu panjang atau mengandung domain yang saling bertentangan. Instruksi untuk menjawab pertanyaan teknis dengan format kode bisa "bocor" ke jawaban pertanyaan emosional yang seharusnya hangat.</li>
        <li><strong>Sulit Diuji dan Dipelihara:</strong> Ketika sebuah domain mengalami perubahan kebijakan, satu model monolitik mengharuskan seluruh sistem dievaluasi ulang. Tidak ada isolasi; perubahan di satu bagian berpotensi memengaruhi perilaku di bagian lain yang tidak terduga.</li>
      </ul>
      <div class="highlight-quote">
        <p>"Sistem yang mencoba tahu segalanya seringkali tidak tahu apa-apa dengan cukup baik."</p>
      </div>
    </div>

    <!-- 03 -->
    <h2 class="section-title">03 — Arsitektur: Router + Sub-Agent</h2>
    <div class="conclusion-box objective-box">
      <h3><i class="fas fa-sitemap"></i> Dua Lapisan Tanggung Jawab</h3>
      <p>Sistem yang dirancang dengan pola ini memisahkan tanggung jawab secara eksplisit ke dalam dua lapisan: <strong>Router</strong> sebagai satu-satunya titik kontak dengan input pengguna mentah, dan <strong>Sub-Agent</strong> sebagai entitas terisolasi yang masing-masing memiliki system prompt, referensi pengetahuan, dan format output tersendiri.</p>

      <div class="arch-diagram">
        <svg viewBox="0 0 620 480" xmlns="http://www.w3.org/2000/svg" style="width:100%;max-width:620px;font-family:'Poppins',sans-serif;">
          <defs>
            <marker id="arrow-green" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 Z" fill="#039f5b"/></marker>
            <marker id="arrow-amber" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 Z" fill="#f59e0b"/></marker>
            <marker id="arrow-purple" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 Z" fill="#7c3aed"/></marker>
            <linearGradient id="grad-router" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#039f5b"/><stop offset="100%" style="stop-color:#10b981"/></linearGradient>
            <linearGradient id="grad-input" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#2d3748"/><stop offset="100%" style="stop-color:#1a1a1a"/></linearGradient>
            <linearGradient id="grad-output" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#4f46e5"/><stop offset="100%" style="stop-color:#7c3aed"/></linearGradient>
            <linearGradient id="grad-context" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#f59e0b"/><stop offset="100%" style="stop-color:#fbbf24"/></linearGradient>
            <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%"><feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.10)"/></filter>
          </defs>
          <rect x="195" y="16" width="230" height="44" rx="10" fill="url(#grad-input)" filter="url(#shadow)"/>
          <text x="310" y="33" text-anchor="middle" fill="white" font-size="11" font-weight="600">👤  Input Pengguna</text>
          <text x="310" y="50" text-anchor="middle" fill="rgba(255,255,255,0.7)" font-size="9.5">pertanyaan giliran ke-N</text>
          <line x1="310" y1="60" x2="310" y2="95" stroke="#039f5b" stroke-width="2" marker-end="url(#arrow-green)"/>
          <rect x="160" y="98" width="300" height="54" rx="12" fill="url(#grad-router)" filter="url(#shadow)"/>
          <text x="310" y="119" text-anchor="middle" fill="white" font-size="13" font-weight="700">⇄  AGENT ROUTER</text>
          <text x="310" y="137" text-anchor="middle" fill="rgba(255,255,255,0.85)" font-size="9.5">Klasifikasi · Rekonstruksi · Delegasi</text>
          <line x1="310" y1="152" x2="310" y2="192" stroke="#039f5b" stroke-width="2" marker-end="url(#arrow-green)"/>
          <rect x="20" y="195" width="90" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" filter="url(#shadow)"/>
          <text x="65" y="211" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="700">📋 Pendaftaran</text>
          <text x="65" y="225" text-anchor="middle" fill="#555" font-size="8">Biaya · Syarat · Seleksi</text>
          <rect x="122" y="195" width="88" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" filter="url(#shadow)"/>
          <text x="166" y="211" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="700">🕌 Kesantrian</text>
          <text x="166" y="225" text-anchor="middle" fill="#555" font-size="8">Absensi · Izin</text>
          <rect x="222" y="195" width="96" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" filter="url(#shadow)"/>
          <text x="270" y="211" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="700">🎓 Kependidikan</text>
          <text x="270" y="225" text-anchor="middle" fill="#555" font-size="8">Hafalan · Kurikulum</text>
          <rect x="330" y="195" width="80" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" filter="url(#shadow)"/>
          <text x="370" y="211" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="700">📣 Humas</text>
          <text x="370" y="225" text-anchor="middle" fill="#555" font-size="8">Publik · Dakwah</text>
          <rect x="422" y="195" width="70" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" filter="url(#shadow)"/>
          <text x="457" y="211" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="700">💻 IT</text>
          <text x="457" y="225" text-anchor="middle" fill="#555" font-size="8">Server · Error</text>
          <rect x="504" y="195" width="70" height="38" rx="8" fill="white" stroke="#039f5b" stroke-width="1.5" stroke-dasharray="4,2" filter="url(#shadow)"/>
          <text x="539" y="215" text-anchor="middle" fill="#039f5b" font-size="9" font-weight="600">+ domain</text>
          <text x="539" y="227" text-anchor="middle" fill="#aaa" font-size="8">modular</text>
          <line x1="240" y1="152" x2="65" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="270" y1="152" x2="166" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="295" y1="152" x2="270" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="325" y1="152" x2="370" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="350" y1="152" x2="457" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="370" y1="152" x2="539" y2="195" stroke="#039f5b" stroke-width="1.2" stroke-dasharray="3,2" opacity="0.6"/>
          <line x1="310" y1="233" x2="310" y2="272" stroke="#7c3aed" stroke-width="2" marker-end="url(#arrow-purple)"/>
          <rect x="160" y="275" width="300" height="44" rx="10" fill="url(#grad-output)" filter="url(#shadow)"/>
          <text x="310" y="293" text-anchor="middle" fill="white" font-size="11" font-weight="700">✓  Respons Terstruktur</text>
          <text x="310" y="309" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="9">"jawaban" + "status_terjawab"</text>
          <rect x="195" y="338" width="230" height="30" rx="15" fill="url(#grad-context)" filter="url(#shadow)"/>
          <text x="310" y="349" text-anchor="middle" fill="#1a1a1a" font-size="9.5" font-weight="700">💾  Konteks S-P-O disimpan</text>
          <text x="310" y="362" text-anchor="middle" fill="rgba(0,0,0,0.55)" font-size="8.5">"User menanyakan biaya SMP Al Yusro" (~10 token)</text>
          <line x1="310" y1="319" x2="310" y2="336" stroke="#f59e0b" stroke-width="1.8" marker-end="url(#arrow-amber)"/>
          <line x1="195" y1="353" x2="100" y2="353" stroke="#f59e0b" stroke-width="2"/>
          <line x1="100" y1="353" x2="100" y2="125" stroke="#f59e0b" stroke-width="2"/>
          <line x1="100" y1="125" x2="158" y2="125" stroke="#f59e0b" stroke-width="2" marker-end="url(#arrow-amber)"/>
          <rect x="52" y="205" width="38" height="95" rx="6" fill="rgba(245,158,11,0.1)" stroke="rgba(245,158,11,0.3)" stroke-width="1"/>
          <text transform="translate(75,298) rotate(-90)" text-anchor="middle" fill="#92680a" font-size="8.5" font-weight="600" letter-spacing="0.03em">menggantikan histori percakapan</text>
          <text x="127" y="348" text-anchor="middle" fill="#92680a" font-size="8.5" font-weight="600">↩ diwariskan ke giliran berikutnya</text>
          <rect x="195" y="400" width="230" height="38" rx="10" fill="white" stroke="#2d3748" stroke-width="1.5" stroke-dasharray="5,3" filter="url(#shadow)" opacity="0.75"/>
          <text x="310" y="416" text-anchor="middle" fill="#2d3748" font-size="10" font-weight="600">👤  Input Pengguna — Giliran N+1</text>
          <text x="310" y="430" text-anchor="middle" fill="#888" font-size="8.5">pertanyaan ambigu / tanpa entitas baru</text>
          <line x1="427" y1="419" x2="490" y2="419" stroke="#2d3748" stroke-width="1.5" stroke-dasharray="3,2"/>
          <line x1="490" y1="419" x2="490" y2="125" stroke="#2d3748" stroke-width="1.5" stroke-dasharray="3,2"/>
          <line x1="490" y1="125" x2="462" y2="125" stroke="#2d3748" stroke-width="1.5" stroke-dasharray="3,2" marker-end="url(#arrow-green)"/>
          <text x="508" y="280" transform="rotate(90,508,280)" text-anchor="middle" fill="#555" font-size="8" font-weight="600">+ konteks S-P-O diwariskan</text>
        </svg>
        <div style="display:flex;justify-content:center;gap:1.5rem;flex-wrap:wrap;margin-top:1rem;font-size:0.78rem;color:var(--text-light);">
          <span><span style="display:inline-block;width:12px;height:3px;background:#039f5b;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Alur utama</span>
          <span><span style="display:inline-block;width:12px;height:3px;background:#7c3aed;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Respons sub-agent</span>
          <span><span style="display:inline-block;width:12px;height:3px;background:#f59e0b;border-radius:2px;vertical-align:middle;margin-right:4px;"></span>Feedback konteks S-P-O</span>
          <span><span style="display:inline-block;width:12px;height:3px;background:#2d3748;border-radius:2px;vertical-align:middle;margin-right:4px;border-top:2px dashed #2d3748;"></span>Giliran berikutnya</span>
        </div>
      </div>
      <p>Tidak ada satu model yang mencoba tahu segalanya — setiap agent hanya tahu apa yang perlu ia tahu.</p>
    </div>

    <!-- 04 -->
    <h2 class="section-title">04 — Keunggulan 1: Efisiensi Token</h2>
    <div class="conclusion-box token-box">
      <h3><i class="fas fa-coins"></i> Logika Token yang Dibalik</h3>
      <p>Token adalah satuan biaya dan latensi dalam sistem berbasis model bahasa besar. Dalam arsitektur monolitik, seluruh system prompt dikirim ulang di setiap panggilan API, bahkan ketika pengguna hanya mengucapkan salam.</p>
      <p>Arsitektur Router + Sub-Agent membalik logika ini. Router hanya membawa instruksi ringkas untuk routing. Sub-agent yang dipanggil hanya membawa konteks domain-nya sendiri.</p>
      <div class="token-stats">
        <div class="token-stat"><div class="number">~600</div><div class="label">Token Router per Panggilan</div></div>
        <div class="token-stat"><div class="number">~400</div><div class="label">Token Sub-Agent per Domain</div></div>
        <div class="token-stat"><div class="number blue">~10</div><div class="label">Token Konteks S-P-O</div></div>
        <div class="token-stat"><div class="number red">4000+</div><div class="label">Token Sistem Monolitik</div></div>
      </div>
      <p>Sistem ini menggantikan mekanisme memori percakapan penuh dengan sebuah string ringkas berformat <strong>Subjek-Predikat-Objek</strong>. Alih-alih mengirim ulang 10 pesan terakhir, sistem hanya mengirim satu kalimat seperti <em>"User menanyakan biaya masuk SMP Al Yusro"</em> — sekitar 10 token.</p>
      <div style="background:rgba(245,158,11,0.08);padding:1rem;border-radius:10px;border-left:4px solid #f59e0b;margin-top:1rem;">
        <strong>Hasil Nyata:</strong> Konteks S-P-O menghemat 90%+ token dibandingkan memory window penuh, sambil mempertahankan akurasi warisan entitas antar giliran percakapan.
      </div>
    </div>

    <!-- 05 -->
    <h2 class="section-title">05 — Keunggulan 2: Spesialisasi Konteks</h2>
    <div class="conclusion-box findings-box">
      <h3><i class="fas fa-layer-group"></i> Domain yang Bersih, Jawaban yang Tajam</h3>
      <p>Setiap sub-agent beroperasi dengan system prompt yang dirancang khusus untuk satu domain. Tidak ada instruksi yang saling mengganggu — model yang hanya "memikirkan" satu domain pada satu waktu menghasilkan respons yang lebih terfokus, lebih akurat, dan lebih konsisten.</p>
      <div class="domain-grid">
        <div class="domain-card"><i class="fas fa-graduation-cap"></i><strong>Kependidikan</strong><span>Kurikulum · Hafalan · Metode Ajar</span></div>
        <div class="domain-card"><i class="fas fa-mosque"></i><strong>Kesantrian</strong><span>Absensi · Izin · Pelanggaran</span></div>
        <div class="domain-card"><i class="fas fa-clipboard-list"></i><strong>Pendaftaran</strong><span>Syarat · Biaya · Seleksi</span></div>
        <div class="domain-card"><i class="fas fa-server"></i><strong>IT</strong><span>Server · Jaringan · Coding</span></div>
        <div class="domain-card"><i class="fas fa-bullhorn"></i><strong>Humas & Dakwah</strong><span>Pengumuman · Publik</span></div>
        <div class="domain-card"><i class="fas fa-images"></i><strong>Media</strong><span>Caption · Desain · Konten</span></div>
        <div class="domain-card"><i class="fas fa-map-marker-alt"></i><strong>Alamat</strong><span>Lokasi · Petunjuk Arah</span></div>
        <div class="domain-card"><i class="fas fa-comments"></i><strong>Basa-Basi</strong><span>Salam · Sapaan · Small Talk</span></div>
      </div>
    </div>

    <!-- 06 -->
    <h2 class="section-title">06 — Keunggulan 3: Manajemen Konteks Lintas Giliran</h2>
    <div class="conclusion-box insights-box">
      <h3><i class="fas fa-exchange-alt"></i> Konteks S-P-O: Solusi Elegan</h3>
      <p>Arsitektur ini memperkenalkan <strong>konteks S-P-O (Subjek-Predikat-Objek)</strong> — setiap respons router menghasilkan satu string yang merangkum inti percakapan, bukan seluruh history.</p>
      <div class="code-block">
        <span class="comment">// Giliran 1 — User bertanya tentang program SMP</span><br>
        <span class="key">konteks</span>: <span class="str">"User menanyakan ketersediaan program SMP Al Yusro"</span><br><br>
        <span class="comment">// Giliran 2 — User bertanya: "terus biayanya berapa?"</span><br>
        <span class="comment">// Router mewarisi entitas "SMP" dari konteks sebelumnya</span><br>
        <span class="key">query</span>: <span class="str">"biaya masuk SMP Al Yusro"</span> <span class="comment">← bukan hanya "biaya masuk"</span>
      </div>
    </div>

    <!-- 07 -->
    <h2 class="section-title">07 — Keunggulan 4: Skalabilitas Modular</h2>
    <div class="conclusion-box scale-box">
      <h3><i class="fas fa-expand-arrows-alt"></i> Tumbuh Horizontal, Bukan Vertikal</h3>
      <p>Sistem monolitik tumbuh secara vertikal. Arsitektur Router + Sub-Agent tumbuh secara horizontal — menambah domain baru berarti menambah satu sub-agent baru, lalu mendaftarkannya dalam routing table Router.</p>
      <div class="methodology-grid">
        <div class="method-card">
          <h4><i class="fas fa-plus-circle"></i> Penambahan Domain Baru</h4>
          <p>Komponen yang sudah berjalan tidak perlu dimodifikasi. Sub-agent yang sudah stabil tetap stabil. Hanya Router yang perlu memperbarui daftar agent dan aturan routing-nya.</p>
        </div>
        <div class="method-card">
          <h4><i class="fas fa-dollar-sign"></i> Penggantian Model per Domain</h4>
          <p>Modularitas ini membuka kemungkinan menggunakan model yang berbeda untuk domain berbeda. Domain FAQ sederhana bisa menggunakan model yang lebih ringan dan murah.</p>
        </div>
      </div>
    </div>

    <!-- 08 -->
    <h2 class="section-title">08 — Keunggulan 5: Output Terstruktur dan Deterministik</h2>
    <div class="conclusion-box data-box">
      <h3><i class="fas fa-code"></i> JSON Berskema — Dapat Diandalkan</h3>
      <p>Ketika sistem AI diintegrasikan dengan platform otomasi seperti <strong>n8n</strong>, format output yang konsisten adalah kebutuhan mutlak. Dalam arsitektur ini, setiap lapisan menghasilkan JSON yang didefinisikan skemanya secara eksplisit.</p>
      <div class="two-column-grid" style="margin:1.5rem 0;">
        <div>
          <p style="font-weight:600;color:var(--primary);margin-bottom:0.5rem;"><i class="fas fa-random" style="color:var(--secondary);margin-right:6px;"></i>Router Output</p>
          <div class="code-block" style="margin:0;">
            {<br>&nbsp;&nbsp;<span class="key">"agent"</span>: <span class="str">"pendaftaran"</span>,<br>&nbsp;&nbsp;<span class="key">"query"</span>: <span class="str">"biaya SMP..."</span>,<br>&nbsp;&nbsp;<span class="key">"konteks"</span>: <span class="str">"User ..."</span>,<br>&nbsp;&nbsp;<span class="key">"confidence"</span>: <span class="val">0.82</span><br>}
          </div>
        </div>
        <div>
          <p style="font-weight:600;color:var(--primary);margin-bottom:0.5rem;"><i class="fas fa-robot" style="color:var(--secondary);margin-right:6px;"></i>Sub-Agent Output</p>
          <div class="code-block" style="margin:0;">
            {<br>&nbsp;&nbsp;<span class="key">"jawaban"</span>: <span class="str">"Biaya masuk SMP..."</span>,<br>&nbsp;&nbsp;<span class="key">"status_terjawab"</span>: <span class="str">"ya"</span><br>}<br><br><span class="comment">// ✓ skema tervalidasi</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 09 — SECTION BARU: PERBANDINGAN JURNAL (3 jurnal, tanpa 2018) -->
    <h2 class="section-title">09 — Perbandingan dengan Riset Multi-Agent Global</h2>

    <div class="conclusion-box journal-box">
      <h3><i class="fas fa-book-open"></i> Landasan dari Penelitian Akademik</h3>
      <div class="journal-badge"><i class="fas fa-graduation-cap"></i> Didukung 3 Jurnal Ilmiah Peer-Reviewed</div>
      <p>Arsitektur yang diajukan bukan hanya eksperimen praktis — ia bersesuaian dengan temuan-temuan riset multi-agent global yang telah dievaluasi secara akademik. Berikut perbandingan dengan tiga studi ilmiah yang relevan.</p>

      <!-- Jurnal 1: Axetorn et al. 2026 -->
      <div style="margin:2rem 0;padding:1.5rem;background:#f8fafc;border-radius:14px;border:1px solid rgba(99,102,241,0.2);">
        <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
          <div style="background:linear-gradient(135deg,#6366f1,#a78bfa);color:white;padding:0.4rem 1rem;border-radius:50px;font-size:0.78rem;font-weight:700;white-space:nowrap;">Jurnal 1 · Requirements Engineering, 2026</div>
          <div style="font-size:0.85rem;color:var(--text-light);">Axetorn et al. — <em>Addressing trust requirements in the design of an open-source multiagent LLM-based domain-specific chatbot</em></div>
        </div>

        <div class="journal-grid">
          <div class="journal-col">
            <h4 style="color:#6366f1;"><i class="fas fa-flask" style="color:#6366f1;"></i> Temuan Riset</h4>
            <p>Studi ini merancang chatbot HR multi-agent menggunakan arsitektur tiga-agent: <strong>judge</strong> (filter dokumen relevan), <strong>generator</strong> (sintesis jawaban), dan <strong>checker</strong> (validasi kualitas) — menunjukkan bahwa pemisahan peran agent meningkatkan keandalan sistem secara signifikan.</p>
            <p>Hasil kuantitatif: sistem mencapai skor <strong>faithfulness ≥ 0.86</strong> dan disetujui oleh 9 dari 11 peserta evaluasi sebagai siap deployment. Temuan kunci: <em>reliabilitas</em> adalah faktor kepercayaan paling kritis bagi pengguna.</p>
            <div class="tag-row"><span class="j-tag">Judge Agent</span><span class="j-tag">Generator Agent</span><span class="j-tag">Checker Agent</span><span class="j-tag">LangGraph</span><span class="j-tag">DeepEval</span></div>
          </div>
          <div class="journal-col">
            <h4 style="color:#039f5b;"><i class="fas fa-exchange-alt" style="color:#039f5b;"></i> Perbandingan</h4>
            <p>Arsitektur yang diajukan mengimplementasikan prinsip serupa: Router berperan sebagai <em>judge</em> (menentukan domain yang tepat), setiap Sub-Agent sebagai <em>generator</em> (menghasilkan jawaban dari data domain), dan field <code>status_terjawab</code> sebagai <em>checker</em> ringan.</p>
            <p>Perbedaan utama: arsitektur ini menggunakan <strong>n8n workflow</strong> sebagai orkestrator (bukan LangGraph), dan lebih menekankan efisiensi token via S-P-O dibanding validasi kualitas iteratif.</p>
            <div style="margin-top:0.8rem;">
              <div class="metric-row"><span class="metric-label">Pemisahan peran agent</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:90%;background:#6366f1;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Output terstruktur</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:95%;background:#039f5b;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Efisiensi token S-P-O</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:100%;background:#f59e0b;"></div></div><span class="metric-val">Lebih jauh</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Jurnal 2: Jiang et al. 2023 (CommunityBots) -->
      <div style="margin:2rem 0;padding:1.5rem;background:#f8fafc;border-radius:14px;border:1px solid rgba(14,165,233,0.2);">
        <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
          <div style="background:linear-gradient(135deg,#0891b2,#22d3ee);color:white;padding:0.4rem 1rem;border-radius:50px;font-size:0.78rem;font-weight:700;white-space:nowrap;">Jurnal 2 · ACM CSCW, 2023</div>
          <div style="font-size:0.85rem;color:var(--text-light);">Jiang et al. — <em>CommunityBots: Creating and Evaluating A Multi-Agent Chatbot Platform for Public Input Elicitation</em></div>
        </div>

        <div class="journal-grid">
          <div class="journal-col">
            <h4 style="color:#0891b2;"><i class="fas fa-flask" style="color:#0891b2;"></i> Temuan Riset</h4>
            <p>CommunityBots membagi percakapan multi-domain ke dalam tiga chatbot spesialis (household, work, healthcare) dengan mekanisme <strong>CTM (Conversation and Topic Management)</strong> yang mengelola perpindahan topik dan chatbot secara otomatis.</p>
            <p>Dari 96 peserta: pengguna CommunityBots memberikan respons <strong>10% lebih spesifik</strong>, <strong>20% lebih ekspansif</strong>, dan mengalami <strong>50% lebih sedikit interupsi</strong> dibanding sistem agen tunggal.</p>
            <div class="tag-row"><span class="j-tag">CTM Mechanism</span><span class="j-tag">Topic Switching</span><span class="j-tag">Chatbot Switching</span><span class="j-tag">NLU</span></div>
          </div>
          <div class="journal-col">
            <h4 style="color:#039f5b;"><i class="fas fa-exchange-alt" style="color:#039f5b;"></i> Perbandingan</h4>
            <p>CommunityBots dan arsitektur yang diajukan sama-sama membuktikan bahwa spesialisasi domain per-agent menghasilkan kualitas interaksi yang lebih tinggi. CTM di CommunityBots analog dengan Router — keduanya mendeteksi intent dan mengarahkan ke agent yang tepat.</p>
            <p>Arsitektur ini melangkah lebih jauh dengan mekanisme <strong>warisan konteks S-P-O</strong> yang ringan, sedangkan CommunityBots menggunakan threshold panjang respons untuk deteksi disengagement.</p>
            <div style="margin-top:0.8rem;">
              <div class="metric-row"><span class="metric-label">Spesialisasi domain</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:95%;background:#0891b2;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Manajemen perpindahan</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:88%;background:#0891b2;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Efisiensi token</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:100%;background:#f59e0b;"></div></div><span class="metric-val">↑ Lebih efisien</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Jurnal 3: ChatMap -->
      <div style="margin:2rem 0;padding:1.5rem;background:#f8fafc;border-radius:14px;border:1px solid rgba(139,92,246,0.2);">
        <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
          <div style="background:linear-gradient(135deg,#7c3aed,#c4b5fd);color:white;padding:0.4rem 1rem;border-radius:50px;font-size:0.78rem;font-weight:700;white-space:nowrap;">Jurnal 3 · ACL Findings, 2025</div>
          <div style="font-size:0.85rem;color:var(--text-light);">Jiang et al. (Alibaba) — <em>ChatMap: Mining Human Thought Processes for Customer Service Chatbots via Multi-Agent Collaboration</em></div>
        </div>

        <div class="journal-grid">
          <div class="journal-col">
            <h4 style="color:#7c3aed;"><i class="fas fa-flask" style="color:#7c3aed;"></i> Temuan Riset</h4>
            <p>ChatMap dari Alibaba/Taobao menggunakan pipeline 5-agent: <strong>Extraction → Clustering → Induction → Post-Processing → Quality Inspection</strong> untuk menambang "service thoughts" dari 10.000 dialog historis.</p>
            <p>Hasil online A/B test di Taobao: Human Agent Transfer Rate turun dari 20.28% → <strong>18.99%</strong>, Customer Satisfaction Rate naik dari 45.32% → <strong>46.10%</strong>. Pendekatan ini setara dengan anotasi manual ahli.</p>
            <div class="tag-row"><span class="j-tag">Service Thoughts</span><span class="j-tag">Inductive Reasoning</span><span class="j-tag">Quality Inspection</span><span class="j-tag">A/B Testing</span></div>
          </div>
          <div class="journal-col">
            <h4 style="color:#039f5b;"><i class="fas fa-exchange-alt" style="color:#039f5b;"></i> Perbandingan</h4>
            <p>Keduanya membuktikan bahwa pemisahan tanggung jawab per-agent — bukan satu model monolitik — menghasilkan kualitas dan keandalan yang lebih tinggi. "Service thoughts" di ChatMap analog dengan system prompt per-domain di Sub-Agent.</p>
            <p>Arsitektur yang diajukan menggunakan data statis (dokumen kebijakan pondok), sementara ChatMap mined knowledge dari dialog historis. Keduanya menghasilkan <strong>domain-specific knowledge yang terisolasi</strong> per agent.</p>
            <div style="margin-top:0.8rem;">
              <div class="metric-row"><span class="metric-label">Isolasi pengetahuan domain</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:92%;background:#7c3aed;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Pipeline multi-agent</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:85%;background:#7c3aed;"></div></div><span class="metric-val">Selaras</span></div>
              <div class="metric-row"><span class="metric-label">Produksi nyata</span><div class="metric-bar-wrap"><div class="metric-bar" style="width:100%;background:#039f5b;"></div></div><span class="metric-val">Keduanya</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 10 — Tabel Komparasi Lintas Jurnal (3 jurnal) -->
    <h2 class="section-title">10 — Sintesis vs Riset Global</h2>
    <div class="conclusion-box compare-box">
      <h3><i class="fas fa-balance-scale"></i> Posisi dalam Lanskap Riset Multi-Agent</h3>
      <p>Berikut adalah peta perbandingan pendekatan yang diajukan dengan tiga riset multi-agent terkemuka di bidangnya.</p>
      <div style="overflow-x:auto;">
        <table class="styled-table">
          <thead><tr><th>Dimensi</th><th>Arsitektur yang Diajukan</th><th>Axetorn 2026 (HR Bot)</th><th>CommunityBots 2023</th><th>ChatMap 2025</th></tr></thead>
          <tbody>
            <tr><td><strong>Arsitektur utama</strong></td><td style="color:#059669;">Router + N Sub-Agent</td><td>Judge + Generator + Checker</td><td>3 Specialist Bots + CTM</td><td>5-Agent Pipeline</td></tr>
            <tr><td><strong>Isolasi domain</strong></td><td style="color:#059669;">✓ Per sub-agent</td><td style="color:#059669;">✓ Per agent role</td><td style="color:#059669;">✓ Per chatbot</td><td style="color:#059669;">✓ Per topic cluster</td></tr>
            <tr><td><strong>Manajemen konteks</strong></td><td style="color:#059669;">S-P-O (~10 token)</td><td style="color:#dc2626;">Full history / RAG</td><td style="color:#dc2626;">Response length threshold</td><td style="color:#dc2626;">Full dialog history</td></tr>
            <tr><td><strong>Output format</strong></td><td style="color:#059669;">JSON berskema</td><td>Natural language + citation</td><td>Natural language</td><td>Natural language + thought</td></tr>
            <tr><td><strong>Integrasi otomasi</strong></td><td style="color:#059669;">n8n (native)</td><td style="color:#dc2626;">Terminal app, tidak native</td><td style="color:#dc2626;">Web UI only</td><td style="color:#dc2626;">Platform internal Taobao</td></tr>
            <tr><td><strong>Efisiensi token</strong></td><td style="color:#059669;">Sangat tinggi (S-P-O)</td><td style="color:#f59e0b;">Sedang (RAG filtering)</td><td style="color:#dc2626;">Rendah (full history)</td><td style="color:#f59e0b;">Sedang (per-cluster)</td></tr>
            <tr><td><strong>Pengujian data nyata</strong></td><td style="color:#059669;">112 pertanyaan nyata</td><td style="color:#059669;">2.860 evaluation runs</td><td style="color:#059669;">96 crowd workers</td><td style="color:#059669;">A/B test Taobao live</td></tr>
            <tr><td><strong>Akurasi routing</strong></td><td style="color:#059669;">100% (112/112)</td><td style="color:#059669;">≥ 0.86 faithfulness</td><td style="color:#059669;">Signifikan lebih baik</td><td style="color:#059669;">+0.78% satisfaction</td></tr>
          </tbody>
        </table>
      </div>
      <div style="background:rgba(14,165,233,0.08);padding:1.2rem;border-radius:10px;border-left:4px solid #0ea5e9;margin-top:1.5rem;">
        <strong><i class="fas fa-lightbulb" style="color:#0ea5e9;margin-right:0.5rem;"></i>Insight Komparatif:</strong> Keunggulan unik arsitektur yang diajukan terletak pada kombinasi <strong>manajemen konteks S-P-O yang efisien</strong> (tidak ditemukan di riset lain dalam bentuk ini) dengan <strong>integrasi native platform otomasi n8n</strong> dan output JSON deterministik yang memungkinkan branching logis otomatis.
      </div>
    </div>

    <!-- 11 — Testing -->
    <h2 class="section-title">11 — Hasil Testing: 112 Pertanyaan Nyata</h2>
    <div class="conclusion-box limitations-box">
      <h3><i class="fas fa-flask"></i> Hasil Data Testing</h3>
      <p>Arsitektur ini diuji menggunakan <strong>112 pertanyaan nyata</strong> dari calon santri, wali santri, dan pengguna aktif sistem — bukan data buatan. Router mengarahkan ke sub-agent yang tepat di setiap kasus.</p>
      <div class="token-stats" style="margin:1.5rem 0;">
        <div class="token-stat"><div class="number">112</div><div class="label">Total Pertanyaan Diuji</div></div>
        <div class="token-stat"><div class="number">8</div><div class="label">Domain Sub-Agent Aktif</div></div>
        <div class="token-stat"><div class="number blue">100%</div><div class="label">Akurasi Routing ke Domain yang Tepat</div></div>
        <div class="token-stat"><div class="number amber">~10</div><div class="label">Token Konteks S-P-O per Giliran</div></div>
      </div>
      <div style="overflow-x:auto;margin:1rem 0;">
        <table class="styled-table">
          <thead><tr><th>Domain Sub-Agent</th><th>Jumlah Pertanyaan</th><th>Proporsi</th><th>Contoh Input Nyata</th></tr></thead>
          <tbody>
            <tr><td><strong>pendaftaran</strong></td><td>62</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:var(--secondary);height:10px;border-radius:4px;width:55%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">55%</span></td><td style="font-size:0.85rem;font-style:italic;">"sarat pendaftaran apa aja min"</td></tr>
            <tr><td><strong>kependidikan</strong></td><td>16</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#4f46e5;height:10px;border-radius:4px;width:14%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">14%</span></td><td style="font-size:0.85rem;font-style:italic;">"Program tahfidz ny gimana ustadz 🙏🏻"</td></tr>
            <tr><td><strong>alamat</strong></td><td>9</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#0ea5e9;height:10px;border-radius:4px;width:8%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">8%</span></td><td style="font-size:0.85rem;font-style:italic;">"lokasi mahad dimana ustadz"</td></tr>
            <tr><td><strong>kesantrian</strong></td><td>8</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#dc2626;height:10px;border-radius:4px;width:7%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">7%</span></td><td style="font-size:0.85rem;font-style:italic;">"Ustadz Afwan, ana udah di Ma'had..."</td></tr>
            <tr><td><strong>basa_basi</strong></td><td>7</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#f59e0b;height:10px;border-radius:4px;width:6%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">6%</span></td><td style="font-size:0.85rem;font-style:italic;">"Assalamualaikum, izin bertanya ustadz"</td></td>
            <tr><td><strong>humas_dakwah</strong></td><td>5</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#7c3aed;height:10px;border-radius:4px;width:4.5%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">5%</span></td><td style="font-size:0.85rem;font-style:italic;">"fasilitas mahad apa aja min"</td></tr>
            <tr><td><strong>it</strong></td><td>5</td><td><div style="background:#e2e8f0;border-radius:4px;height:10px;width:100%;"><div style="background:#10b981;height:10px;border-radius:4px;width:4.5%;"></div></div><span style="font-size:0.8rem;color:var(--text-light);">5%</span></td><td style="font-size:0.85rem;font-style:italic;">"Hp di riset lupa akun dan pasword nya"</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Showcase (disingkat) -->
    <div class="conclusion-box findings-box">
      <h3><i class="fas fa-vial"></i> Showcase: Pertanyaan Nyata & Hasil Routing</h3>
      <div style="margin:1.5rem 0;">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.8rem;"><span style="background:rgba(3,159,91,0.15);color:var(--secondary);padding:0.2rem 0.7rem;border-radius:50px;font-size:0.8rem;font-weight:600;">KASUS 1 — Typo & Bahasa Informal</span></div>
        <div class="code-block">
          <span class="comment">// Typo "sarat" → "syarat", tanpa kapital</span><br>
          <span class="str">"sarat pendaftaran apa aja min"</span><br><br>
          {<br>&nbsp;&nbsp;<span class="key">"agent"</span>: <span class="val">"pendaftaran"</span>,<br>&nbsp;&nbsp;<span class="key">"query"</span>: <span class="str">"syarat pendaftaran Ma'had Al Yusro"</span>,<br>&nbsp;&nbsp;<span class="key">"konteks"</span>: <span class="str">"User menanyakan syarat pendaftaran untuk Ma'had Al Yusro"</span>,<br>&nbsp;&nbsp;<span class="key">"confidence"</span>: <span class="val">0.82</span><br>}
        </div>
      </div>
      <div style="margin:1.5rem 0;">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.8rem;"><span style="background:rgba(14,165,233,0.15);color:#0ea5e9;padding:0.2rem 0.7rem;border-radius:50px;font-size:0.8rem;font-weight:600;">KASUS 2 — Warisan Konteks S-P-O</span></div>
        <div class="code-block">
          <span class="comment">// Giliran 2 — "biayanya berapa" (ambigu total)</span><br>
          <span class="comment">// Router mewarisi entitas "SMP" dari konteks sebelumnya</span><br>
          {<br>&nbsp;&nbsp;<span class="key">"agent"</span>: <span class="val">"pendaftaran"</span>,<br>&nbsp;&nbsp;<span class="key">"query"</span>: <span class="str">"biaya pendaftaran jenjang SMP Ma'had Al Yusro"</span><br>}
        </div>
        <p style="font-size:0.9rem;color:var(--text-light);margin-top:0.5rem;"><i class="fas fa-check-circle" style="color:var(--secondary);"></i> Selaras dengan temuan Axetorn 2026: memory management yang efisien adalah fondasi kepercayaan pengguna.</p>
      </div>
    </div>

    <!-- 12 Kesimpulan -->
    <h2 class="section-title">12 — Kesimpulan</h2>
    <div class="conclusion-box background-box">
      <h3><i class="fas fa-crosshairs"></i> Fokus Utama: Dua Masalah Fundamental Agent</h3>
      <p>Sistem ini dirancang dengan satu prioritas: <strong>akurasi</strong>. Dua masalah paling umum dalam sistem AI berbasis agent diselesaikan secara bersamaan — dan didukung oleh konsensus tiga riset ilmiah global.</p>
      <div style="margin:2rem 0;">
        <div style="display:flex;align-items:center;gap:0.8rem;margin-bottom:1rem;">
          <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;flex-shrink:0;">1</div>
          <h4 style="color:#4f46e5;font-size:1.1rem;margin:0;">Masalah Konteks: Histori Percakapan yang Membengkak</h4>
        </div>
        <div class="two-column-grid">
          <div style="background:rgba(220,38,38,0.05);padding:1.2rem;border-radius:10px;border-left:4px solid #dc2626;">
            <p style="font-weight:600;color:#dc2626;margin-bottom:0.6rem;font-size:0.9rem;"><i class="fas fa-exclamation-triangle"></i> Masalah Konvensional</p>
            <p style="font-size:0.9rem;color:var(--text-light);">Seluruh histori dikirim ulang ke setiap API call. Semakin panjang percakapan, semakin besar token yang dikonsumsi — diakui semua riset sebagai masalah utama sistem monolitik.</p>
          </div>
          <div style="background:rgba(3,159,91,0.05);padding:1.2rem;border-radius:10px;border-left:4px solid var(--secondary);">
            <p style="font-weight:600;color:var(--secondary);margin-bottom:0.6rem;font-size:0.9rem;"><i class="fas fa-check-circle"></i> Solusi S-P-O</p>
            <p style="font-size:0.9rem;color:var(--text-light);">Satu kalimat konteks ~10 token menggantikan ratusan token histori. Token yang diwariskan tetap flat berapapun panjang percakapan — pendekatan unik yang tidak ditemukan di riset pembanding.</p>
          </div>
        </div>
      </div>
      <div style="margin:2rem 0 0;">
        <div style="display:flex;align-items:center;gap:0.8rem;margin-bottom:1rem;">
          <div style="background:linear-gradient(135deg,#ea580c,#f97316);color:white;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;flex-shrink:0;">2</div>
          <h4 style="color:#ea580c;font-size:1.1rem;margin:0;">Masalah Informasi: Satu Agent Menyimpan Segalanya</h4>
        </div>
        <div class="two-column-grid">
          <div style="background:rgba(220,38,38,0.05);padding:1.2rem;border-radius:10px;border-left:4px solid #dc2626;">
            <p style="font-weight:600;color:#dc2626;margin-bottom:0.6rem;font-size:0.9rem;"><i class="fas fa-exclamation-triangle"></i> Masalah Konvensional</p>
            <p style="font-size:0.9rem;color:var(--text-light);">Semua domain dalam satu prompt/RAG. Setiap panggilan membebani model dengan informasi yang tidak relevan — terbukti menurunkan akurasi di semua riset pembanding.</p>
          </div>
          <div style="background:rgba(3,159,91,0.05);padding:1.2rem;border-radius:10px;border-left:4px solid var(--secondary);">
            <p style="font-weight:600;color:var(--secondary);margin-bottom:0.6rem;font-size:0.9rem;"><i class="fas fa-check-circle"></i> Solusi Sub-Agent Spesialis</p>
            <p style="font-size:0.9rem;color:var(--text-light);">Setiap sub-agent hanya menyimpan informasi domain-nya. 100% informasi yang diterima agent relevan dengan pertanyaan — selaras dengan prinsip yang dibuktikan CommunityBots, Axetorn, dan ChatMap.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="conclusion-box">
      <h3><i class="fas fa-check-circle"></i> Perubahan Paradigma, Bukan Sekadar Optimasi</h3>
      <ul class="enhanced-list">
        <li><strong>Akurasi sebagai Prioritas Utama:</strong> Routing yang tepat memastikan setiap pertanyaan dijawab oleh sub-agent yang memang ahli di domain tersebut — dikonfirmasi Axetorn 2026 sebagai faktor kepercayaan #1.</li>
        <li><strong>Konteks Ringkas, Bukan Histori Panjang:</strong> Masalah klasik agent diselesaikan dengan konteks S-P-O. Memori menjadi efisien tanpa kehilangan kemampuan memahami percakapan multi-giliran.</li>
        <li><strong>Informasi Tidak Mubazir Jadi Token:</strong> Setiap sub-agent hanya menerima data yang ia butuhkan — prinsip yang sama dibuktikan ChatMap (Alibaba) meningkatkan kepuasan pelanggan di Taobao.</li>
        <li><strong>Output Deterministik:</strong> JSON berskema memungkinkan integrasi andal dengan n8n — keunggulan yang tidak dimiliki sistem CommunityBots atau ChatMap secara native.</li>
        <li><strong>Skalabilitas Horizontal:</strong> Menambah domain baru = menambah satu sub-agent. Dikonfirmasi secara empiris oleh seluruh riset pembanding.</li>
      </ul>
      <div class="highlight-quote">
        <p>"Sistem yang baik bukan yang mencoba menjawab segalanya, melainkan yang tahu persis kepada siapa harus bertanya — dan hanya membawa bekal yang memang dibutuhkan untuk perjalanan itu."</p>
      </div>
      <div class="two-column-grid" style="margin-top:1.5rem;">
        <div style="background:rgba(3,159,91,0.05);padding:1.2rem;border-radius:10px;">
          <h4 style="color:var(--secondary);margin-bottom:0.8rem;"><i class="fas fa-tools"></i> Untuk Praktisi & Developer</h4>
          <ul style="padding-left:1.5rem;font-size:0.9rem;color:var(--text-light);">
            <li>Gunakan arsitektur ini untuk sistem dengan 3+ domain berbeda</li>
            <li>Isi setiap sub-agent hanya dengan data domain-nya — jangan campur</li>
            <li>Definisikan konteks S-P-O dengan entitas yang cukup spesifik</li>
            <li>Buat test case per domain secara independen</li>
          </ul>
        </div>
        <div style="background:rgba(14,165,233,0.05);padding:1.2rem;border-radius:10px;">
          <h4 style="color:#0ea5e9;margin-bottom:0.8rem;"><i class="fas fa-lightbulb"></i> Didukung Riset Akademik</h4>
          <ul style="padding-left:1.5rem;font-size:0.9rem;color:var(--text-light);">
            <li>Axetorn 2026: reliabilitas via pemisahan peran agent</li>
            <li>CommunityBots 2023: spesialisasi domain meningkatkan kualitas respons</li>
            <li>ChatMap 2025: isolasi knowledge per-agent = performa produksi nyata</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Metadata -->
    <div style="text-align:center;margin-top:3rem;padding:1.5rem;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-radius:14px;">
      <h3 style="color:var(--primary);margin-bottom:1rem;"><i class="fas fa-file-alt"></i> Metadata Dokumen</h3>
      <div style="display:flex;justify-content:center;gap:1.5rem;flex-wrap:wrap;font-size:0.9rem;color:var(--text-light);">
        <div><strong><i class="fas fa-tag"></i> Topik:</strong> Multi-Agent System Design</div>
        <div><strong><i class="fas fa-calendar"></i> Tahun:</strong> 2026</div>
        <div><strong><i class="fas fa-tools"></i> Platform:</strong> n8n Workflow Automation</div>
        <div><strong><i class="fas fa-book"></i> Referensi:</strong> 3 Jurnal Peer-Reviewed</div>
      </div>
      <p style="margin-top:1.5rem;font-size:0.85rem;color:#64748b;max-width:800px;margin-left:auto;margin-right:auto;">
        <i class="fas fa-info-circle"></i>
        Artikel ini disusun berdasarkan proyek eksperimen menggunakan platform n8n, diperkuat dengan perbandingan dari jurnal Requirements Engineering (2026), ACM CSCW (2023), dan ACL Findings (2025).
      </p>
    </div>
  </div>

  <footer>
    <img src="/nuscode - white.png" alt="Logo" />
    <p>© <span id="footerYear">2025</span> — Teknologi dari Nusantara, untuk Dunia.</p>
    <p style="margin-top:0.5rem;max-width:600px;margin-left:auto;margin-right:auto;">
      Berdedikasi untuk memajukan literasi teknologi melalui penelitian, inovasi, dan kolaborasi dalam ekosistem digital Indonesia.
    </p>
    <div class="footer-tags">
      <span class="tag">Agent Router Architecture</span>
      <span class="tag">Multi-Agent System</span>
      <span class="tag">n8n Workflow</span>
      <span class="tag">LLM Orchestration</span>
      <span class="tag">Token Efficiency</span>
      <span class="tag">Peer-Reviewed Research</span>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const menuToggle = document.querySelector('.menu-toggle');
      const mainNav = document.querySelector('header nav');
      if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function (e) {
          e.stopPropagation();
          mainNav.classList.toggle('active');
          const icon = menuToggle.querySelector('i');
          icon.classList.contains('fa-bars') ? icon.classList.replace('fa-bars','fa-times') : icon.classList.replace('fa-times','fa-bars');
        });
        mainNav.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', () => {
            mainNav.classList.remove('active');
            menuToggle.querySelector('i').classList.replace('fa-times','fa-bars');
          });
        });
        document.addEventListener('click', function (e) {
          if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) icon.classList.replace('fa-times','fa-bars');
          }
        });
        window.addEventListener('resize', function () {
          if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) icon.classList.replace('fa-times','fa-bars');
          }
        });
      }

      const boxes = document.querySelectorAll('.conclusion-box, .method-card, .domain-card, .token-stat');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.transition = 'all 0.6s cubic-bezier(0.22,0.61,0.36,1)';
          }
        });
      }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
      boxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(30px)';
        observer.observe(box);
      });

      document.querySelectorAll('.conclusion-box').forEach(box => {
        box.addEventListener('click', function () {
          this.style.transform = 'translateY(-8px)';
          setTimeout(() => { this.style.transform = 'translateY(-5px)'; }, 150);
        });
      });

      document.getElementById('footerYear').textContent = new Date().getFullYear();
    });
  </script>
</body>
</html>