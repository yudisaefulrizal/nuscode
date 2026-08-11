<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'status' => $order->status,
            'download_url' => $order->status === 'paid'
                ? route('unduh.show', $order->download_token)
                : null,
            'whatsapp_url' => 'https://wa.me/'.config('midtrans.whatsapp_admin_number')
                .'?text='.rawurlencode('Halo, saya butuh bantuan untuk order '.$order->order_number),
        ]);
    }
}
