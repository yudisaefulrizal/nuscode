<x-mail::message>
# Pembayaran Berhasil

Terima kasih, pembayaran untuk order **{{ $order->order_number }}** sudah kami terima.

<x-mail::button :url="$downloadUrl">
Unduh Produk
</x-mail::button>

Produk ini juga bisa Anda unduh kapan saja lewat halaman "Aset Saya" di akun Anda.

Kalau email ini tidak ada link/tombol yang bisa diklik, atau ada kendala apa pun, hubungi kami langsung:

<x-mail::button :url="$whatsappUrl">
Hubungi via WhatsApp
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
