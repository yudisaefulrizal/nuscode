<x-mail::message>
# Akun Anda Sudah Siap

Terima kasih sudah checkout di NUSCODE. Kami buatkan akun untuk Anda supaya bisa lihat status pesanan dan produk yang sudah dibeli kapan saja di halaman "Aset Saya" — termasuk melanjutkan pembayaran kalau belum selesai.

- **Email:** {{ $user->email }}
- **Password:** {{ $password }}

<x-mail::button :url="route('akun.login')">
Login ke Akun Saya
</x-mail::button>

Kalau ada kendala, hubungi kami langsung:

<x-mail::button :url="$whatsappUrl">
Hubungi via WhatsApp
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
