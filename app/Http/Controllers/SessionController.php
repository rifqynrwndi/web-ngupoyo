<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function submitLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required',
        ]);

        try {
            // Step 1: Login ke API
            $response = Http::post('https://back-end-absensi.vercel.app/api/auth/login', [
                'identifier' => $request->identifier,
                'password' => $request->password,
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Login gagal. Cek email/username dan password.');
            }

            $token = $response->json('data');

            // Step 2: Get user info dari /auth/me
            $userResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/auth/me');

            if ($userResponse->failed()) {
                return back()->with('error', 'Gagal mengambil data pengguna.');
            }

            $user = $userResponse->json('data');

            if (!isset($user['role']) || $user['role'] !== 'admin') {
                return back()->with('error', 'Hanya admin yang diizinkan login.');
            }

            // Step 3: Simpan ke session Laravel
            session([
                'token' => $token,
                'user' => $user,
            ]);

            return redirect()->route('dashboard.index')->with('success', 'Login berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
    $token = $request->token;
    $response = Http::withToken($token)
        ->get('https://back-end-absensi.vercel.app/api/auth/me');

    if ($response->successful()) {
        session([
            'user' => $response->json()['data'],
            'token' => $token
        ]);
        return redirect()->route('dashboard.index')->with('success', 'Login berhasil!');
    }

    return back()->with('error', 'Login failed: ' . $response->status());
    }

    public function updatePasswordForm()
    {
        return view('pages.auth.update-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $token = session('token');

        $response = Http::withToken($token)->put('https://back-end-absensi.vercel.app/api/auth/update-password', [
            'oldPassword' => $request->oldPassword,
            'password' => $request->password,
            'confirmPassword' => $request->password_confirmation,
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal mengubah password.');
        }

        return redirect('/')->with('success', 'Password berhasil diubah. Silahkan login kembali.');
    }
}

