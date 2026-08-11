<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaid;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class MidtransController extends Controller
{
    public function notification(Request $request): Response
    {
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $paymentType = $request->input('payment_type');

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.config('midtrans.server_key'));

        abort_unless(hash_equals($expectedSignature, $signatureKey), 403, 'Invalid signature');

        $order = Order::where('order_number', $orderId)->first();

        abort_unless($order, 404);

        // Guard idempotensi paling awal — Midtrans bisa kirim notifikasi lebih dari sekali.
        if ($order->status === 'paid') {
            return response('OK');
        }

        if (in_array($transactionStatus, ['settlement', 'capture'], true)) {
            $order->update([
                'status' => 'paid',
                'payment_type' => $paymentType,
                'paid_at' => now(),
            ]);

            // Akun klien sudah dibuat & login sejak checkout (CheckoutController), bukan di sini —
            // supaya pembeli yang keluar-masuk sebelum bayar tetap dikenali dan bisa lanjutkan
            // pembayaran, bukan bikin order/akun duplikat.
            Mail::to($order->buyer_email)->send(new OrderPaid($order));
        } elseif ($transactionStatus === 'expire') {
            $order->update(['status' => 'expired']);
        } elseif (in_array($transactionStatus, ['deny', 'cancel'], true)) {
            $order->update(['status' => $transactionStatus === 'deny' ? 'failed' : 'cancelled']);
        }

        return response('OK');
    }
}
