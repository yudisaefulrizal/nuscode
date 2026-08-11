<?php

namespace App\Http\Controllers;

use App\Mail\AccountCredentials;
use App\Models\Order;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    /**
     * @var array<string, class-string>
     */
    private const TYPE_MAP = [
        'prompt' => Prompt::class,
    ];

    public function store(Request $request, string $type, string $slug): JsonResponse
    {
        $modelClass = self::TYPE_MAP[$type] ?? null;

        abort_unless($modelClass, 404);

        $product = $modelClass::where('slug', $slug)->firstOrFail();

        $newPassword = null;

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
            ]);

            $confirmNewPassword = $request->boolean('confirm_new_password');
            $existingUser = User::where('email', $validated['email'])->first();

            if ($existingUser && ! $confirmNewPassword) {
                return response()->json(['email_exists' => true]);
            }

            $newPassword = Str::random(10);

            if ($existingUser) {
                // Guest sudah pilih "Lanjutkan & Kirim Password Baru" (§7.8) — reset password akun lama.
                $existingUser->update(['password' => $newPassword]);
                $user = $existingUser;
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $newPassword,
                    'role' => 'client',
                ]);
            }

            // Login segera saat checkout (bukan setelah bayar) supaya kalau pembeli keluar
            // dan masuk lagi di browser yang sama, mereka masih dikenali dan bisa lihat/lanjutkan
            // order yang belum dibayar di halaman "Aset Saya" — bukan bikin order duplikat.
            Auth::login($user);
            $request->session()->regenerate();

            Mail::to($user->email)->send(new AccountCredentials($user, $newPassword));
        }

        $buyerPhone = $request->input('phone');

        // Cek order pending yang sudah ada untuk produk ini + akun ini, supaya tidak bikin
        // duplikat kalau pembeli sempat keluar halaman sebelum bayar lalu checkout ulang.
        $order = Order::where('user_id', $user->id)
            ->where('orderable_type', $modelClass)
            ->where('orderable_id', $product->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if (! $order) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'orderable_type' => $modelClass,
                'orderable_id' => $product->id,
                'user_id' => $user->id,
                'buyer_name' => $user->name,
                'buyer_email' => $user->email,
                'buyer_phone' => $buyerPhone,
                'amount' => $product->price,
                'status' => 'pending',
                'download_token' => Order::generateDownloadToken(),
            ]);
        }

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->amount,
            ],
            'enabled_payments' => ['other_qris'],
            'customer_details' => [
                'first_name' => $order->buyer_name,
                'email' => $order->buyer_email,
                'phone' => $order->buyer_phone,
            ],
            'item_details' => [[
                'id' => $product->slug,
                'price' => (int) $product->price,
                'quantity' => 1,
                'name' => $product->name,
            ]],
        ]);

        $order->update(['snap_token' => $snapToken]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_number' => $order->order_number,
            'new_password' => $newPassword,
        ]);
    }
}
