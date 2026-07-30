<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NUSCODE – Kode dari Nusantara</title>
  <meta name="description" content="NUSCODE adalah pengembang teknologi dari Nusantara yang menghadirkan solusi digital mudah digunakan, cepat dikembangkan, dan memberikan nilai nyata bagi bisnis dan masyarakat." />
  <meta name="keywords" content="NUSCODE, software house Indonesia, developer aplikasi nusantara, solusi digital efisien, Indonesian tech developer, simple and fast app development" />
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
      background: var(--bg);
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
      backdrop-filter: blur(8px);
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

    /* HERO */
    .hero {
      text-align: center;
      padding: 6rem 5% 5rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      position: relative;
      overflow: hidden;
    }

    .hero::after {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 10%, transparent 70%);
      animation: float 10s infinite linear;
      pointer-events: none;
    }

    @keyframes float {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .hero h1 {
      font-size: 2.5rem;
      font-weight: 700;
      line-height: 1.3;
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .hero p {
      font-size: 1.2rem;
      margin-top: 1rem;
      opacity: 0.9;
      position: relative;
      z-index: 1;
      line-height: 1.6;
    }

    .hero .cta {
      display: inline-block;
      margin-top: 2rem;
      padding: 0.8rem 2rem;
      background: var(--accent);
      color: #222;
      border-radius: 50px;
      font-weight: 600;
      transition: transform 0.3s ease, background 0.3s;
      text-decoration: none;
      position: relative;
      z-index: 1;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .hero .cta:hover {
      transform: translateY(-4px);
      background: #ffd84d;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    /* SECTIONS */
    section {
      padding: 4rem 5%;
      max-width: 1100px;
      margin: auto;
    }

    h2 {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
      position: relative;
      font-weight: 700;
    }

    h2::after {
      content: "";
      width: 60px;
      height: 4px;
      background: var(--accent);
      position: absolute;
      bottom: -8px;
      left: 0;
      border-radius: 2px;
    }

    .content p {
      color: var(--text-light);
      margin-bottom: 1rem;
      font-size: 1.05rem;
      line-height: 1.7;
    }

    blockquote {
      border-left: 4px solid var(--accent);
      padding-left: 1.2rem;
      color: var(--text-dark);
      font-style: italic;
      margin: 1.5rem 0;
      background: rgba(255, 204, 51, 0.05);
      padding: 1.2rem;
      border-radius: 8px;
    }

    /* VALUES */
    .values {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
      margin-top: 2.5rem;
    }

    .value-card {
      background: #fff;
      padding: 1.8rem;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      text-align: center;
      transition: all 0.4s ease;
      border: 1px solid rgba(0,0,0,0.03);
    }

    .value-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .value-card h3 {
      color: var(--primary);
      margin-bottom: 0.8rem;
      font-size: 1.3rem;
    }

    .value-card p {
      color: var(--text-light);
      font-size: 0.95rem;
    }

    /* ARAH SECTION */
    #arah {
      background: #ffffff;
      padding: 4rem 5%;
      text-align: center;
    }
    
    #arah h2 {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 2rem;
      font-weight: 700;
      text-align: center;
    }
    
    #arah h2::after {
      left: 50%;
      transform: translateX(-50%);
    }
    
    #arah .narrative {
      max-width: 850px;
      margin: 0 auto;
      color: var(--text-light);
      font-size: 1.1rem;
      line-height: 1.8;
    }
    
    #arah strong {
      color: var(--primary);
      font-weight: 600;
    }

    /* PORTFOLIO */
    .portfolio {
      text-align: center;
      padding: 4rem 5%;
      background: #f9fafb;
    }
    
    .portfolio h2 {
      font-size: 2rem;
      margin-bottom: 2.5rem;
      color: var(--primary);
      text-align: center;
    }
    
    .portfolio h2::after {
      left: 50%;
      transform: translateX(-50%);
    }
    
    .portfolio-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      justify-content: center;
      align-items: stretch;
    }
    
    .project-card {
      background: #fff;
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.06);
      transition: all 0.4s ease;
      border: 1px solid rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    
    .project-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .icon {
      font-size: 3rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
    }
    
    .info h3 {
      font-size: 1.3rem;
      color: var(--primary);
      margin-bottom: 1rem;
      font-weight: 700;
    }
    
    .info p {
      font-size: 1rem;
      color: var(--text-light);
      line-height: 1.7;
      margin-bottom: 1rem;
      flex-grow: 1;
    }
    
    .project-card .info a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
      display: inline-block;
      margin-top: 0.5rem;
    }
    
    .project-card .info a:hover {
      color: var(--secondary);
      text-decoration: underline;
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

    /* Responsive Styles */
    @media (max-width: 1024px) {
      .hero h1 {
        font-size: 2.2rem;
      }
      
      section {
        padding: 3.5rem 5%;
      }
      
      .portfolio-grid {
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
      
      .hero {
        padding: 5rem 4% 4rem;
      }
      
      .hero h1 {
        font-size: 1.9rem;
      }
      
      .hero p {
        font-size: 1.1rem;
      }
      
      h2 {
        font-size: 1.6rem;
      }
      
      .values {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.2rem;
      }
      
      .value-card {
        padding: 1.5rem;
      }
      
      .portfolio {
        padding: 3rem 4%;
      }
      
      .portfolio-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      
      .project-card {
        padding: 1.5rem;
      }
      
      #arah .narrative {
        font-size: 1rem;
      }
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 1.7rem;
      }
      
      .hero p {
        font-size: 1rem;
      }
      
      .hero .cta {
        padding: 0.7rem 1.5rem;
        font-size: 0.9rem;
      }
      
      section {
        padding: 3rem 4%;
      }
      
      .values {
        grid-template-columns: 1fr;
      }
      
      h2 {
        font-size: 1.5rem;
      }
      
      .portfolio h2 {
        font-size: 1.7rem;
      }
      
      .icon {
        font-size: 2.5rem;
      }
      
      .info h3 {
        font-size: 1.2rem;
      }
      
      footer {
        padding: 2.5rem 4% 2rem;
      }
      
      header nav {
        top: 65px;
      }
    }

    /* ANIMATIONS */
    [data-animate] {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s ease;
    }

    [data-animate].visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Utility classes */
    .text-center {
      text-align: center;
    }
  </style>
