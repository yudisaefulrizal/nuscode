<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function download(Order $order): StreamedResponse
    {
        abort_unless($order->status === 'paid', 404);

        $product = $order->orderable;

        abort_unless($product && $product->file_path, 404);

        return Storage::disk('local')->download($product->file_path);
    }
}
