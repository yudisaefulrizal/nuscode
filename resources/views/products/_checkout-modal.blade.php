{{-- Modal checkout satu-halaman: form -> Snap QRIS -> polling -> unduh, tanpa pindah halaman (PRD §7.5) --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<button type="button" class="btn" onclick="document.getElementById('checkout-modal-{{ $type }}-{{ $product->slug }}').style.display='flex'">
  Beli — Rp{{ number_format($product->price, 0, ',', '.') }}
</button>

<div id="checkout-modal-{{ $type }}-{{ $product->slug }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding:1rem;">
  <div style="background:#fff; border-radius:15px; padding:2rem; max-width:420px; width:100%; max-height:90vh; overflow-y:auto;">
    <div id="checkout-step-form-{{ $type }}-{{ $product->slug }}">
      <h3 style="margin-bottom:1rem;">Beli {{ $product->name }}</h3>

      @auth
        <p style="margin-bottom:1rem;">Login sebagai <strong>{{ auth()->user()->email }}</strong></p>
        <button type="button" class="btn" style="width:100%;" onclick="NuscodeCheckout.startPayment('{{ $type }}', '{{ $product->slug }}')">Bayar dengan QRIS</button>
      @else
        <div id="checkout-guest-form-{{ $type }}-{{ $product->slug }}">
          <input type="text" id="ck-name-{{ $type }}-{{ $product->slug }}" placeholder="Nama" style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
          <input type="email" id="ck-email-{{ $type }}-{{ $product->slug }}" placeholder="Email" style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
          <input type="tel" id="ck-phone-{{ $type }}-{{ $product->slug }}" placeholder="No. HP (opsional)" style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
          <button type="button" class="btn" style="width:100%;" onclick="NuscodeCheckout.submitGuestForm('{{ $type }}', '{{ $product->slug }}')">Bayar dengan QRIS</button>
        </div>

        <div id="checkout-email-exists-{{ $type }}-{{ $product->slug }}" style="display:none;">
          <p style="margin-bottom:1rem;">Email Anda sudah terdaftar. Login untuk melanjutkan, atau lanjutkan pembayaran dan terima email dengan password baru.</p>
          <input type="password" id="ck-login-password-{{ $type }}-{{ $product->slug }}" placeholder="Password" style="width:100%; padding:0.7rem; margin-bottom:0.7rem; border:1px solid #ddd; border-radius:8px;">
          <button type="button" class="btn" style="width:100%; margin-bottom:0.5rem;" onclick="NuscodeCheckout.loginAndContinue('{{ $type }}', '{{ $product->slug }}')">Login</button>
          <button type="button" style="width:100%; background:none; border:1px solid var(--secondary); color:var(--secondary); padding:0.7rem; border-radius:50px; cursor:pointer;" onclick="NuscodeCheckout.continueAsGuest('{{ $type }}', '{{ $product->slug }}')">Lanjutkan & Kirim Password Baru</button>
        </div>
      @endauth
    </div>

    <div id="checkout-step-status-{{ $type }}-{{ $product->slug }}" style="display:none; text-align:center;">
      <p id="checkout-status-message-{{ $type }}-{{ $product->slug }}">Menunggu pembayaran...</p>
      <div id="checkout-result-{{ $type }}-{{ $product->slug }}"></div>
    </div>

    <button type="button" style="margin-top:1rem; background:none; border:none; color:var(--text-light); cursor:pointer;" onclick="document.getElementById('checkout-modal-{{ $type }}-{{ $product->slug }}').style.display='none'">Tutup</button>
  </div>
</div>

@once
<script>
const NuscodeCheckout = {
  csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,

  ids(type, slug) {
    return {
      form: document.getElementById(`checkout-step-form-${type}-${slug}`),
      guestForm: document.getElementById(`checkout-guest-form-${type}-${slug}`),
      emailExists: document.getElementById(`checkout-email-exists-${type}-${slug}`),
      statusStep: document.getElementById(`checkout-step-status-${type}-${slug}`),
      statusMessage: document.getElementById(`checkout-status-message-${type}-${slug}`),
      result: document.getElementById(`checkout-result-${type}-${slug}`),
      name: document.getElementById(`ck-name-${type}-${slug}`),
      email: document.getElementById(`ck-email-${type}-${slug}`),
      phone: document.getElementById(`ck-phone-${type}-${slug}`),
      loginPassword: document.getElementById(`ck-login-password-${type}-${slug}`),
    };
  },

  async submitGuestForm(type, slug) {
    const el = this.ids(type, slug);
    const name = el.name.value.trim();
    const email = el.email.value.trim();
    const phone = el.phone.value.trim();

    if (!name || !email) { alert('Nama dan email wajib diisi.'); return; }

    localStorage.setItem('nuscode_buyer_name', name);
    localStorage.setItem('nuscode_buyer_email', email);

    const data = await this.postCheckout(type, slug, { name, email, phone });

    if (data.email_exists) {
      el.guestForm.style.display = 'none';
      el.emailExists.style.display = 'block';
      return;
    }

    this.startPayment(type, slug, data);
  },

  async continueAsGuest(type, slug) {
    const el = this.ids(type, slug);
    const data = await this.postCheckout(type, slug, {
      name: el.name.value.trim(),
      email: el.email.value.trim(),
      phone: el.phone.value.trim(),
      confirm_new_password: true,
    });
    this.startPayment(type, slug, data);
  },

  async loginAndContinue(type, slug) {
    const el = this.ids(type, slug);
    const res = await fetch('{{ route('akun.login.attempt') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, Accept: 'application/json' },
      body: JSON.stringify({ email: el.email.value.trim(), password: el.loginPassword.value }),
    });

    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      alert(data.message || 'Email atau password salah.');
      return;
    }

    // Kasus normal (client): tetap di halaman produk ini (reload) supaya bisa
    // langsung lanjut bayar. Kasus admin (jarang, salah pakai form ini): ke /admin.
    const data = await res.json();
    if (data.redirect && data.redirect.includes('/admin')) {
      location.href = data.redirect;
    } else {
      location.reload();
    }
  },

  async postCheckout(type, slug, payload) {
    const res = await fetch(`/checkout/${type}/${slug}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, Accept: 'application/json' },
      body: JSON.stringify(payload),
    });
    return res.json();
  },

  async startPayment(type, slug, data) {
    if (!data) {
      data = await this.postCheckout(type, slug, {});
    }

    const el = this.ids(type, slug);
    el.form.style.display = 'none';
    el.statusStep.style.display = 'block';

    // Akun+password (kalau akun baru dibuat/direset) sudah dikirim ke email saat checkout —
    // tampilkan juga di sini segera, tidak perlu tunggu pembayaran selesai (§ resume pending order).
    if (data.new_password) {
      el.result.innerHTML = `<p>Akun Anda dibuat. Password: <strong>${data.new_password}</strong> (info yang sama sudah dikirim ke email Anda, dipakai untuk login ke "Aset Saya")</p>`;
    }

    snap.pay(data.snap_token, {
      onSuccess: () => this.poll(type, slug, data.order_number),
      onPending: () => this.poll(type, slug, data.order_number),
      onError: () => { el.statusMessage.textContent = 'Pembayaran gagal. Coba lagi atau hubungi kami via WhatsApp.'; },
      onClose: () => { el.statusMessage.textContent = 'Pembayaran dibatalkan. Anda bisa lanjutkan lagi lewat halaman "Aset Saya" atau klik Beli lagi.'; },
    });
  },

  async poll(type, slug, orderNumber, attempt = 0) {
    const el = this.ids(type, slug);
    const res = await fetch(`/api/order/${orderNumber}/status`);
    const data = await res.json();

    if (data.status === 'paid') {
      const html = `<a href="${data.download_url}" class="btn" style="margin-bottom:0.5rem;">Unduh Sekarang</a>
        <p><a href="${data.whatsapp_url}" target="_blank">Butuh bantuan? Hubungi via WhatsApp</a></p>`;
      el.result.innerHTML += html;
      el.statusMessage.textContent = 'Pembayaran berhasil!';
      return;
    }

    if (['failed', 'cancelled', 'expired'].includes(data.status)) {
      el.statusMessage.textContent = 'Pembayaran tidak selesai. Coba lagi atau hubungi kami via WhatsApp.';
      el.result.innerHTML = `<a href="${data.whatsapp_url}" target="_blank">Hubungi via WhatsApp</a>`;
      return;
    }

    if (attempt < 15) {
      setTimeout(() => this.poll(type, slug, orderNumber, attempt + 1), 2000);
    } else {
      el.statusMessage.textContent = 'Menunggu konfirmasi pembayaran lebih lama dari biasanya. Cek email Anda atau hubungi kami via WhatsApp.';
    }
  },
};

document.addEventListener('DOMContentLoaded', () => {
  const savedName = localStorage.getItem('nuscode_buyer_name');
  const savedEmail = localStorage.getItem('nuscode_buyer_email');
  document.querySelectorAll('[id^="ck-name-"]').forEach(input => { if (savedName) input.value = savedName; });
  document.querySelectorAll('[id^="ck-email-"]').forEach(input => { if (savedEmail) input.value = savedEmail; });
});
</script>
@endonce
