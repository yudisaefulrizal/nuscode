<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NUSCODE – Memahami OSI Layer: Fondasi Jaringan Komputer</title>
  <meta name="description" content="Panduan lengkap memahami 7 lapisan OSI Model dengan penjelasan visual dan sederhana untuk pemula hingga profesional teknologi." />
  <meta name="keywords" content="NUSCODE, OSI Layer, jaringan komputer, networking basics, 7 layer OSI, teknologi jaringan, pembelajaran IT" />
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
      max-width: 1000px;
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

    /* Different colors for OSI layers */
    .layer-7-box { border-left: 5px solid #4f46e5; }
    .layer-7-box::before { background: linear-gradient(180deg, #4f46e5, #7c3aed); }
    
    .layer-6-box { border-left: 5px solid #059669; }
    .layer-6-box::before { background: linear-gradient(180deg, #059669, #10b981); }
    
    .layer-5-box { border-left: 5px solid #dc2626; }
    .layer-5-box::before { background: linear-gradient(180deg, #dc2626, #ef4444); }
    
    .layer-4-box { border-left: 5px solid #ea580c; }
    .layer-4-box::before { background: linear-gradient(180deg, #ea580c, #f97316); }
    
    .layer-3-box { border-left: 5px solid #7c3aed; }
    .layer-3-box::before { background: linear-gradient(180deg, #7c3aed, #8b5cf6); }
    
    .layer-2-box { border-left: 5px solid #0ea5e9; }
    .layer-2-box::before { background: linear-gradient(180deg, #0ea5e9, #38bdf8); }
    
    .layer-1-box { border-left: 5px solid #64748b; }
    .layer-1-box::before { background: linear-gradient(180deg, #64748b, #94a3b8); }

    .history-box { border-left: 5px solid #c026d3; }
    .history-box::before { background: linear-gradient(180deg, #c026d3, #d946ef); }

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

    /* OSI Layer Grid */
    .layer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .layer-card {
      background: white;
      padding: 1.5rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.07);
      border: 1px solid rgba(0,0,0,0.04);
      transition: all 0.3s ease;
      text-align: center;
    }

    .layer-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .layer-card i {
      font-size: 2.2rem;
      color: var(--secondary);
      margin-bottom: 0.8rem;
      background: rgba(3,159,91,0.1);
      padding: 0.8rem;
      border-radius: 10px;
    }

    .layer-card h3 {
      color: var(--primary);
      font-size: 1.3rem;
      margin-bottom: 0.6rem;
    }

    .layer-number {
      display: inline-block;
      background: var(--accent);
      color: var(--primary);
      width: 26px;
      height: 26px;
      border-radius: 50%;
      font-weight: bold;
      margin-right: 0.5rem;
      line-height: 26px;
      font-size: 0.9rem;
    }

    /* OSI Visualization */
    .osi-visualization {
      background: white;
      border-radius: 14px;
      padding: 1.5rem;
      margin: 2rem 0;
      box-shadow: var(--box-shadow);
      text-align: center;
    }

    .osi-stack {
      display: flex;
      flex-direction: column;
      max-width: 600px;
      margin: 1.5rem auto;
    }

    .osi-level {
      display: flex;
      align-items: center;
      padding: 1rem;
      margin: 0.2rem 0;
      border-radius: 8px;
      transition: all 0.3s ease;
      cursor: pointer;
      flex-wrap: wrap;
    }

    .osi-level:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .level-7 { background: linear-gradient(90deg, rgba(79,70,229,0.1), rgba(79,70,229,0.05)); border-left: 4px solid #4f46e5; }
    .level-6 { background: linear-gradient(90deg, rgba(5,150,105,0.1), rgba(5,150,105,0.05)); border-left: 4px solid #059669; }
    .level-5 { background: linear-gradient(90deg, rgba(220,38,38,0.1), rgba(220,38,38,0.05)); border-left: 4px solid #dc2626; }
    .level-4 { background: linear-gradient(90deg, rgba(234,88,12,0.1), rgba(234,88,12,0.05)); border-left: 4px solid #ea580c; }
    .level-3 { background: linear-gradient(90deg, rgba(124,58,237,0.1), rgba(124,58,237,0.05)); border-left: 4px solid #7c3aed; }
    .level-2 { background: linear-gradient(90deg, rgba(14,165,233,0.1), rgba(14,165,233,0.05)); border-left: 4px solid #0ea5e9; }
    .level-1 { background: linear-gradient(90deg, rgba(100,116,139,0.1), rgba(100,116,139,0.05)); border-left: 4px solid #64748b; }

    .level-number {
      font-weight: bold;
      font-size: 1.1rem;
      min-width: 35px;
      flex-shrink: 0;
    }

    .level-name {
      flex-grow: 1;
      text-align: left;
      padding-left: 0.8rem;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .osi-level i {
      margin-left: auto;
      font-size: 1.2rem;
      color: #64748b;
      flex-shrink: 0;
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
      
      .layer-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
      
      .layer-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
      }
      
      .layer-card {
        padding: 1.2rem;
      }
      
      .highlight-quote {
        padding: 1.5rem;
      }
      
      .osi-visualization {
        padding: 1.2rem;
      }
      
      .osi-level {
        padding: 0.8rem;
      }
      
      .level-number {
        min-width: 30px;
        font-size: 1rem;
      }
      
      .level-name {
        font-size: 0.9rem;
      }
      
      /* Two-column layouts to one column on mobile */
      .two-column-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
      }
      
      .three-column-grid {
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
      
      .layer-number {
        width: 24px;
        height: 24px;
        line-height: 24px;
        font-size: 0.85rem;
      }
    }

    /* Utility classes for responsive layouts */
    .two-column-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    .three-column-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 1rem;
    }

    @media (max-width: 768px) {
      .two-column-grid {
        grid-template-columns: 1fr;
      }
      
      .three-column-grid {
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
    
    /* Custom icons */
    .fas.fa-fiber:before {
      content: "⚡";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
    }
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
      <a href="/artikel/Cyber-Security-Digital-Twin">Security DT</a>
      <a href="/review-jurnal">Review Jurnal</a>
      <a href="/chatbot"><i class="fas fa-robot"></i> Chatbot</a>
    </nav>
  </header>

  <div class="article-header">
    <h1>Memahami OSI Layer: Fondasi Jaringan Komputer</h1>
    <p class="article-subtitle">Panduan Visual 7 Lapisan OSI Model untuk Pemula Hingga Profesional</p>
    
    <div class="meta">
      <div class="meta-item">
        <i class="fas fa-tags"></i>
        <span>Networking • OSI Model • Jaringan Komputer • IT Fundamentals</span>
      </div>
    </div>
  </div>

  <div class="article-content">
    
    <h2 class="section-title">Sejarah dan Tujuan OSI Model</h2>
    <div class="conclusion-box history-box">
      <h3><i class="fas fa-history"></i> Latar Belakang Perkembangan</h3>
      <p>OSI Layer (Open Systems Interconnection) pertama kali diperkenalkan oleh International Organization for Standardization (ISO) pada tahun 1984 sebagai respons terhadap kompleksitas jaringan komputer yang berkembang pesat.</p>
      
      <p><strong>Masalah Utama Sebelum OSI:</strong></p>
      <ul class="enhanced-list">
        <li><strong>Inkompatibilitas Vendor:</strong> Setiap produsen memiliki protokol jaringan sendiri-sendiri</li>
        <li><strong>Biaya Integrasi Tinggi:</strong> Menghubungkan sistem dari vendor berbeda sangat mahal dan rumit</li>
        <li><strong>Keterbatasan Interoperabilitas:</strong> Perangkat tidak dapat berkomunikasi lintas platform</li>
        <li><strong>Kesulitan Troubleshooting:</strong> Tidak ada standar untuk diagnosis masalah jaringan</li>
      </ul>
      
      <p><strong>Tujuan Utama OSI Model:</strong></p>
      <ul class="enhanced-list">
        <li>Menciptakan standar universal untuk komunikasi jaringan</li>
        <li>Memungkinkan interoperabilitas perangkat dari vendor berbeda</li>
        <li>Menyederhanakan pengembangan dan implementasi jaringan</li>
        <li>Menyediakan kerangka kerja untuk troubleshooting dan optimasi</li>
        <li>Memfasilitasi evolusi teknologi jaringan secara terstruktur</li>
      </ul>
    </div>

    <h2 class="section-title">Mengapa OSI Layer Penting?</h2>
    <div class="conclusion-box">
      <h3><i class="fas fa-lightbulb"></i> Signifikansi dalam Dunia Teknologi</h3>
      
      <p>Pemahaman tentang OSI Layer bukan hanya untuk ahli jaringan, tetapi penting untuk berbagai profesi teknologi:</p>
      
      <div class="two-column-grid">
        <div style="background: rgba(3,159,91,0.05); padding: 1.2rem; border-radius: 10px; border-left: 4px solid #039f5b;">
          <h4 style="color: #039f5b; margin-bottom: 0.8rem;">Bagi Pengembang Software</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li>Memahami bagaimana aplikasi berkomunikasi di jaringan</li>
            <li>Mendesain aplikasi yang efisien dan kompatibel</li>
            <li>Mengimplementasikan keamanan yang tepat di setiap layer</li>
            <li>Optimasi performa komunikasi jaringan</li>
          </ul>
        </div>
        
        <div style="background: rgba(255,204,51,0.05); padding: 1.2rem; border-radius: 10px; border-left: 4px solid #ffcc33;">
          <h4 style="color: #d97706; margin-bottom: 0.8rem;">Bagi Network Engineer</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li>Troubleshooting masalah jaringan secara sistematis</li>
            <li>Perencanaan dan desain infrastruktur jaringan</li>
            <li>Implementasi protokol dan standar yang tepat</li>
            <li>Optimasi performa dan keamanan jaringan</li>
          </ul>
        </div>
      </div>
      
      <p><strong>Konsep Dasar:</strong> OSI Layer membantu memvisualisasikan bagaimana data bergerak dari perangkat fisik (layer 1) hingga aplikasi yang kita gunakan sehari-hari (layer 7). Setiap layer memiliki fungsi spesifik dan berkomunikasi dengan layer di atas dan bawahnya melalui interface yang terdefinisi dengan baik.</p>
    </div>

    <h2 class="section-title">Visualisasi 7 Lapisan OSI</h2>
    
    <div class="osi-visualization">
      <h3><i class="fas fa-layer-group"></i> OSI Model Stack</h3>
      <p>Berikut adalah representasi visual dari 7 lapisan OSI, dari layer aplikasi hingga layer fisik:</p>
      
      <div class="osi-stack">
        <div class="osi-level level-7">
          <div class="level-number">7</div>
          <div class="level-name">Application Layer</div>
          <i class="fas fa-desktop"></i>
        </div>
        <div class="osi-level level-6">
          <div class="level-number">6</div>
          <div class="level-name">Presentation Layer</div>
          <i class="fas fa-file-alt"></i>
        </div>
        <div class="osi-level level-5">
          <div class="level-number">5</div>
          <div class="level-name">Session Layer</div>
          <i class="fas fa-handshake"></i>
        </div>
        <div class="osi-level level-4">
          <div class="level-number">4</div>
          <div class="level-name">Transport Layer</div>
          <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="osi-level level-3">
          <div class="level-number">3</div>
          <div class="level-name">Network Layer</div>
          <i class="fas fa-globe"></i>
        </div>
        <div class="osi-level level-2">
          <div class="level-number">2</div>
          <div class="level-name">Data Link Layer</div>
          <i class="fas fa-network-wired"></i>
        </div>
        <div class="osi-level level-1">
          <div class="level-number">1</div>
          <div class="level-name">Physical Layer</div>
          <i class="fas fa-plug"></i>
        </div>
      </div>
      
      <p style="margin-top: 1.5rem; font-size: 0.9rem; color: #64748b;">
        <i class="fas fa-info-circle"></i> Klik pada setiap layer untuk melihat penjelasan detail
      </p>
    </div>

    <h2 class="section-title">Detail 7 Lapisan OSI</h2>
    
    <div class="conclusion-box layer-7-box">
      <h3><i class="fas fa-desktop"></i> Layer 7: Application Layer</h3>
      <p><span class="layer-number">7</span> <strong>Fungsi Utama:</strong> Interface antara pengguna dan sistem jaringan</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Berinteraksi langsung dengan pengguna akhir</li>
        <li>Menyediakan layanan jaringan ke aplikasi</li>
        <li>Contoh: HTTP, FTP, SMTP, DNS, Telnet</li>
        <li>Tidak termasuk aplikasi itu sendiri, tapi protokol yang digunakan aplikasi</li>
      </ul>
      
      <p><strong>Contoh Praktis:</strong> Ketika Anda membuka browser dan mengetik "google.com", browser menggunakan HTTP (Application Layer protocol) untuk berkomunikasi dengan server Google.</p>
      
      <div style="background: rgba(79,70,229,0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <strong>Mnemonic:</strong> <em>"All People Seem To Need Data Processing"</em> (A = Application)
      </div>
    </div>

    <div class="conclusion-box layer-6-box">
      <h3><i class="fas fa-file-alt"></i> Layer 6: Presentation Layer</h3>
      <p><span class="layer-number">6</span> <strong>Fungsi Utama:</strong> Translasi, enkripsi, dan kompresi data</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Menerjemahkan data antara Application Layer dan format jaringan</li>
        <li>Menangani enkripsi dan dekripsi untuk keamanan</li>
        <li>Melakukan kompresi data untuk efisiensi bandwidth</li>
        <li>Mengkonversi karakter set (ASCII ke EBCDIC, dll)</li>
      </ul>
      
      <p><strong>Contoh Praktis:</strong> Saat Anda mengirim file ZIP melalui email, Presentation Layer bertanggung jawab untuk kompresi file sebelum dikirim, dan dekompresi saat diterima.</p>
      
      <div style="background: rgba(5,150,105,0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <strong>Fungsi Kritis:</strong> Memastikan data dapat dibaca oleh sistem penerima, terlepas dari perbedaan format atau encoding.
      </div>
    </div>

    <div class="conclusion-box layer-5-box">
      <h3><i class="fas fa-handshake"></i> Layer 5: Session Layer</h3>
      <p><span class="layer-number">5</span> <strong>Fungsi Utama:</strong> Mengelola dialog antara komputer</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Membuka, mengelola, dan menutup sesi komunikasi</li>
        <li>Mengatur dialog (full-duplex, half-duplex, simplex)</li>
        <li>Melakukan checkpoint dan recovery</li>
        <li>Koordinasi komunikasi antar aplikasi</li>
      </ul>
      
      <p><strong>Contoh Praktis:</strong> Saat Anda login ke situs web, Session Layer membuat sesi yang memungkinkan Anda tetap login saat berpindah halaman. Jika koneksi terputus, Session Layer dapat memulihkan sesi dari titik terakhir.</p>
      
      <div style="background: rgba(220,38,38,0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <strong>Analogi:</strong> Seperti konduktor orkestra yang mengatur kapan setiap instrumen mulai dan berhenti bermain.
      </div>
    </div>

    <div class="conclusion-box layer-4-box">
      <h3><i class="fas fa-exchange-alt"></i> Layer 4: Transport Layer</h3>
      <p><span class="layer-number">4</span> <strong>Fungsi Utama:</strong> Pengiriman end-to-end yang andal</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Memastikan pengiriman data lengkap dan benar</li>
        <li>Flow control (mengatur kecepatan pengiriman)</li>
        <li>Error detection and recovery</li>
        <li>Segmentation dan reassembly data</li>
        <li>Port addressing (TCP/UDP ports)</li>
      </ul>
      
      <p><strong>Protokol Penting:</strong></p>
      <div class="two-column-grid">
        <div style="background: #f3f4f6; padding: 1rem; border-radius: 8px;">
          <strong>TCP (Transmission Control Protocol)</strong><br>
          • Connection-oriented<br>
          • Reliable delivery<br>
          • Flow control<br>
          • Contoh: Web browsing, email
        </div>
        <div style="background: #f3f4f6; padding: 1rem; border-radius: 8px;">
          <strong>UDP (User Datagram Protocol)</strong><br>
          • Connectionless<br>
          • Faster delivery<br>
          • No guarantee<br>
          • Contoh: Video streaming, VoIP
        </div>
      </div>
    </div>

    <div class="conclusion-box layer-3-box">
      <h3><i class="fas fa-globe"></i> Layer 3: Network Layer</h3>
      <p><span class="layer-number">3</span> <strong>Fungsi Utama:</strong> Routing data antar jaringan</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Logical addressing (IP addresses)</li>
        <li>Routing packets antara jaringan</li>
        <li>Path determination (menentukan jalur terbaik)</li>
        <li>Fragmentation dan reassembly</li>
        <li>Internetworking (menghubungkan jaringan berbeda)</li>
      </ul>
      
      <p><strong>Contoh Praktis:</strong> Router bekerja di layer ini. Ketika Anda mengirim email ke seseorang di negara lain, Network Layer menentukan rute terbaik melalui berbagai router di internet.</p>
      
      <div style="background: rgba(124,58,237,0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <strong>Perangkat Utama:</strong> Router - Perangkat yang membuat keputusan cerdas tentang bagaimana mengirimkan paket data melalui jaringan.
      </div>
    </div>

    <div class="conclusion-box layer-2-box">
      <h3><i class="fas fa-network-wired"></i> Layer 2: Data Link Layer</h3>
      <p><span class="layer-number">2</span> <strong>Fungsi Utama:</strong> Pengiriman data antar perangkat dalam jaringan yang sama</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Physical addressing (MAC addresses)</li>
        <li>Frame formatting dan sequencing</li>
        <li>Error detection (CRC checks)</li>
        <li>Media access control (CSMA/CD, CSMA/CA)</li>
        <li>Mengatur akses ke media fisik</li>
      </ul>
      
      <p><strong>Dua Sub-layer:</strong></p>
      <div class="two-column-grid">
        <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px;">
          <strong>LLC (Logical Link Control)</strong><br>
          • Interface ke Network Layer<br>
          • Flow control<br>
          • Error control
        </div>
        <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px;">
          <strong>MAC (Media Access Control)</strong><br>
          • Physical addressing<br>
          • Media access control<br>
          • Frame formatting
        </div>
      </div>
    </div>

    <div class="conclusion-box layer-1-box">
      <h3><i class="fas fa-plug"></i> Layer 1: Physical Layer</h3>
      <p><span class="layer-number">1</span> <strong>Fungsi Utama:</strong> Transmisi bit melalui media fisik</p>
      
      <p><strong>Karakteristik Kunci:</strong></p>
      <ul class="enhanced-list">
        <li>Transmisi bit stream (0 dan 1)</li>
        <li>Definisi karakteristik fisik koneksi</li>
        <li>Spesifikasi kabel, konektor, voltage, timing</li>
        <li>Topologi jaringan fisik</li>
        <li>Transmission mode (simplex, half-duplex, full-duplex)</li>
      </ul>
      
      <p><strong>Contoh Teknologi:</strong></p>
      <div class="three-column-grid">
        <div style="text-align: center;">
          <i class="fas fa-ethernet" style="font-size: 1.8rem; color: #64748b;"></i><br>
          <small>Ethernet Cables</small>
        </div>
        <div style="text-align: center;">
          <i class="fas fa-wifi" style="font-size: 1.8rem; color: #64748b;"></i><br>
          <small>Wi-Fi Radio Waves</small>
        </div>
        <div style="text-align: center;">
          <i class="fas fa-bolt" style="font-size: 1.8rem; color: #64748b;"></i><br>
          <small>Fiber Optic</small>
        </div>
      </div>
      
      <div style="background: rgba(100,116,139,0.05); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
        <strong>Intinya:</strong> Layer 1 berhubungan dengan "hal-hal yang bisa disentuh" - kabel, sinyal listrik, gelombang radio, dan komponen fisik lainnya.
      </div>
    </div>

    <div class="highlight-quote">
      <p>"Memahami OSI Layer membantu kita melihat bagaimana data bergerak dari kabel hingga aplikasi yang kita gunakan sehari-hari. Setiap layer memiliki tanggung jawab spesifik, dan bersama-sama mereka memungkinkan komunikasi digital yang kita andalkan setiap hari."</p>
    </div>

    <h2 class="section-title">Aplikasi Praktis dan Manfaat</h2>
    <div class="conclusion-box">
      <h3><i class="fas fa-tools"></i> Penerapan dalam Dunia Nyata</h3>
      
      <div class="two-column-grid">
        <div style="background: rgba(3,159,91,0.05); padding: 1.2rem; border-radius: 10px;">
          <h4 style="color: #039f5b; margin-bottom: 0.8rem;"><i class="fas fa-search"></i> Troubleshooting Jaringan</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li><strong>Metodologi Bottom-Up:</strong> Mulai dari Physical Layer (layer 1) naik ke Application Layer</li>
            <li><strong>Metodologi Top-Down:</strong> Mulai dari Application Layer turun ke Physical Layer</li>
            <li><strong>Divide and Conquer:</strong> Mengisolasi masalah ke layer tertentu</li>
            <li><strong>Tools Specific per Layer:</strong> Ping (Layer 3), Traceroute (Layer 3), Netstat (Layer 4)</li>
          </ul>
        </div>
        
        <div style="background: rgba(255,204,51,0.05); padding: 1.2rem; border-radius: 10px;">
          <h4 style="color: #d97706; margin-bottom: 0.8rem;"><i class="fas fa-shield-alt"></i> Keamanan Jaringan</h4>
          <ul style="padding-left: 1.5rem; font-size: 0.95rem;">
            <li><strong>Layered Security:</strong> Implementasi keamanan di multiple layers</li>
            <li><strong>Firewall:</strong> Bekerja di Layer 3-4 (Network/Transport)</li>
            <li><strong>VPN:</strong> Enkripsi di Layer 3 (IPsec) atau Layer 2</li>
            <li><strong>IDS/IPS:</strong> Monitoring di berbagai layers untuk deteksi anomali</li>
          </ul>
        </div>
      </div>
      
      <p><strong>Contoh Kasus Troubleshooting:</strong></p>
      <div style="background: #f8fafc; padding: 1.2rem; border-radius: 10px; margin: 1.5rem 0; border-left: 4px solid #7c3aed;">
        <p><strong>Masalah:</strong> Tidak bisa mengakses website tertentu</p>
        <p><strong>Analisis OSI-based:</strong></p>
        <ol style="padding-left: 1.5rem;">
          <li><strong>Layer 1:</strong> Cek kabel dan koneksi fisik ✓</li>
          <li><strong>Layer 2:</strong> Cek MAC address dan switch connection ✓</li>
          <li><strong>Layer 3:</strong> Ping ke default gateway - BERHASIL ✓</li>
          <li><strong>Layer 3:</strong> Ping ke website - GAGAL ✗</li>
          <li><strong>Kesimpulan:</strong> Masalah di routing (Layer 3) atau DNS (Layer 7)</li>
        </ol>
      </div>
    </div>

    <div style="text-align: center; margin-top: 3rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 14px;">
      <h3 style="color: var(--primary); margin-bottom: 1rem;"><i class="fas fa-graduation-cap"></i> Ringkasan Pembelajaran</h3>
      <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap; margin: 1.5rem 0; font-size: 0.9rem;">
        <div><strong>7 Layers:</strong> Physical → Data Link → Network → Transport → Session → Presentation → Application</div>
        <div><strong>Mnemonic:</strong> "Please Do Not Throw Sausage Pizza Away"</div>
        <div><strong>Protokol Utama:</strong> TCP/IP, HTTP, FTP, DNS, IP, Ethernet</div>
      </div>
      <p style="margin-top: 1.5rem; font-size: 0.85rem; color: #64748b;">
        <i class="fas fa-info-circle"></i> Artikel ini disusun untuk memenuhi tugas Sistem Komputer dan Jaringan oleh Yudi Saeful Rizal. Semua penjelasan berdasarkan standar OSI Model dengan ilustrasi praktis untuk pemahaman yang lebih baik.
      </p>
    </div>
  </div>

  <footer>
    <img src="/nuscode - white.png" alt="NUSCODE Logo" />
    <p>© 2025 NUSCODE — Teknologi dari Nusantara, untuk Dunia.</p>
    <p style="margin-top: 0.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
      Berdedikasi untuk memajukan literasi teknologi melalui pendidikan, inovasi, dan kolaborasi dalam ekosistem digital Indonesia.
    </p>
    
    <div class="footer-tags">
      <span class="tag">OSI Model</span>
      <span class="tag">Jaringan Komputer</span>
      <span class="tag">Networking Basics</span>
      <span class="tag">IT Education</span>
      <span class="tag">Technology Learning</span>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
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
      const boxes = document.querySelectorAll('.conclusion-box, .layer-card, .osi-level');
      
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
      
      // Click effect on OSI levels
      document.querySelectorAll('.osi-level').forEach(level => {
        level.addEventListener('click', function() {
          const layerNumber = this.querySelector('.level-number').textContent;
          const layerName = this.querySelector('.level-name').textContent;
          
          // Scroll to corresponding layer box
          const targetBox = document.querySelector(`.layer-${layerNumber}-box`);
          if (targetBox) {
            targetBox.scrollIntoView({
              behavior: 'smooth',
              block: 'center'
            });
            
            // Highlight effect
            targetBox.style.boxShadow = '0 0 0 3px rgba(255,204,51,0.5)';
            setTimeout(() => {
              targetBox.style.boxShadow = 'var(--box-shadow)';
            }, 1500);
          }
        });
      });
      
      // Dynamic year in footer
      const yearSpan = document.querySelector('footer p');
      if (yearSpan && yearSpan.textContent.includes('2025')) {
        yearSpan.innerHTML = yearSpan.innerHTML.replace('2025', new Date().getFullYear());
      }
    });
  </script>
</body>
</html>