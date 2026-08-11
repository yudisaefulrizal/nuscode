<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $apps = Aplikasi::orderBy('order')->orderByDesc('id')->get();

        return view('pages.welcome', compact('apps'));
    }
}
