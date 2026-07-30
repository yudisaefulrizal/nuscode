<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NUSCODE – Review Jurnal: Dampak Cyber-Attacks pada Digital Twin</title>
  <meta name="description" content="Analisis mendalam tentang dampak serangan siber terhadap kinerja organisasi berbasis Digital Twin menggunakan pendekatan DTMC dan Dynamic Bayesian Network." />
  <meta name="keywords" content="NUSCODE, digital twin, cyber attack, keamanan siber, DTMC, dynamic bayesian network, review jurnal, industri 4.0" />
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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f8fafc;
      color: var(--text-dark);
      scroll-behavior: smooth;
      overflow-x: hidden;
      line-height: 1.7;
    }

    /* HEADER */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.2rem 5%;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      flex-wrap: wrap;
    }

    header img.logo {
      height: 45px;
      width: auto;
      transition: transform 0.3s ease;
    }

    header img.logo:hover {
      transform: scale(1.05);
    }

    /* Mobile menu button */
    .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--primary);
      cursor: pointer;
      z-index: 1001;
    }

    header nav {
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
    }

    header nav a {
      text-decoration: none;
      color: var(--text-dark);
      margin-left: 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.5rem 0;
      position: relative;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    header nav a:hover {
      color: var(--secondary);
    }

    header nav a::after {
      content: "";
      position: absolute;
      width: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--secondary), var(--accent));
      bottom: 0;
      left: 0;
      transition: width 0.4s cubic-bezier(0.22, 0.61, 0.36, 1);
      border-radius: 2px;
    }

    header nav a:hover::after {
      width: 100%;
    }

    /* ARTICLE HEADER */
    .article-header {
      background: linear-gradient(135deg, #1a1a1a, #2d3748);
      color: white;
      padding: 4rem 5% 3rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .article-header::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(3,159,91,0.1)"/></svg>');
      background-size: cover;
    }

    .article-header h1 {
      font-size: 2.2rem;
      margin-bottom: 1.2rem;
      font-weight: 800;
      line-height: 1.3;
      position: relative;
      z-index: 1;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .article-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      margin-bottom: 1.5rem;
      position: relative;
      z-index: 1;
      font-weight: 400;
      line-height: 1.6;
    }

    .meta {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
      position: relative;
      z-index: 1;
    }

    .meta-item {
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(10px);
      padding: 0.6rem 1.2rem;
      border-radius: 50px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      border: 1px solid rgba(255,255,255,0.2);
      transition: all 0.3s ease;
      flex-wrap: wrap;
      justify-content: center;
    }

    .meta-item:hover {
      background: rgba(255,255,255,0.25);
      transform: translateY(-2px);
    }

    /* ARTICLE CONTENT */
    .article-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 3rem 5% 4rem;
    }

    .section-title {
      color: var(--primary);
      font-size: 1.7rem;
      margin: 3rem 0 1.5rem;
      padding-bottom: 0.8rem;
      position: relative;
      font-weight: 700;
    }

    .section-title::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, var(--secondary), var(--accent));
      border-radius: 2px;
    }

    /* ENHANCED CONCLUSION BOXES */
    .conclusion-box {
      background: white;
      border-radius: 14px;
      padding: 2rem;
      margin: 2rem 0;
      box-shadow: var(--box-shadow);
      border: 1px solid rgba(0,0,0,0.05);
      position: relative;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .conclusion-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    .conclusion-box::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 100%;
      background: linear-gradient(180deg, var(--secondary), var(--accent));
    }

    .conclusion-box h3 {
      color: var(--primary);
      font-size: 1.3rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    .conclusion-box h3 i {
      color: var(--secondary);
      background: rgba(3,159,91,0.1);
      padding: 0.5rem;
      border-radius: 8px;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .conclusion-box p {
      color: var(--text-light);
      margin-bottom: 1rem;
      font-size: 1rem;
      line-height: 1.7;
    }

    /* Different colors for different types of boxes */
    .background-box {
      border-left: 5px solid #4f46e5;
    }

    .background-box::before {
      background: linear-gradient(180deg, #4f46e5, #7c3aed);
    }

    .objective-box {
      border-left: 5px solid #059669;
    }

    .objective-box::before {
      background: linear-gradient(180deg, #059669, #10b981);
    }

    .methodology-box {
      border-left: 5px solid #dc2626;
    }

    .methodology-box::before {
      background: linear-gradient(180deg, #dc2626, #ef4444);
    }

    .data-box {
      border-left: 5px solid #ea580c;
    }

    .data-box::before {
      background: linear-gradient(180deg, #ea580c, #f97316);
    }

    .findings-box {
      border-left: 5px solid #7c3aed;
    }

    .findings-box::before {
      background: linear-gradient(180deg, #7c3aed, #8b5cf6);
    }

    .limitations-box {
      border-left: 5px solid #64748b;
    }

    .limitations-box::before {
      background: linear-gradient(180deg, #64748b, #94a3b8);
    }

    .insights-box {
      border-left: 5px solid #0ea5e9;
    }

    .insights-box::before {
      background: linear-gradient(180deg, #0ea5e9, #38bdf8);
    }

    /* Enhanced list styles */
    .enhanced-list {
      list-style: none;
      padding-left: 0;
      margin: 1.5rem 0;
    }

    .enhanced-list li {
      padding: 0.7rem 0.8rem 0.7rem 2.5rem;
      margin-bottom: 0.7rem;
      background: rgba(248, 250, 252, 0.8);
      border-radius: 8px;
      position: relative;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .enhanced-list li:hover {
      background: rgba(3,159,91,0.05);
      border-left: 3px solid var(--secondary);
      transform: translateX(3px);
    }

    .enhanced-list li::before {
      content: "✓";
      position: absolute;
      left: 0.8rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--secondary);
      font-weight: bold;
      font-size: 1rem;
      background: rgba(3,159,91,0.1);
      width: 24px;
      height: 24px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Methodology grid */
    .methodology-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .method-card {
      background: white;
      padding: 1.5rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.07);
      border: 1px solid rgba(0,0,0,0.04);
      transition: all 0.3s ease;
    }

    .method-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .method-card h4 {
      color: var(--primary);
      font-size: 1.2rem;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      flex-wrap: wrap;
    }

    .method-card h4 i {
      color: var(--secondary);
      font-size: 1.3rem;
      flex-shrink: 0;
    }

    /* Table styling */
    .styled-table {
      width: 100%;
      border-collapse: collapse;
      margin: 2rem 0;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      font-size: 0.9rem;
    }

    .styled-table th {
      background: linear-gradient(135deg, var(--primary), #2d3748);
      color: white;
      padding: 1rem;
      text-align: left;
      font-weight: 600;
    }

    .styled-table td {
      padding: 1rem;
      border-bottom: 1px solid #f1f5f9;
      color: var(--text-light);
    }

    .styled-table tr:last-child td {
      border-bottom: none;
    }

    .styled-table tr:hover {
      background: rgba(3,159,91,0.03);
    }

    .styled-table tr:nth-child(even) {
      background: #f8fafc;
    }

    /* Quote styling */
    .highlight-quote {
      background: linear-gradient(135deg, #f0f9ff, #fef7ff);
      border-radius: 14px;
      padding: 2rem;
      margin: 2.5rem 0;
      position: relative;
      border-left: 5px solid var(--accent);
    }

    .highlight-quote::before {
      content: "❝";
      position: absolute;
      top: -15px;
      left: 1.5rem;
      font-size: 3rem;
      color: rgba(255, 204, 51, 0.2);
      font-family: serif;
    }

    .highlight-quote p {
      font-size: 1.1rem;
      color: var(--text-dark);
      font-style: italic;
      line-height: 1.7;
      position: relative;
      z-index: 1;
    }

    /* FOOTER */
    footer {
      background: linear-gradient(135deg, #0d1b2a, #1b263b);
      color: #e2e8f0;
      padding: 3rem 5% 2rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    footer::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    }

    footer img {
      height: 45px;
      margin-bottom: 1.2rem;
      filter: brightness(0) invert(1);
      opacity: 0.9;
      width: auto;
    }

    footer p {
      font-size: 0.9rem;
      opacity: 0.8;
      line-height: 1.6;
      margin: 0.5rem 0;
    }

    .footer-tags {
      display: flex;
      justify-content: center;
      gap: 0.8rem;
      flex-wrap: wrap;
      margin-top: 1.5rem;
    }

    .tag {
      background: rgba(255,255,255,0.1);
      padding: 0.4rem 1rem;
      border-radius: 50px;
      font-size: 0.8rem;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
      transition: all 0.3s ease;
    }

    .tag:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
      .article-header h1 {
        font-size: 2rem;
      }
      
      .article-content {
        padding: 2.5rem 5% 3rem;
      }
    }

    @media (max-width: 768px) {
      header {
        padding: 1rem 4%;
      }
      
      .menu-toggle {
        display: block;
      }
      
      header nav {
        display: none;
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        width: 100%;
        flex-direction: column;
        align-items: center;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        padding: 1.5rem 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        z-index: 999;
      }
      
      header nav.active {
        display: flex;
      }
      
      header nav a {
        margin: 0.7rem 0;
        margin-left: 0;
        width: 90%;
        padding: 0.8rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        text-align: center;
        font-size: 1rem;
      }
      
      header nav a:last-child {
        border-bottom: none;
      }
      
      .article-header {
        padding: 3rem 4% 2rem;
      }
      
      .article-header h1 {
        font-size: 1.8rem;
        line-height: 1.4;
      }
      
      .article-subtitle {
        font-size: 1rem;
      }
      
      .meta-item {
        width: 100%;
        justify-content: center;
        text-align: center;
        padding: 0.5rem 1rem;
      }
      
      .section-title {
        font-size: 1.5rem;
        margin: 2.5rem 0 1.2rem;
      }
      
      .conclusion-box {
        padding: 1.5rem;
      }
      
      .methodology-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
      }
      
      .method-card {
        padding: 1.2rem;
      }
      
      .highlight-quote {
        padding: 1.5rem;
      }
      
      .styled-table {
        font-size: 0.85rem;
      }
      
      .styled-table th,
      .styled-table td {
        padding: 0.8rem;
      }
      
      /* Two-column layouts to one column on mobile */
      .two-column-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
      }
    }

    @media (max-width: 480px) {
      .article-header h1 {
        font-size: 1.6rem;
      }
      
      .article-content {
        padding: 2rem 4% 2.5rem;
      }
      
      .conclusion-box h3 {
        font-size: 1.2rem;
      }
      
      .conclusion-box h3 i {
        font-size: 1rem;
        padding: 0.4rem;
      }
      
      .enhanced-list li {
        padding: 0.6rem 0.7rem 0.6rem 2.2rem;
        font-size: 0.95rem;
      }
      
      .meta {
        gap: 0.7rem;
      }
      
      .meta-item {
        font-size: 0.85rem;
      }
      
      footer {
        padding: 2.5rem 4% 2rem;
      }
      
      .footer-tags {
        gap: 0.6rem;
      }
      
      .tag {
        font-size: 0.75rem;
        padding: 0.3rem 0.8rem;
      }
      
      header nav {
        top: 65px;
      }
    }

    /* Utility classes for responsive layouts */
    .two-column-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    @media (max-width: 768px) {
      .two-column-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-in {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Text utility classes */
    .text-primary { color: var(--primary); }
    .text-warning { color: var(--accent); }
    .text-danger { color: #dc2626; }
    .text-dark { color: var(--text-dark); }
  </style>
</head>
<body>

  <header>
    <img src="/nuscode.png" alt="NUSCODE Logo" class="logo" />
    <button class="menu-toggle" id="menuToggle">
      <i class="fas fa-bars"></i>
    </button>
    <nav id="mainNav">
      <a href="/">Home</a>
      <a href="/artikel/osi-layers">OSI Layers</a>
      <a href="/review-jurnal">Review Jurnal Apps</a>
      <a href="/chatbot"><i class="fas fa-robot"></i> Chatbot</a>
    </nav>
  </header>

  <div class="article-header">
    <h1>Analyzing the Impact of Cyber-Attacks on Digital Twin-Based Industrial Organizations</h1>
    <p class="article-subtitle">Sebuah Analisis Komprehensif Menggunakan Pendekatan DTMC dan Dynamic Bayesian Network</p>
    <div class="meta">
      <div class="meta-item">
        <i class="fas fa-tags"></i>
        <span>Digital Twin • Cyber Security • Industry 4.0 • AI Security</span>
      </div>
    </div>
  </div>

  <div class="article-content">
    
    <h2 class="section-title">Latar Belakang Penelitian</h2>
    <div class="conclusion-box background-box">
      <h3><i class="fas fa-map-marker-alt"></i> Konteks dan Permasalahan</h3>
      <p>Digital Twin (DT) merepresentasikan terobosan teknologi dalam transformasi industri, berfungsi sebagai replika virtual dari entitas fisik untuk memantau, menganalisis, dan mengoptimalkan sistem industri secara real-time. Namun, integrasi DT dengan sistem kontrol industri dan ekosistem internet justru memperluas permukaan serangan (attack surface) terhadap ancaman siber.</p>
      
      <p><strong>Risiko Utama yang Dihadapi:</strong></p>
      <ul class="enhanced-list">
        <li><strong>Kebocoran Data Sensitif:</strong> Informasi proprietary dan operasional rentan terekspos</li>
        <li><strong>Gangguan Operasional:</strong> Disrupsi pada proses produksi dan supply chain</li>
        <li><strong>Kerugian Finansial:</strong> Biaya pemulihan dan downtime yang signifikan</li>
        <li><strong>Penurunan Kinerja TI:</strong> Degradasi kapabilitas sistem secara bertahap</li>
        <li><strong>Kerusakan Reputasi:</strong> Hilangnya kepercayaan stakeholder dan pelanggan</li>
      </ul>
      
      <p>Penelitian sebelumnya cenderung bersifat statis dalam menganalisis dampak serangan siber. Studi ini hadir untuk mengisi celah tersebut dengan pendekatan dinamis yang mempertimbangkan faktor waktu sebagai variabel kritis.</p>
    </div>

    <h2 class="section-title">Tujuan dan Fokus Penelitian</h2>
    <div class="conclusion-box objective-box">
      <h3><i class="fas fa-bullseye"></i> Sasaran Strategis</h3>
      <p>Penelitian ini dirancang dengan tiga tujuan utama yang saling terkait untuk memberikan pemahaman holistik tentang dampak serangan siber pada ekosistem Digital Twin:</p>
      
      <ul class="enhanced-list">
        <li>
          <strong>Analisis Evolusi Kinerja TI:</strong> 
          Melacak perubahan kinerja sistem TI organisasi berbasis DT dalam kondisi dinamis selama serangan siber berlangsung, dari onset hingga resolusi.
        </li>
        <li>
          <strong>Penilaian Dampak Finansial:</strong> 
          Mengkuantifikasi kerugian ekonomi yang timbul akibat berbagai jenis serangan siber, termasuk biaya langsung dan tidak langsung.
        </li>
        <li>
          <strong>Evaluasi Mekanisme Pencegahan:</strong> 
          Mengukur efektivitas mekanisme keamanan yang diterapkan dalam meningkatkan keandalan (reliability) dan ketahanan (resilience) sistem TI.
        </li>
      </ul>
      
      <p><em>Pendekatan ini memungkinkan organisasi untuk tidak hanya bereaksi terhadap insiden, tetapi juga memprediksi dan mempersiapkan diri terhadap ancaman potensial.</em></p>
    </div>

    <h2 class="section-title">Metodologi Penelitian</h2>
    
    <div class="methodology-grid">
      <div class="method-card">
        <h4><i class="fas fa-project-diagram"></i> Discrete-Time Markov Chain (DTMC)</h4>
        <p><strong>Fungsi Utama:</strong> Pemodelan perubahan state-based dari kondisi kinerja sistem TI selama serangan siber</p>
        
        <p><strong>Arsitektur Status Sistem:</strong></p>
        <ul style="padding-left: 1.5rem; margin: 1rem 0;">
          <li><strong class="text-primary">Fully Functional</strong> (100% kapasitas operasional)</li>
          <li><strong class="text-warning">Partially Functional</strong> (50% degradasi kinerja)</li>
          <li><strong class="text-danger">Barely Functional</strong> (75% degradasi kinerja)</li>
          <li><strong class="text-dark">Non-Functional</strong> (0% kapasitas operasional)</li>
        </ul>
        
        <p><strong>Mekanisme Kerja:</strong> DTMC menghitung probabilitas transisi antar status menggunakan matriks Markov, memungkinkan prediksi keadaan sistem pada waktu t+n berdasarkan keadaan pada waktu t.</p>
      </div>
      
      <div class="method-card">
        <h4><i class="fas fa-network-wired"></i> Dynamic Bayesian Network (DBN)</h4>
        <p><strong>Fungsi Utama:</strong> Simulasi dinamika penyebaran serangan siber dan dampaknya terhadap kinerja TI secara temporal</p>
        
        <p><strong>Analogi Intuitif:</strong> Seperti sistem prediksi cuaca canggih yang tidak hanya melihat kondisi hari ini, tetapi juga pola historis untuk memprediksi cuaca esok dengan akurasi tinggi.</p>
        
        <p><strong>Keunggulan Krusial:</strong></p>
        <ul style="padding-left: 1.5rem; margin: 1rem 0;">
          <li>Multi-time slice analysis (granularitas menit ke menit)</li>
          <li>Probabilistic forecasting dengan confidence interval</li>
          <li>Real-time adaptability dengan data streaming</li>
          <li>Integrated diagnostic dan predictive capabilities</li>
        </ul>
      </div>
    </div>

    <div class="conclusion-box methodology-box">
      <h3><i class="fas fa-sitemap"></i> Alur Penelitian Terintegrasi</h3>
      
      <div style="overflow-x: auto;">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Tahap</th>
              <th>Proses</th>
              <th>Output</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>1</strong></td>
              <td>Initial State Prediction menggunakan DTMC</td>
              <td>Baseline kondisi sistem</td>
            </tr>
            <tr>
              <td><strong>2</strong></td>
              <td>Integration DTMC-DBN untuk analisis dinamis</td>
              <td>Model prediktif temporal</td>
            </tr>
            <tr>
              <td><strong>3</strong></td>
              <td>Case Studies: Malware vs DDoS attacks</td>
              <td>Comparative attack profiles</td>
            </tr>
            <tr>
              <td><strong>4</strong></td>
              <td>Work Level & Total Expected Utility analysis</td>
              <td>Operational impact metrics</td>
            </tr>
            <tr>
              <td><strong>5</strong></td>
              <td>Reliability assessment dengan/sans countermeasures</td>
              <td>Security effectiveness metrics</td>
            </tr>
            <tr>
              <td><strong>6</strong></td>
              <td>Resilience capability evaluation menggunakan resilience triangle</td>
              <td>Organizational resilience index</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <p><strong>Visualisasi DBN dalam Konteks Serangan:</strong></p>
      <div style="background: #f8fafc; padding: 1.2rem; border-radius: 10px; margin: 1.5rem 0; font-family: monospace; border-left: 4px solid #7c3aed; font-size: 0.9rem; overflow-x: auto;">
        <div>Waktu t=0: [Attack Vector] → [TI Performance] → [Business Impact]</div>
        <div>Waktu t=1: [Attack Vector] → [TI Performance] → [Business Impact]</div>
        <div>Waktu t=2: [Attack Vector] → [TI Performance] → [Business Impact]</div>
        <div style="color: #7c3aed; margin-top: 0.5rem;">↑ Temporal dependencies across time slices</div>
      </div>
    </div>

    <h2 class="section-title">Temuan dan Analisis Data</h2>
    <div class="conclusion-box findings-box">
      <h3><i class="fas fa-chart-line"></i> Hasil Simulasi dan Interpretasi</h3>
      
      <p><strong>Data Input dan Parameter Simulasi:</strong></p>
      <ul class="enhanced-list">
        <li><strong>Data Historis:</strong> Probabilitas transisi dari insiden keamanan sebelumnya</li>
        <li><strong>Expert Opinion:</strong> Penilaian ahli keamanan siber industri</li>
        <li><strong>Time Frame:</strong> Simulasi dari t=0 hingga t=5 (6 interval waktu)</li>
        <li><strong>Countermeasures Tested:</strong> Next-gen firewall dan secure IoT infrastructure</li>
      </ul>
      
      <p><strong>Comparative Attack Impact Analysis:</strong></p>
      
      <div class="two-column-grid">
        <div style="background: rgba(239, 68, 68, 0.05); padding: 1.2rem; border-radius: 10px; border-left: 4px solid #ef4444;">
          <h4 style="color: #dc2626; margin-bottom: 0.8rem;"><i class="fas fa-virus"></i> Kasus Malware</h4>
          <ul style="padding-left: 1.5rem;">
            <li>t=0: 32% fully functional</li>
            <li>t=1: 26% fully functional</li>
            <li>t=2: 21% fully functional</li>
            <li>t=3: <strong>17% fully functional</strong></li>
          </ul>
          <p style="margin-top: 0.8rem; color: #dc2626; font-size: 0.9rem;"><strong>Degradasi Bertahap:</strong> 15% penurunan dalam 3 interval waktu</p>
        </div>
        
        <div style="background: rgba(139, 92, 246, 0.05); padding: 1.2rem; border-radius: 10px; border-left: 4px solid #8b5cf6;">
          <h4 style="color: #7c3aed; margin-bottom: 0.8rem;"><i class="fas fa-broadcast-tower"></i> Kasus DDoS</h4>
          <ul style="padding-left: 1.5rem;">
            <li>t=0: 24% fully functional</li>
            <li>t=1: 20% fully functional</li>
            <li>t=2: 17% fully functional</li>
            <li>t=3: 15% fully functional</li>
            <li>t=5: <strong>14% fully functional</strong></li>
          </ul>
          <p style="margin-top: 0.8rem; color: #7c3aed; font-size: 0.9rem;"><strong>Degradasi Ekstrem:</strong> 10% penurunan dalam 5 interval waktu</p>
        </div>
      </div>
      
      <p><strong>Key Insight:</strong> DDoS attack menunjukkan karakteristik yang lebih merusak dibandingkan malware, dengan dampak yang lebih langsung dan sulit dipulihkan. Hal ini disebabkan oleh sifat serangan DDoS yang mengganggu ketersediaan (availability) sistem secara masif.</p>
    </div>

    <h2 class="section-title">Kesimpulan Utama</h2>
    <div class="conclusion-box">
      <h3><i class="fas fa-check-circle"></i> Implikasi Strategis dan Rekomendasi</h3>
      
      <ul class="enhanced-list">
        <li>
          <strong>Efektivitas Model DTMC-DBN:</strong> Pendekatan integratif terbukti sangat efektif dalam memodelkan dinamika serangan siber pada organisasi berbasis Digital Twin, memberikan granularitas temporal yang sebelumnya tidak tersedia.
        </li>
        <li>
          <strong>Hierarki Ancaman:</strong> DDoS attack menempati posisi paling kritis dalam matriks ancaman, dengan dampak yang lebih signifikan terhadap kinerja TI, keandalan sistem, dan aspek finansial organisasi.
        </li>
        <li>
          <strong>Value of Countermeasures:</strong> Implementasi mekanisme pencegahan yang kuat (firewall dan secure IoT infrastructure) meningkatkan keandalan sistem hingga 45% dan ketahanan organisasi hingga 60% dalam skenario serangan.
        </li>
        <li>
          <strong>Decision Support Framework:</strong> Temuan penelitian menyediakan framework berbasis data untuk pengambilan keputusan strategis dalam identifikasi ancaman, prioritisasi mitigasi, dan alokasi sumber daya keamanan.
        </li>
      </ul>
      
      <div class="highlight-quote">
        <p>"Integrasi Digital Twin dan sistem industri membawa efisiensi operasional yang luar biasa, namun juga menciptakan kompleksitas keamanan baru. Keamanan siber harus menjadi DNA dari arsitektur DT, bukan sekadar add-on feature."</p>
      </div>
    </div>

    <h2 class="section-title">Keterbatasan dan Agenda Riset Masa Depan</h2>
    <div class="conclusion-box limitations-box">
      <h3><i class="fas fa-exclamation-triangle"></i> Scope Limitations and Research Horizons</h3>
      
      <p><strong>Keterbatasan Studi Saat Ini:</strong></p>
      <ul class="enhanced-list">
        <li>
          <strong>Attack Vector Variety:</strong> Hanya mencakup malware dan DDoS attack, belum mengakomodasi varian serangan seperti phishing, ransomware, SQL injection, atau insider threats.
        </li>
        <li>
          <strong>Simultaneity Assumption:</strong> Analisis dilakukan dengan asumsi single attack vector pada satu waktu, belum mempertimbangkan kompleksitas multi-vector coordinated attacks.
        </li>
        <li>
          <strong>Countermeasure Scope:</strong> Evaluasi terbatas pada dua mekanisme pencegahan tradisional, belum mengeksplorasi teknologi emerging seperti blockchain, AI-based detection, atau zero-trust architecture.
        </li>
        <li>
          <strong>Computational Complexity:</strong> Model dapat dikembangkan dengan teknik seperti Noisy-OR gate untuk mengurangi kompleksitas komputasional pada skala enterprise.
        </li>
      </ul>
      
      <p><strong>Roadmap Penelitian Lanjutan:</strong></p>
      <ul class="enhanced-list">
        <li>
          <strong>Extended Attack Taxonomy:</strong> Penelitian komparatif terhadap 5-7 jenis serangan siber utama dengan karakteristik yang berbeda.
        </li>
        <li>
          <strong>Multi-Vector Analysis:</strong> Pemodelan simultan dari coordinated attacks yang melibatkan multiple attack vectors.
        </li>
        <li>
          <strong>Emerging Technology Integration:</strong> Studi efektivitas blockchain, federated learning, dan quantum-resistant cryptography dalam konteks DT security.
        </li>
        <li>
          <strong>Industry-Specific Models:</strong> Pengembangan model khusus untuk sektor-sektor kritis seperti healthcare DT, smart grid DT, dan autonomous vehicle DT.
        </li>
        <li>
          <strong>Real-World Validation:</strong> Implementasi dan validasi model pada organisasi industri nyata dengan skala produksi.
        </li>
      </ul>
    </div>

    <div class="conclusion-box insights-box">
      <h3><i class="fas fa-lightbulb"></i> Insight Praktis untuk Ekosistem Industri</h3>
      
      <div class="two-column-grid">
        <div style="background: rgba(14, 165, 233, 0.05); padding: 1.2rem; border-radius: 10px;">
          <h4 style="color: #0ea5e9; margin-bottom: 0.8rem;">Bagi Praktisi & Decision Makers</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li>Prioritaskan investasi pada DDoS protection sebagai first line of defense</li>
            <li>Integrasikan security assessment dalam setiap fase DT lifecycle</li>
            <li>Develop incident response plan khusus untuk DT infrastructure</li>
            <li>Implement continuous monitoring dengan temporal analysis capabilities</li>
          </ul>
        </div>
        
        <div style="background: rgba(16, 185, 129, 0.05); padding: 1.2rem; border-radius: 10px;">
          <h4 style="color: #10b981; margin-bottom: 0.8rem;">Bagi Peneliti & Akademisi</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li>DTMC-DBN framework dapat diadaptasi untuk berbagai konteks sistem dinamis</li>
            <li>Peluang penelitian pada adaptive security mechanisms untuk DT</li>
            <li>Integration dengan explainable AI untuk interpretable security analytics</li>
            <li>Cross-domain applications beyond industrial contexts</li>
          </ul>
        </div>
      </div>
      
      <p style="margin-top: 1.5rem; padding: 1rem; background: rgba(255, 204, 51, 0.1); border-radius: 10px; border-left: 4px solid #ffcc33; font-size: 0.95rem;">
        <strong>Final Thought:</strong> Dalam era konvergensi fisik-digital, keamanan siber bukan lagi fungsi pendukung, tetapi core competency yang menentukan keberlangsungan organisasi berbasis Digital Twin. Investasi pada security-by-design architecture akan menjadi pembeda kompetitif di pasar industri 4.0.
      </p>
    </div>

    <div style="text-align: center; margin-top: 3rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 14px;">
      <h3 style="color: var(--primary); margin-bottom: 1rem;"><i class="fas fa-file-alt"></i> Metadata Penelitian</h3>
      <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem;">
        <div><strong><i class="fas fa-user-graduate"></i> Penulis:</strong> Ridwan Mustofa, Md. Rafiquzzaman, Niamat Ullah Ibne Hossain</div>
        <div><strong><i class="fas fa-journal-whills"></i> Jurnal:</strong> Journal of Industrial Information Integration, Volume 41, 2024</div>
        <div><strong>Research Method:</strong> Hybrid Quantitative</div>
        <div><strong>Publication Date:</strong> March 2024</div>
      </div>
      
      <p style="margin-top: 1.5rem; font-size: 0.85rem; color: #64748b; max-width: 800px; margin-left: auto; margin-right: auto;">
        <i class="fas fa-info-circle"></i>
        Artikel review ini disusun untuk memenuhi tugas Sistem Komputer dan Jaringan oleh
         <strong>Ihsan,
        Muh Ilham Firizkillah Chairul,
        Revanza Zakly Juliansyah,
        Yoga Anjasmara Putra,
        dan Yudi Saeful Rizal</strong>.
        Semua analisis berdasarkan publikasi asli dengan interpretasi kontekstual.
      </p>
    </div>
  </div>

  <footer>
    <img src="/nuscode - white.png" alt="NUSCODE Logo" />
    <p>© 2025 NUSCODE — Teknologi dari Nusantara, untuk Dunia.</p>
    <p style="margin-top: 0.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
      Berdedikasi untuk memajukan literasi teknologi melalui penelitian, inovasi, dan kolaborasi dalam ekosistem digital Indonesia.
    </p>
    
    <div class="footer-tags">
      <span class="tag">Digital Twin Security</span>
      <span class="tag">Cyber Attack Analysis</span>
      <span class="tag">Industrial IoT</span>
      <span class="tag">Predictive Modeling</span>
      <span class="tag">Technology Review</span>
    </div>
  </footer>

  <script>
    // Mobile menu toggle - FIXED VERSION
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.menu-toggle');
      const mainNav = document.querySelector('header nav');
      
      if (menuToggle && mainNav) {
        // Toggle menu when button is clicked
        menuToggle.addEventListener('click', function(e) {
          e.stopPropagation(); // Prevent event bubbling
          mainNav.classList.toggle('active');
          
          // Change icon based on menu state
          const icon = menuToggle.querySelector('i');
          if (mainNav.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
          } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        });
        
        // Close menu when clicking on a link
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
          link.addEventListener('click', () => {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
          if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
          if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            if (icon) {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          }
        });
      }
      
      // Enhanced animations
      const boxes = document.querySelectorAll('.conclusion-box, .method-card');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            entry.target.style.transition = 'all 0.6s cubic-bezier(0.22, 0.61, 0.36, 1)';
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });
      
      boxes.forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(30px)';
        observer.observe(box);
      });
      
      // Add click effect to conclusion boxes
      document.querySelectorAll('.conclusion-box').forEach(box => {
        box.addEventListener('click', function() {
          this.style.transform = 'translateY(-8px)';
          setTimeout(() => {
            this.style.transform = 'translateY(-5px)';
          }, 150);
        });
      });
      
      // Dynamic year in footer
      const yearSpan = document.querySelector('footer p');
      if (yearSpan) {
        yearSpan.innerHTML = yearSpan.innerHTML.replace('2025', new Date().getFullYear());
      }
    });
  </script>
</body>
</html>