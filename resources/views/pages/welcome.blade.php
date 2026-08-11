@extends('layouts.app')

@section('title', 'NUSCODE – Kode dari Nusantara')
@section('description', 'NUSCODE adalah pengembang teknologi dari Nusantara yang menghadirkan solusi digital mudah digunakan, cepat dikembangkan, dan memberikan nilai nyata bagi bisnis dan masyarakat.')

@push('styles')
  /* HERO — spesifik halaman utama, tidak dipakai halaman lain */
  .hero {
    padding: 4.5rem 5%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    position: relative;
    overflow: hidden;
  }

  .hero-inner {
    display: grid;
    grid-template-columns: minmax(0, 0.9fr) minmax(360px, 1.1fr);
    gap: clamp(2rem, 5vw, 4.5rem);
    align-items: center;
    position: relative;
    z-index: 1;
  }

  .hero-copy {
    text-align: left;
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
    color: white;
    font-size: clamp(2.1rem, 4vw, 3.35rem);
    font-weight: 700;
    line-height: 1.15;
    margin-bottom: 1rem;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
  }

  .hero p {
    font-size: 1.08rem;
    margin-top: 1rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
    line-height: 1.6;
    max-width: 36rem;
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
    text-decoration: none;
  }

  .hero-visual {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 24px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.32);
    background: #101614;
    isolation: isolate;
  }

  .hero-visual::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 0 45px rgba(3, 159, 91, 0.16);
    pointer-events: none;
  }

  .hero-visual img {
    display: block;
    width: 100%;
    height: auto;
    aspect-ratio: 3 / 2;
    object-fit: cover;
  }

  /* Section konten halaman utama pakai padding lebih besar dari default `section` situs */
  .content { padding: 4rem 5%; }

  h2 {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    position: relative;
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

  #tentang {
    padding-bottom: 0.5rem;
  }

  #tentang blockquote {
    margin-bottom: 0;
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

  /* ARAH */
  #arah {
    background: #ffffff;
    padding: 0.5rem 5% 4rem;
    text-align: center;
  }

  #arah .narrative {
    max-width: 850px;
    margin: 0 auto;
    color: var(--text-light);
    font-size: 1.1rem;
    line-height: 1.8;
  }

  .direction-visual {
    max-width: 950px;
    margin: 0 auto 2.25rem;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 20px;
    box-shadow: 0 18px 45px rgba(3, 159, 91, 0.1);
  }

  .direction-visual img {
    display: block;
    width: 100%;
    height: auto;
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

  @media (max-width: 1024px) {
    .hero { padding: 4rem 4%; }
    .hero-inner { grid-template-columns: minmax(0, 0.9fr) minmax(320px, 1.1fr); gap: 2rem; }
    section { padding: 3.5rem 5%; }
    .portfolio-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
  }

  @media (max-width: 768px) {
    .hero { padding: 2.75rem 4%; }
    .hero-inner {
      grid-template-columns: minmax(0, 1.05fr) minmax(150px, 0.95fr);
      gap: 1.25rem;
    }
    .hero-copy { text-align: left; }
    .hero-visual { width: 100%; margin: 0; border-radius: 18px; }
    .hero-visual img { aspect-ratio: 4 / 5; object-fit: cover; object-position: 67% center; }
    .hero h1 { font-size: 1.45rem; }
    .hero p { font-size: 0.88rem; }
    .hero .cta { margin-top: 1.4rem; padding: 0.65rem 1.15rem; font-size: 0.78rem; }
    h2 { font-size: 1.6rem; }
    .direction-visual { margin: 0 auto 1.75rem; border-radius: 16px; }
    .portfolio { padding: 3rem 4%; }
    .portfolio-grid { grid-template-columns: 1fr; gap: 1.5rem; }
    .project-card { padding: 1.5rem; }
    #arah .narrative { font-size: 1rem; }
  }

  @media (max-width: 480px) {
    .hero { padding: 2rem 4%; }
    .hero-inner {
      grid-template-columns: minmax(0, 1.08fr) minmax(125px, 0.92fr);
      gap: 0.8rem;
    }
    .hero h1 { font-size: 1.22rem; line-height: 1.2; }
    .hero p { font-size: 0.75rem; line-height: 1.5; margin-top: 0.7rem; }
    .hero-visual { border-radius: 14px; }
    .hero .cta { margin-top: 1rem; padding: 0.58rem 0.82rem; font-size: 0.68rem; }
    #arah { padding: 0 4% 2.75rem; }
    .direction-visual { margin: 0 auto 1.5rem; border-radius: 12px; }
    #arah .narrative { font-size: 0.95rem; line-height: 1.7; }
    section { padding: 3rem 4%; }
    h2 { font-size: 1.5rem; }
    .portfolio h2 { font-size: 1.7rem; }
    .icon { font-size: 2.5rem; }
    .info h3 { font-size: 1.2rem; }
  }

  [data-animate] {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease;
  }

  [data-animate].visible {
    opacity: 1;
    transform: translateY(0);
  }

  .text-center { text-align: center; }
@endpush

@section('content')
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-copy">
        <h1>Kode dari Nusantara, untuk Dunia 🌏</h1>
        <p>Simple Code. Global Impact.</p>
        <a href="#portfolio" class="cta">Jelajahi Karya Kami</a>
      </div>

      <div class="hero-visual" aria-hidden="true">
        <picture>
          <source srcset="{{ asset('images/nuscode-ai-developer-hero.webp') }}" type="image/webp">
          <img
            src="{{ asset('images/nuscode-ai-developer-hero.png') }}"
            alt=""
            width="1536"
            height="1024"
            fetchpriority="high"
          >
        </picture>
      </div>
    </div>
  </section>

  <section id="tentang" class="content" data-animate>
    <h2>Tentang NUSCODE</h2>
    <p>Setiap baris kode yang kami tulis membawa nilai efisiensi, kesederhanaan, dan kebermanfaatan — mencerminkan keseimbangan antara logika teknologi dan nilai-nilai manusiawi.</p>
    <blockquote><em>"Kode dari Nusantara, untuk dunia."</em></blockquote>
  </section>

  <section id="arah" class="content" data-animate>
    <div class="direction-visual">
      <picture>
        <source
          media="(max-width: 480px)"
          srcset="{{ asset('images/nuscode-simple-digital-solutions-mobile.png') }}"
        >
        <img
          src="{{ asset('images/nuscode-simple-digital-solutions.png') }}"
          alt="Ilustrasi teknologi dan AI yang disederhanakan menjadi solusi digital untuk bisnis, pendidikan, dan masyarakat"
          width="1200"
          height="724"
          loading="lazy"
        >
      </picture>
    </div>

    <div class="narrative">
      <p>
        Kami percaya bahwa teknologi terbaik lahir dari kesederhanaan.
        kami membangun solusi digital yang cepat, ringan, dan bermakna —
        membantu bisnis, lembaga, dan masyarakat bergerak lebih efisien.
      </p>
    </div>
  </section>

  <!-- PORTOFOLIO — daftar Jasa Pembuatan Aplikasi (dari database, model Aplikasi) -->
  <section id="portfolio" class="portfolio" data-animate>
    <h2>Portofolio Kami</h2>

    @if($apps->isEmpty())
      <p>Belum ada portofolio ditampilkan.</p>
    @else
      <div class="portfolio-grid">
        @foreach($apps as $app)
          <div class="project-card">
            <div class="icon">
              <i class="fas fa-laptop-code"></i>
            </div>
            <div class="info">
              <h3>{{ $app->name }}</h3>
              <p>{{ $app->description }}</p>
              @if($app->demo_url)
                <a href="{{ $app->demo_url }}" target="_blank">Kunjungi website →</a>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <p class="text-center" style="margin-top: 2rem;">
      <a href="{{ route('aplikasi.index') }}">Lihat semua & pesan Jasa Pembuatan Aplikasi →</a>
    </p>
  </section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const animateElements = document.querySelectorAll('[data-animate]');
    const onScroll = () => {
      const triggerBottom = window.innerHeight * 0.85;
      animateElements.forEach(el => {
        const boxTop = el.getBoundingClientRect().top;
        if (boxTop < triggerBottom) el.classList.add('visible');
      });
    };
    window.addEventListener('scroll', onScroll);
    onScroll();
  });
</script>
@endpush
