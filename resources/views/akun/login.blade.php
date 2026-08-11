@extends('layouts.app')

@section('title', 'Login - NUSCODE')

@section('content')
<section style="max-width: 420px;">
  <h1>Login</h1>

  @if($errors->any())
    <p style="color: #c0392b; margin-bottom: 1rem;">{{ $errors->first() }}</p>
  @endif

  <form method="POST" action="{{ route('akun.login.attempt') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required
           style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
    <input type="password" name="password" placeholder="Password" required
           style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
    <button type="submit" class="btn" style="width:100%;">Login</button>
  </form>

  <p style="margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-light);">
    Lupa password? Hubungi kami via
    <a href="https://wa.me/{{ config('midtrans.whatsapp_admin_number') }}" target="_blank">WhatsApp</a>.
  </p>

  <p style="margin-top: 0.8rem; font-size: 0.9rem; color: var(--text-light); border-top: 1px solid rgba(0,0,0,0.08); padding-top: 1rem;">
    Belum punya akun? Tidak perlu daftar manual — akun Anda otomatis dibuat begitu pertama kali membeli produk.
    <a href="{{ route('products.index') }}">Lihat Produk →</a>
  </p>
</section>
@endsection
