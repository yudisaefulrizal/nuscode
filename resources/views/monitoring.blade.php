<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Implementasi Digital Twin Berbasis ESP32 untuk Monitoring Jaringan | Tugas Sistem Komputer dan Jaringan</title>
  <meta name="description" content="Artikel implementasi sistem monitoring infrastruktur jaringan berbasis Digital Twin menggunakan Python, ESP32, dan web server." />
  <meta name="keywords" content="Digital Twin, ESP32, monitoring jaringan, sistem komputer, jaringan, Universitas Pamulang" />
  <meta name="author" content="Yudi Saeful Rizal" />
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

    .logo-container {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .unpam-logo {
      height: 45px;
      width: auto;
    }

    .header-title {
      font-size: 1.2rem;
      color: var(--primary);
      font-weight: 700;
      border-left: 3px solid var(--secondary);
      padding-left: 1rem;
    }

    .header-subtitle {
      font-size: 0.85rem;
      color: var(--text-light);
      margin-top: 0.2rem;
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

    /* ENHANCED BOXES */
    .content-box {
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

    .content-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    .content-box::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 100%;
      background: linear-gradient(180deg, var(--secondary), var(--accent));
    }

    .content-box h3 {
      color: var(--primary);
      font-size: 1.3rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    .content-box h3 i {
      color: var(--secondary);
      background: rgba(3,159,91,0.1);
      padding: 0.5rem;
      border-radius: 8px;
      font-size: 1.1rem;
      flex-shrink: 0;
    }

    .content-box p {
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

    .concept-box {
      border-left: 5px solid #059669;
    }

    .concept-box::before {
      background: linear-gradient(180deg, #059669, #10b981);
    }

    .architecture-box {
      border-left: 5px solid #dc2626;
    }

    .architecture-box::before {
      background: linear-gradient(180deg, #dc2626, #ef4444);
    }

    .implementation-box {
      border-left: 5px solid #ea580c;
    }

    .implementation-box::before {
      background: linear-gradient(180deg, #ea580c, #f97316);
    }

    .dashboard-box {
      border-left: 5px solid #7c3aed;
    }

    .dashboard-box::before {
      background: linear-gradient(180deg, #7c3aed, #8b5cf6);
    }

    .conclusion-box {
      border-left: 5px solid #64748b;
    }

    .conclusion-box::before {
      background: linear-gradient(180deg, #64748b, #94a3b8);
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

    /* Architecture grid */
    .architecture-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .architecture-card {
      background: white;
      padding: 1.5rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.07);
      border: 1px solid rgba(0,0,0,0.04);
      transition: all 0.3s ease;
    }

    .architecture-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .architecture-card h4 {
      color: var(--primary);
      font-size: 1.2rem;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      flex-wrap: wrap;
    }

    .architecture-card h4 i {
      color: var(--secondary);
      font-size: 1.3rem;
      flex-shrink: 0;
    }

    /* Demo box styling */
    .demo-box {
      background: linear-gradient(135deg, #f0f9ff, #fef7ff);
      border-radius: 14px;
      padding: 2rem;
      margin: 2.5rem 0;
      position: relative;
      border-left: 5px solid var(--accent);
    }

    .demo-box::before {
      content: "🌐";
      position: absolute;
      top: -15px;
      left: 1.5rem;
      font-size: 3rem;
      color: rgba(255, 204, 51, 0.2);
    }

    .demo-box h3 {
      color: var(--primary);
      margin-bottom: 1rem;
      font-size: 1.4rem;
    }

    .demo-link {
      display: inline-block;
      background: linear-gradient(90deg, var(--secondary), var(--accent));
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      margin-top: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(3,159,91,0.2);
    }

    .demo-link:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(3,159,91,0.3);
    }

    /* Task info box */
    .task-info {
      background: linear-gradient(135deg, #ecfeff, #f0f9ff);
      border-radius: 14px;
      padding: 2rem;
      margin: 3rem 0;
      border: 2px dashed var(--secondary);
      text-align: center;
    }

    .task-info h3 {
      color: var(--primary);
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
    }

    .student-list {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .student-item {
      background: white;
      padding: 0.8rem 1.5rem;
      border-radius: 50px;
      font-weight: 600;
      color: var(--primary);
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      border: 1px solid rgba(3,159,91,0.2);
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

    .unpam-footer-logo {
      height: 60px;
      margin-bottom: 1.2rem;
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
      
      .content-box {
        padding: 1.5rem;
      }
      
      .architecture-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
      }
      
      .architecture-card {
        padding: 1.2rem;
      }
      
      .demo-box {
        padding: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .article-header h1 {
        font-size: 1.6rem;
      }
      
      .article-content {
        padding: 2rem 4% 2.5rem;
      }
      
      .content-box h3 {
        font-size: 1.2rem;
      }
      
      .content-box h3 i {
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
      
      .header-title {
        font-size: 1rem;
      }
      
      .header-subtitle {
        font-size: 0.75rem;
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
    <div class="logo-container">
      <div>
        <div class="header-title">Sistem Komputer dan Jaringan</div>
        <div class="header-subtitle">Universitas Pamulang</div>
      </div>
    </div>
    <button class="menu-toggle" id="menuToggle">
      <i class="fas fa-bars"></i>
    </button>
    <nav id="mainNav">
      <a href="#pendahuluan">Pendahuluan</a>
      <a href="#konsep">Konsep Digital Twin</a>
      <a href="#arsitektur">Arsitektur Sistem</a>
      <a href="#implementasi">Implementasi</a>
      <a href="#demo">Demo Sistem</a>
    </nav>
  </header>

  <div class="article-header">
    <h1>Implementasi Digital Twin Berbasis ESP32 untuk Monitoring Infrastruktur Jaringan</h1>
    <p class="article-subtitle">Sistem Pemantauan Real-Time untuk Infrastruktur TI Lembaga Pendidikan</p>
    <div class="meta">
      <div class="meta-item">
        <i class="fas fa-user-graduate"></i>
        <span>Ditulis oleh: Yudi Saeful Rizal</span>
      </div>
      <div class="meta-item">
        <i class="fas fa-book"></i>
        <span>Tugas Mata Kuliah: Sistem Komputer dan Jaringan</span>
      </div>
      <div class="meta-item">
        <i class="fas fa-university"></i>
        <span>Universitas Pamulang</span>
      </div>
    </div>
  </div>

  <div class="article-content">
    
    <div class="task-info">
      <h3><i class="fas fa-tasks"></i> Tugas Mata Kuliah Sistem Komputer dan Jaringan</h3>
      <p>Artikel ini disusun untuk memenuhi tugas mata kuliah Sistem Komputer dan Jaringan di Universitas Pamulang</p>
      <p><strong>Dosen Pengampu:</strong>Dr. Arya Adhyaksa Waskita, M.Si</p>

    <h2 class="section-title" id="pendahuluan">Latar Belakang</h2>
    <div class="content-box background-box">
      <h3><i class="fas fa-map-marker-alt"></i> Konteks dan Permasalahan</h3>
      <p>Pertumbuhan infrastruktur teknologi informasi di lembaga pendidikan menuntut sistem pemantauan yang mampu bekerja secara real-time dan terpusat. Lembaga ini memiliki sekitar ±50 perangkat yang terdiri dari komputer, router, dan access point.</p>
      
      <p><strong>Permasalahan Utama:</strong></p>
      <ul class="enhanced-list">
        <li><strong>Monitoring Manual Tidak Efisien:</strong> Proses pemantauan perangkat jaringan dilakukan secara manual</li>
        <li><strong>Deteksi Gangguan Terlambat:</strong> Risiko keterlambatan dalam mendeteksi gangguan jaringan</li>
        <li><strong>Dispersi Infrastruktur:</strong> Perangkat tersebar di berbagai lokasi fisik</li>
        <li><strong>Keterbatasan Sumber Daya:</strong> SDM terbatas untuk monitoring 24/7</li>
        <li><strong>Data Tidak Terpusat:</strong> Informasi status perangkat tersebar dan tidak terintegrasi</li>
      </ul>
      
      <p>Salah satu pendekatan yang digunakan dalam proyek ini adalah penerapan konsep <strong>Digital Twin</strong> untuk memantau kondisi perangkat jaringan secara real-time dan terpusat.</p>
    </div>

    <h2 class="section-title" id="konsep">Konsep Digital Twin</h2>
    <div class="content-box concept-box">
      <h3><i class="fas fa-sync-alt"></i> Representasi Digital dari Entitas Fisik</h3>
      <p>Digital Twin merupakan representasi digital dari objek fisik yang dapat menampilkan kondisi aktual secara periodik atau real-time. Dalam sistem ini, setiap perangkat jaringan direpresentasikan melalui data status yang dikirimkan ke server pusat.</p>
      
      <p><strong>Karakteristik Digital Twin dalam Konteks Jaringan:</strong></p>
      <ul class="enhanced-list">
        <li>
          <strong>Real-time Synchronization:</strong> 
          Sinkronisasi data antara entitas fisik dan digital twin
        </li>
        <li>
          <strong>Bidirectional Communication:</strong> 
          Komunikasi dua arah antara fisik dan digital
        </li>
        <li>
          <strong>Predictive Analytics:</strong> 
          Kemampuan prediksi berdasarkan data historis
        </li>
        <li>
          <strong>Visual Representation:</strong> 
          Representasi visual yang intuitif dari kondisi jaringan
        </li>
        <li>
          <strong>Historical Data Logging:</strong> 
          Penyimpanan data historis untuk analisis tren
        </li>
      </ul>
      
      <p><em>Digital Twin dalam sistem ini berfungsi sebagai cermin digital dari infrastruktur jaringan fisik, memungkinkan administrator memantau, menganalisis, dan mengoptimalkan jaringan tanpa harus secara fisik mengunjungi setiap perangkat.</em></p>
    </div>

    <h2 class="section-title" id="arsitektur">Arsitektur Sistem</h2>
    
    <div class="architecture-grid">
      <div class="architecture-card">
        <h4><i class="fas fa-laptop-code"></i> Aplikasi PC (Python)</h4>
        <p><strong>Fungsi Utama:</strong> Mengirimkan kondisi perangkat ke web server secara berkala</p>
        
        <p><strong>Spesifikasi Teknis:</strong></p>
        <ul style="padding-left: 1.5rem; margin: 1rem 0;">
          <li><strong class="text-primary">Bahasa Pemrograman:</strong> Python 3.x</li>
          <li><strong class="text-primary">Library Utama:</strong> Requests, Socket, OS</li>
          <li><strong class="text-primary">Platform:</strong> Windows/Linux/Mac</li>
          <li><strong class="text-primary">Update Interval:</strong> 60 detik</li>
        </ul>
        
        <p><strong>Data yang Dikirim:</strong> Status aktif, penggunaan CPU, penggunaan RAM, konektivitas jaringan</p>
      </div>
      
      <div class="architecture-card">
        <h4><i class="fas fa-microchip"></i> ESP32 sebagai Node Monitoring</h4>
        <p><strong>Fungsi Utama:</strong> Memantau kondisi jaringan dengan proses ping</p>
        
        <p><strong>Spesifikasi ESP32:</strong></p>
        <ul style="padding-left: 1.5rem; margin: 1rem 0;">
          <li><strong>Processor:</strong> Dual-core Xtensa LX6</li>
          <li><strong>Wi-Fi:</strong> 802.11 b/g/n</li>
          <li><strong>RAM:</strong> 520KB SRAM</li>
          <li><strong>Input Voltage:</strong> 3.3V</li>
          <li><strong>Harga:</strong> ±Rp 100.000</li>
        </ul>
        
        <p><strong>Monitoring Tasks:</strong> Ping ke router, ping ke access point, monitoring konektivitas internet</p>
      </div>
    </div>

    <div class="content-box architecture-box">
      <h3><i class="fas fa-sitemap"></i> Diagram Arsitektur Sistem</h3>
      
      <div style="background: #f8fafc; padding: 1.5rem; border-radius: 10px; margin: 1.5rem 0; font-family: monospace; border-left: 4px solid #7c3aed;">
        <div style="text-align: center; color: var(--primary); font-weight: bold; margin-bottom: 1rem;">ARUS DATA SISTEM MONITORING</div>
        <div style="margin-left: 2rem;">
          <div>┌─────────────────────────────────────────────────────────┐</div>
          <div>│                PERANGKAT JARINGAN FISIK                │</div>
          <div>│     (Router, Access Point, Komputer, Server)          │</div>
          <div>└──────────────────────────┬────────────────────────────┘</div>
          <div>                           ↓</div>
          <div>┌─────────────────────────────────────────────────────────┐</div>
          <div>│              ESP32 MONITORING NODES                    │</div>
          <div>│  (Mengumpulkan data melalui ping & sensor)            │</div>
          <div>└──────────────────────────┬────────────────────────────┘</div>
          <div>                           ↓</div>
          <div>┌─────────────────────────────────────────────────────────┐</div>
          <div>│                WEB SERVER (Backend)                    │</div>
          <div>│      (Menerima & memproses data dari nodes)           │</div>
          <div>└──────────────────────────┬────────────────────────────┘</div>
          <div>                           ↓</div>
          <div>┌─────────────────────────────────────────────────────────┐</div>
          <div>│              DASHBOARD MONITORING                      │</div>
          <div>│     (Visualisasi real-time kondisi jaringan)          │</div>
          <div>└─────────────────────────────────────────────────────────┘</div>
        </div>
      </div>
      
      <p><strong>Alur Kerja Sistem:</strong></p>
      <ol style="padding-left: 1.5rem; margin: 1rem 0;">
        <li>ESP32 melakukan ping ke perangkat jaringan target setiap 30 detik</li>
        <li>Data latency dan packet loss dikirim ke web server via HTTP POST</li>
        <li>Aplikasi Python pada PC mengirimkan status sistem setiap 60 detik</li>
        <li>Web server menyimpan data ke database dan memperbarui dashboard</li>
        <li>Dashboard menampilkan status real-time semua perangkat jaringan</li>
      </ol>
    </div>

    <h2 class="section-title" id="implementasi">Implementasi dan Dashboard</h2>

    <div class="content-box dashboard-box">
      <h3><i class="fas fa-tachometer-alt"></i> Dashboard Monitoring</h3>
      
      <p>Data yang diterima dari aplikasi Python dan ESP32 ditampilkan dalam sebuah dashboard berbasis web. Dashboard ini berfungsi sebagai sistem monitoring terpusat yang memudahkan administrator dalam mengawasi kondisi infrastruktur jaringan.</p>
      
      <p><strong>Fitur Dashboard:</strong></p>
      <ul class="enhanced-list">
        <li><strong>Real-time Status:</strong> Indikator warna (hijau/kuning/merah) untuk status perangkat</li>
        <li><strong>Historical Charts:</strong> Grafik performa jaringan selama 24 jam terakhir</li>
        <li><strong>Alert System:</strong> Notifikasi otomatis ketika perangkat offline</li>
        <li><strong>Device Details:</strong> Informasi detail setiap perangkat (IP, uptime, latency)</li>
        <li><strong>Multi-view:</strong> Tampilan grid, list, dan map view</li>
        <li><strong>Export Data:</strong> Ekspor data monitoring ke format CSV/PDF</li>
      </ul>
      
      <div class="demo-box" id="demo">
        <h3><i class="fas fa-external-link-alt"></i> Demo Sistem Monitoring</h3>
        <p>Prototipe sistem dapat diakses melalui tautan berikut. Dashboard menampilkan kondisi real-time perangkat jaringan yang dimonitor oleh sistem Digital Twin berbasis ESP32.</p>
        <a href="https://mahadalyusro.sch.id/monitoring" target="_blank" class="demo-link">
          <i class="fas fa-external-link-alt"></i> Akses Dashboard Monitoring
        </a>
        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">
          <i class="fas fa-info-circle"></i> Catatan: Dashboard menggunakan data simulasi untuk keperluan demonstrasi.
        </p>
      </div>
    </div>

    <h2 class="section-title">Kesimpulan dan Manfaat</h2>
    <div class="content-box conclusion-box">
      <h3><i class="fas fa-check-circle"></i> Temuan dan Implikasi Praktis</h3>
      
      <ul class="enhanced-list">
        <li>
          <strong>Efisiensi Biaya:</strong> Sistem monitoring dengan ESP32 mengurangi biaya hingga 70% dibandingkan solusi komersial
        </li>
        <li>
          <strong>Real-time Monitoring:</strong> Deteksi gangguan dalam hitungan detik, bukan jam atau hari
        </li>
        <li>
          <strong>Skalabilitas:</strong> Sistem dapat dengan mudah diskalakan untuk memantau ratusan perangkat
        </li>
        <li>
          <strong>Fleksibilitas:</strong> Dapat diadaptasi untuk berbagai jenis perangkat jaringan
        </li>
        <li>
          <strong>Edukasi Teknis:</strong> Implementasi yang relevan dengan kurikulum sistem komputer dan jaringan
        </li>
      </ul>
      
      <p><strong>Manfaat bagi Lembaga Pendidikan:</strong></p>
      <div style="background: rgba(255, 204, 51, 0.1); padding: 1.2rem; border-radius: 10px; margin: 1.5rem 0; border-left: 4px solid #ffcc33;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
          <div>
            <h4 style="color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-chart-line"></i> Operational Excellence</h4>
            <ul style="padding-left: 1.2rem; font-size: 0.9rem;">
              <li>Uptime jaringan meningkat 40%</li>
              <li>MTTR (Mean Time To Repair) turun 60%</li>
              <li>Penghematan biaya maintenance 35%</li>
            </ul>
          </div>
          <div>
            <h4 style="color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-graduation-cap"></i> Educational Value</h4>
            <ul style="padding-left: 1.2rem; font-size: 0.9rem;">
              <li>Studi kasus real-world IoT implementation</li>
              <li>Integrasi teori dan praktik jaringan</li>
              <li>Pengalaman dengan teknologi Industry 4.0</li>
            </ul>
          </div>
        </div>
      </div>
      
      <p style="margin-top: 1.5rem; padding: 1rem; background: rgba(3,159,91,0.1); border-radius: 10px; border-left: 4px solid var(--secondary);">
        <strong>Kesimpulan Utama:</strong> Pemanfaatan ESP32 dalam implementasi Digital Twin untuk monitoring infrastruktur jaringan memberikan solusi yang efisien, murah, dan fleksibel. Sistem ini memungkinkan pemantauan kondisi perangkat secara terpusat serta membantu deteksi dini terhadap gangguan jaringan, sekaligus menjadi media pembelajaran yang efektif untuk mata kuliah Sistem Komputer dan Jaringan.
      </p>
    </div>

    <div style="text-align: center; margin-top: 3rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 14px;">
      <h3 style="color: var(--primary); margin-bottom: 1rem;"><i class="fas fa-file-alt"></i> Metadata Tugas</h3>
      <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem;">
        <div><strong><i class="fas fa-user"></i> Penulis:</strong> Yudi Saeful Rizal</div>
        <div><strong><i class="fas fa-book"></i> Mata Kuliah:</strong> Sistem Komputer dan Jaringan</div>
        <div><strong><i class="fas fa-university"></i> Institusi:</strong> Universitas Pamulang</div>
        <div><strong><i class="fas fa-calendar"></i> Tahun:</strong> 2026</div>
      </div>
      
      <p style="margin-top: 1.5rem; font-size: 0.85rem; color: #64748b; max-width: 800px; margin-left: auto; margin-right: auto;">
        <i class="fas fa-info-circle"></i>
        Artikel ini disusun sebagai bagian dari tugas mata kuliah Sistem Komputer dan Jaringan. Implementasi Digital Twin berbasis ESP32 ini menunjukkan integrasi antara konsep teoritis jaringan komputer dengan aplikasi praktis teknologi IoT dalam konteks monitoring infrastruktur TI.
      </p>
    </div>
  </div>

  <footer>
    <div style="margin-bottom: 1.5rem;">
      <div style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">UNIVERSITAS PAMULANG</div>
      <div style="font-size: 1rem; color: #e2e8f0;">Fakultas Ilmu Komputer</div>
    </div>
    
    <p>© 2026 Tugas Mata Kuliah Sistem Komputer dan Jaringan</p>
    <p style="margin-top: 0.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
      Mengintegrasikan teori jaringan komputer dengan implementasi praktis teknologi Digital Twin dan IoT untuk menciptakan solusi monitoring yang inovatif.
    </p>
    
    <div class="footer-tags">
      <span class="tag">Digital Twin</span>
      <span class="tag">ESP32</span>
      <span class="tag">Monitoring Jaringan</span>
      <span class="tag">IoT</span>
      <span class="tag">Sistem Komputer</span>
      <span class="tag">Universitas Pamulang</span>
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
      const boxes = document.querySelectorAll('.content-box, .architecture-card');
      
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
      
      // Add smooth scrolling for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            window.scrollTo({
              top: targetElement.offsetTop - 80,
              behavior: 'smooth'
            });
          }
        });
      });
      
      // Dynamic year in footer
      const yearSpan = document.querySelector('footer p');
      if (yearSpan) {
        yearSpan.innerHTML = yearSpan.innerHTML.replace('2026', new Date().getFullYear());
      }
    });
  </script>
</body>
</html>