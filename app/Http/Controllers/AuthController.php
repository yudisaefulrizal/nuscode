<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('akun.login');
    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email atau password salah.'], 422);
            }

            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Form ini dipakai bersama untuk client & admin (satu form, satu guard 'web').
        // Daripada tolak akun admin, langsung arahkan ke /admin — sesi yang baru dibuat
        // di sini sudah otomatis dikenali Filament (guard sama), jadi tidak perlu login dua kali.
        $redirectUrl = Auth::user()->role === 'admin'
            ? route('filament.admin.pages.dashboard')
            : route('akun.produk-saya');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
