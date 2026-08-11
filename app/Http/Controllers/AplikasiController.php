<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use Illuminate\View\View;

class AplikasiController extends Controller
{
    public function index(): View
    {
        $apps = Aplikasi::orderBy('order')->orderByDesc('id')->get();

        return view('jasa-aplikasi.index', compact('apps'));
    }
}
