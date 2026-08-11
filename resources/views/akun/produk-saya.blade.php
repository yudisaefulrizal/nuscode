@extends('layouts.app')

@section('title', 'Aset Saya - NUSCODE')

@section('content')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<section>
  <h1>Aset Saya</h1>

  @if(session('status'))
    <p style="background:#e6f7ee; color:#027a47; padding:0.8rem 1rem; border-radius:8px; margin-bottom:1.5rem;">{{ session('status') }}</p>
  @endif

  @if($pendingOrders->isNotEmpty())
    <h2>Menunggu Pembayaran</h2>
    <div class="card-grid">
      @foreach($pendingOrders as $order)
        <div class="card">
          <h3>{{ $order->orderable->name ?? 'Produk' }}</h3>
          <p><small>Dibuat {{ $order->created_at->translatedFormat('d M Y H:i') }}</small></p>
          <p class="price">Rp{{ number_format($order->amount, 0, ',', '.') }}</p>
          <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
            @if($order->productType())
              <button type="button" class="btn" onclick="ResumePayment.start('{{ $order->productType() }}', '{{ $order->orderable->slug }}', '{{ $order->order_number }}')">Lanjutkan Bayar</button>
            @endif
            <form method="POST" action="{{ route('akun.pesanan.batalkan', $order->order_number) }}" onsubmit="return confirm('Batalkan pesanan ini?');">
              @csrf
              <button type="submit" style="background:none; border:1px solid #c0392b; color:#c0392b; padding:0.8rem 1.5rem; border-radius:50px; cursor:pointer;">Batalkan Pesanan</button>
            </form>
          </div>
          <div id="resume-status-{{ $order->order_number }}" style="margin-top:0.8rem;"></div>
        </div>
      @endforeach
    </div>
  @endif

  <h2 style="margin-top: 2.5rem;">Produk Dibeli</h2>
  @if($orders->isEmpty())
    <div class="empty-state">
      <p>Anda belum memiliki produk yang dibeli.</p>
      <a href="{{ route('products.index') }}" class="btn" style="margin-top:1rem;">Lihat Produk</a>
    </div>
  @else
    <div class="card-grid">
      @foreach($orders as $order)
        <div class="card">
          <h3>{{ $order->orderable->name }}</h3>
          <p><small>Dibeli {{ $order->paid_at->translatedFormat('d M Y') }}</small></p>
          <a href="{{ route('unduh.show', $order->download_token) }}" class="btn" style="margin-top:1rem;">Unduh</a>
        </div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('akun.logout') }}" style="margin-top: 2rem;">
    @csrf
    <button type="submit" style="background:none; border:1px solid var(--text-light); padding:0.6rem 1.5rem; border-radius:50px; cursor:pointer;">Logout</button>
  </form>
</section>

<script>
const ResumePayment = {
  csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,

  async start(type, slug, orderNumber) {
    const statusEl = document.getElementById(`resume-status-${orderNumber}`);
    statusEl.textContent = 'Menyiapkan pembayaran...';

    const res = await fetch(`/checkout/${type}/${slug}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, Accept: 'application/json' },
      body: JSON.stringify({}),
    });
    const data = await res.json();

    if (!data.snap_token) {
      statusEl.textContent = 'Gagal membuka pembayaran. Coba lagi.';
      return;
    }

    statusEl.textContent = '';

    snap.pay(data.snap_token, {
      onSuccess: () => this.poll(orderNumber),
      onPending: () => this.poll(orderNumber),
      onError: () => { statusEl.textContent = 'Pembayaran gagal. Coba lagi atau hubungi kami via WhatsApp.'; },
      onClose: () => { statusEl.textContent = 'Pembayaran dibatalkan. Klik "Lanjutkan Bayar" lagi kapan saja.'; },
    });
  },

  async poll(orderNumber, attempt = 0) {
    const statusEl = document.getElementById(`resume-status-${orderNumber}`);
    const res = await fetch(`/api/order/${orderNumber}/status`);
    const data = await res.json();

    if (data.status === 'paid') {
      statusEl.innerHTML = 'Pembayaran berhasil! Memuat ulang halaman...';
      location.reload();
      return;
    }

    if (['failed', 'cancelled', 'expired'].includes(data.status)) {
      statusEl.innerHTML = `Pembayaran tidak selesai. <a href="${data.whatsapp_url}" target="_blank">Hubungi via WhatsApp</a>`;
      return;
    }

    if (attempt < 15) {
      setTimeout(() => this.poll(orderNumber, attempt + 1), 2000);
    } else {
      statusEl.textContent = 'Menunggu konfirmasi lebih lama dari biasanya. Coba refresh halaman ini sebentar lagi.';
    }
  },
};
</script>
@endsection
