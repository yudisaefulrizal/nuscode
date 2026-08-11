<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Transaction;

class DashboardController extends Controller
{
    public function index(): View
    {
        $paidOrders = Auth::user()->orders()
            ->where('status', 'paid')
            ->with('orderable')
            ->latest('paid_at')
            ->get();

        $pendingOrders = Auth::user()->orders()
            ->where('status', 'pending')
            ->with('orderable')
            ->latest()
            ->get();

        return view('akun.produk-saya', [
            'orders' => $paidOrders,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($order->status === 'pending', 422);

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');

        try {
            Transaction::cancel($order->order_number);
        } catch (\Throwable $e) {
            // Transaksi mungkin belum pernah benar-benar dibuka di Midtrans (Snap belum
            // diklik/dipindai sama sekali) — tidak masalah, tetap lanjut batalkan di sisi kita.
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('akun.produk-saya')->with('status', 'Pesanan berhasil dibatalkan.');
    }
}