</head>
<body>

  <header>
    <img src="nuscode.png" alt="NUSCODE Logo" class="logo" />
    <button class="menu-toggle" id="menuToggle">
      <i class="fas fa-bars"></i>
    </button>
    <nav id="mainNav">
      <a href="/artikel/osi-layers">OSI Layers</a>
      <a href="/artikel/Cyber-Security-Digital-Twin">Security DT</a>
      <a href="/review-jurnal">Review Jurnal Apps</a>
      <a href="/chatbot"><i class="fas fa-robot"></i> Chatbot</a>
    </nav>
  </header>

  <section class="hero">
    <h1>Kode dari Nusantara, untuk Dunia 🌏</h1>
    <p>Simple Code. Global Impact.</p>
    <a href="#kontak" class="cta">Comming Soon</a>
  </section>

  <section id="tentang" class="content" data-animate>
    <h2>Tentang NUSCODE</h2>
    <p><strong>NUSCODE</strong> berasal dari kata "Nusantara Code" yang berarti "Kode dari Nusantara". Kami adalah pengembang teknologi yang lahir dari tanah Nusantara dengan semangat global.</p>
    <p>Setiap baris kode yang kami tulis membawa nilai efisiensi, kesederhanaan, dan kebermanfaatan — mencerminkan keseimbangan antara logika teknologi dan nilai-nilai manusiawi.</p>
    <blockquote><em>"Kode dari Nusantara, untuk dunia."</em></blockquote>
  </section>

  <section id="arah" class="content" data-animate>
    <h2>Arah & Visi</h2>
    <div class="narrative">
      <p>
        Kami percaya bahwa teknologi terbaik lahir dari kesederhanaan.  
        Dari Nusantara, kami membangun solusi digital yang cepat, ringan, dan bermakna —
        membantu bisnis, lembaga, dan masyarakat bergerak lebih efisien.
      </p>
    </div>
  </section>

  <section id="nilai" class="content" data-animate>
    <h2>Core Values</h2>
    <div class="values">
      <div class="value-card">
        <h3>Simplicity</h3>
        <p>Produk mudah dipahami dan digunakan.</p>
      </div>
      <div class="value-card">
        <h3>Speed</h3>
        <p>Cepat dan berperforma tinggi.</p>
      </div>
      <div class="value-card">
        <h3>Usefulness</h3>
        <p>Berorientasi pada manfaat nyata.</p>
      </div>
      <div class="value-card">
        <h3>Integrity</h3>
        <p>Didasarkan pada kejujuran dan tanggung jawab.</p>
      </div>
      <div class="value-card">
        <h3>Innovation</h3>
        <p>Terus menciptakan cara baru untuk menyelesaikan masalah terupdate.</p>
      </div>
    </div>
  </section>

  <!-- PORTOFOLIO -->
  <section id="portfolio" class="portfolio" data-animate>
    <h2>Portofolio Kami</h2>
    <div class="portfolio-grid">
      
      <!-- Mahad Tahfizh Al Yusro -->
      <div class="project-card">
        <div class="icon">
          <i class="fas fa-mosque"></i>
        </div>
        <div class="info">
          <h3>Mahad Tahfizh Al Yusro</h3>
          <p>
            Website lengkap untuk Ma'had Tahfizh Al Yusro yang mencakup: pendaftaran online, tracking hafalan santri, dashboard guru & wali, berita & artikel lembaga, manajemen sertifikat & ijazah, jadwal sholat, monitoring jaringan & device, kalender akademik, SEO (optimasi mesin pencari), serta fitur administrasi santri lengkap. 
            Dikembangkan dengan fokus pada <strong>performa tinggi, kemudahan,</strong> dan <strong>kegunaan maksimal</strong> bagi pengajar, santri, dan wali santri.
          </p>
          <a href="https://mahadalyusro.sch.id" target="_blank">Kunjungi website →</a>
        </div>
      </div>

      <!-- Tambahkan project lainnya di sini jika ada -->
      <div class="project-card">
        <div class="icon">
          <i class="fas fa-laptop-code"></i>
        </div>
        <div class="info">
          <h3>Coming Soon</h3>
          <p>
            Kami sedang mengembangkan proyek-proyek baru yang akan segera diluncurkan. Tetap pantau perkembangan kami untuk inovasi teknologi terbaru dari Nusantara.
          </p>
          <a href="#kontak">Hubungi Kami →</a>
        </div>
      </div>

    </div>
  </section>

  <footer>
    <img src="nuscode - white.png" alt="NUSCODE Logo" />
    <p>© 2025 NUSCODE — Teknologi dari Nusantara, untuk Dunia.</p>
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
      
      // Scroll animation
      const animateElements = document.querySelectorAll('[data-animate]');
      const onScroll = () => {
        const triggerBottom = window.innerHeight * 0.85;
        animateElements.forEach(el => {
          const boxTop = el.getBoundingClientRect().top;
          if (boxTop < triggerBottom) el.classList.add('visible');
        });
      };
      
      window.addEventListener('scroll', onScroll);
      onScroll(); // Initial check
      
      // Dynamic year in footer
      const yearSpan = document.querySelector('footer p');
      if (yearSpan && yearSpan.textContent.includes('2025')) {
        yearSpan.innerHTML = yearSpan.innerHTML.replace('2025', new Date().getFullYear());
      }
    });
  </script>
</body>
</html>