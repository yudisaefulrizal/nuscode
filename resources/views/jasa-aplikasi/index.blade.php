@extends('layouts.app')

@section('title', 'Jasa Pembuatan Aplikasi - NUSCODE')
@section('description', 'Jasa pembuatan aplikasi custom oleh NUSCODE — lihat portofolio aplikasi yang sudah kami buat.')

@section('content')
<section>
  <h1>Jasa Pembuatan Aplikasi</h1>
  <p>Berikut beberapa aplikasi yang sudah kami kembangkan. Butuh aplikasi serupa untuk bisnis atau lembaga Anda? Hubungi kami langsung.</p>

  <p style="margin: 1.5rem 0;">
    <a href="https://wa.me/{{ config('midtrans.whatsapp_admin_number') }}?text={{ urlencode('Halo, saya tertarik dengan jasa pembuatan aplikasi NUSCODE') }}" target="_blank" class="btn">
      Pesan Jasa Pembuatan Aplikasi
    </a>
  </p>

  @if($apps->isEmpty())
    <div class="empty-state">
      <p>Belum ada aplikasi yang ditampilkan.</p>
    </div>
  @else
    <div class="card-grid">
      @foreach($apps as $app)
        <div class="card">
          @if($app->image)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($app->image) }}" alt="{{ $app->name }}">
          @endif
          <h3>{{ $app->name }}</h3>
          <p>{{ $app->description }}</p>
          @if($app->tech_stack)
            <p><small>{{ $app->tech_stack }}</small></p>
          @endif
          @if($app->demo_url)
            <p><a href="{{ $app->demo_url }}" target="_blank">Kunjungi website →</a></p>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</section>
@endsection
