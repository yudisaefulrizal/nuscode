<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>NUSCODE – Kode dari Nusantara</title>

  <meta name="description" content="NUSCODE adalah pengembang teknologi dari Nusantara yang menghadirkan solusi digital mudah digunakan, cepat dikembangkan, dan memberikan nilai nyata bagi bisnis dan masyarakat." />
  <meta name="keywords" content="NUSCODE, software house Indonesia, developer aplikasi nusantara, solusi digital efisien" />

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('nuscode.png') }}">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />

  <!-- CSS Lokal Laravel -->
  <link rel="stylesheet" href="{{ asset('css/style_review_jurnal.css') }}">

  <style>
    :root {
      --primary: #343434;
      --secondary: #039f5b;
      --accent: #ffcc33;
      --text-dark: #222;
      --text-light: #555;
      --bg: #ffffff;
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
      overflow-x: hidden;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 8%;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header img.logo {
      height: 45px;
    }

    header nav a {
      text-decoration: none;
      color: var(--text-dark);
      margin-left: 2rem;
      font-weight: 500;
    }

    .hero {
      text-align: center;
      padding: 8rem 2rem;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
    }

    .hero p {
      font-size: 1.3rem;
      margin-top: 1rem;
      opacity: 0.9;
    }

    .hero .cta {
      display: inline-block;
      margin-top: 2rem;
      padding: 0.8rem 2rem;
      background: var(--accent);
      color: #222;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
    }

    section {
      padding: 5rem 8%;
      max-width: 1100px;
      margin: auto;
    }

    blockquote {
      border-left: 4px solid var(--accent);
      padding-left: 1rem;
      font-style: italic;
      margin-top: 2rem;
    }

    footer {
      background: linear-gradient(135deg, #0d1b2a, #1b263b);
      color: #eee;
      padding: 3rem 8%;
      text-align: center;
    }

    footer img {
      height: 45px;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body>

<header>
  <img src="{{ asset('nuscode.png') }}" alt="NUSCODE Logo" class="logo">
  <nav>
    <a href="/">Home <i class="fas fa-robot"></i></a>
  </nav>
</header>

<section class="hero">
  <h1></h1>
  <p>Aplikasi Review Jurnal</p>
  <a href="#kontak" class="cta">Simple Code. Global Impact.</a>
</section>

<section>
  <h2>upload atau drag file PDF Jurnal dibawah (sedang maintenance apss tidak bisa digunakan)</h2>

  <div class="article-content">
    <input type="file" id="pdfInput" accept="application/pdf">
    <button onclick="uploadPDF()">Upload & Analisis</button>

    <div id="loading" class="hidden">Memproses PDF...</div>


    <pre id="result"></pre>

    <button onclick="downloadResult()" id="downloadBtn" disabled>
      Download Hasil
    </button>
  </div>

  <blockquote>
    "Halaman ini membantu menganalisis dan merapikan jurnal."
  </blockquote>

  <p>
    .
  </p>
</section>

<footer>
  <img src="{{ asset('nuscode - white.png') }}" alt="NUSCODE Logo">
  <p>© 2025 NUSCODE — Teknologi dari Nusantara, untuk Dunia.</p>
</footer>

<!-- JS Lokal Laravel -->
<script src="{{ asset('js/review_jurnal.js') }}"></script>

<script>
  const animateElements = document.querySelectorAll('[data-animate]');

  const onScroll = () => {
    const triggerBottom = window.innerHeight * 0.85;
    animateElements.forEach(el => {
      if (el.getBoundingClientRect().top < triggerBottom) {
        el.classList.add('visible');
      }
    });
  };

  window.addEventListener('scroll', onScroll);
  onScroll();
</script>

</body>
</html>
