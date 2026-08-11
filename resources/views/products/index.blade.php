@extends('layouts.app')

@section('title', 'Produk - NUSCODE')
@section('description', 'Jasa pembuatan aplikasi dan prompt AI dari NUSCODE.')

@section('content')
<section>
  <h1>Produk</h1>
  <div class="card-grid">
    <a href="{{ route('aplikasi.index') }}" class="card">
      <h3><i class="fas fa-laptop-code"></i> Jasa Pembuatan Aplikasi</h3>
      <p>Lihat portofolio aplikasi yang sudah kami buat, dan pesan aplikasi custom untuk kebutuhan Anda.</p>
    </a>
    <a href="{{ route('products.prompt.index') }}" class="card">
      <h3><i class="fas fa-comment-dots"></i> Prompt</h3>
      <p>Prompt AI siap pakai untuk berbagai kebutuhan.</p>
    </a>
  </div>
</section>
@endsection
