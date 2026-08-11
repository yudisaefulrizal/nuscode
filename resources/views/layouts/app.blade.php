<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'NUSCODE')</title>
  <meta name="description" content="@yield('description', 'NUSCODE - source code dan prompt AI dari Nusantara.')" />
  <link rel="icon" type="image/png" href="/nuscode.png" />
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
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Footer selalu menempel di bawah layar kalau konten pendek, tapi tetap
       mengikuti konten kalau lebih panjang dari tinggi layar. */
    .page-content { flex: 1 0 auto; }
    #site-footer { flex-shrink: 0; }

    /* Selector di-scope pakai #site-header/#site-footer (bukan bare header/footer/body)
       supaya specificity-nya selalu menang dibanding <style> bawaan konten artikel lama
       yang masih ikut ter-render (masih ada selector generik header/footer di dalamnya). */
    #site-header {
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

    #site-header img.logo {
      height: 45px;
      width: auto;
      transition: transform 0.3s ease;
    }

    #site-header .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--primary);
      cursor: pointer;
      z-index: 1001;
    }

    #site-header nav {
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
    }

    #site-header nav a {
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

    #site-header nav a:hover { color: var(--secondary); }

    #site-header nav a::after {
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

    #site-header nav a:hover::after { width: 100%; }

    .nav-dropdown { position: relative; }

    .nav-dropdown-toggle i { font-size: 0.7em; margin-left: 0.2rem; }

    .nav-dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background: #fff;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      border-radius: 10px;
      padding: 0.5rem 0;
      min-width: 220px;
      z-index: 1000;
    }

    .nav-dropdown:hover .nav-dropdown-menu { display: block; }

    #site-header .nav-dropdown-menu a {
      display: block;
      margin-left: 0 !important;
      padding: 0.6rem 1.2rem;
      white-space: nowrap;
    }

    #site-header .nav-dropdown-menu a:hover { background: #f5f5f5; }

    section {
      padding: 3rem 5%;
      max-width: 1100px;
      margin: auto;
    }

    /* Style dasar untuk link teks biasa di dalam konten (paragraf, dst) —
       tanpa ini, link seperti WhatsApp/"Lihat Produk" tampil biru+underline
       bawaan browser, tidak konsisten dengan desain situs. Link yang punya
       class sendiri (.btn, .card, nav, dsb) tidak terpengaruh (specificity
       lebih tinggi). */
    a {
      color: var(--secondary);
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    h1, h2 {
      color: var(--primary);
      font-weight: 700;
    }

    h1 { font-size: 2rem; margin-bottom: 1.5rem; }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .card {
      background: #fff;
      padding: 1.8rem;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      border: 1px solid rgba(0,0,0,0.03);
      transition: all 0.3s ease;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .card img { width: 100%; border-radius: 10px; margin-bottom: 1rem; object-fit: cover; aspect-ratio: 16/9; }
    .card h3 { color: var(--primary); margin-bottom: 0.5rem; }
    .card p { color: var(--text-light); font-size: 0.95rem; }
    .card .price { color: var(--secondary); font-weight: 700; margin-top: 0.5rem; }

    .btn {
      display: inline-block;
      padding: 0.8rem 2rem;
      background: var(--secondary);
      color: #fff;
      border: none;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn:hover { background: #027a47; transform: translateY(-2px); }

    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: var(--text-light);
    }

    #site-footer {
      background: linear-gradient(135deg, #0d1b2a, #1b263b);
      color: #e2e8f0;
      padding: 3rem 5% 2rem;
      text-align: center;
    }

    #site-footer img { height: 45px; margin-bottom: 1.2rem; filter: brightness(0) invert(1); opacity: 0.9; }
    #site-footer p { font-size: 0.9rem; opacity: 0.8; }

    @media (max-width: 768px) {
      #site-header { padding: 1rem 4%; }

      #site-header .menu-toggle { display: block; }

      #site-header nav {
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

      #site-header nav.active { display: flex; }

      #site-header nav a {
        margin: 0.7rem 0;
        margin-left: 0;
        width: 90%;
        padding: 0.8rem 1rem;
        text-align: center;
      }

      .nav-dropdown { width: 90%; }

      .nav-dropdown-menu {
        display: block;
        position: static;
        box-shadow: none;
        background: rgba(0,0,0,0.02);
        border-radius: 8px;
        width: 100%;
        padding: 0.3rem 0;
      }

      .nav-dropdown-menu a { text-align: center; width: 100%; margin: 0.3rem 0 !important; }
    }

    .article-toc {
      background: #f9fafb;
      border: 1px solid rgba(0,0,0,0.05);
      border-radius: 12px;
      padding: 1.2rem 1.5rem;
      margin-bottom: 2rem;
    }

    .article-toc ul { list-style: none; margin-top: 0.5rem; }
    .article-toc li { margin: 0.4rem 0; }
    .article-toc a { color: var(--secondary); text-decoration: none; }
    .article-toc a:hover { text-decoration: underline; }

    @stack('styles')
  </style>
</head>
<body>

  <header id="site-header">
    <a href="{{ route('home') }}"><img src="/nuscode.png" alt="NUSCODE Logo" class="logo" /></a>
    <button class="menu-toggle" id="menuToggle">
      <i class="fas fa-bars"></i>
    </button>
    <nav id="mainNav">
      <div class="nav-dropdown">
        <a href="{{ route('products.index') }}" class="nav-dropdown-toggle">Produk <i class="fas fa-chevron-down"></i></a>
        <div class="nav-dropdown-menu">
          <a href="{{ route('aplikasi.index') }}">Jasa Pembuatan Aplikasi</a>
          <a href="{{ route('products.prompt.index') }}">Prompt</a>
        </div>
      </div>
      <a href="{{ route('articles.index') }}">Artikel</a>
      @auth
        <a href="{{ route('akun.produk-saya') }}"><i class="fas fa-user"></i> Aset Saya</a>
      @else
        <a href="{{ route('akun.login') }}"><i class="fas fa-user"></i> Login</a>
      @endauth
    </nav>
  </header>

  <main class="page-content">
    @yield('content')
  </main>

  <footer id="site-footer">
    <img src="/nuscode - white.png" alt="NUSCODE Logo" />
    <p>&copy; <span id="footerYear">{{ date('Y') }}</span> NUSCODE — Teknologi dari Nusantara, untuk Dunia.</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.menu-toggle');
      const mainNav = document.querySelector('header nav');

      if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          mainNav.classList.toggle('active');
          const icon = menuToggle.querySelector('i');
          icon.classList.toggle('fa-bars');
          icon.classList.toggle('fa-times');
        });

        document.addEventListener('click', function(e) {
          if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
            mainNav.classList.remove('active');
          }
        });
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
