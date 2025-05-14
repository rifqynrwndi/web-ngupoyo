<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //index
    public function index(Request $request)
    {
    $token = session('token');

    $response = Http::withToken($token)
                    ->get('https://back-end-absensi.vercel.app/api/users');

    if ($response->failed()) {
        return redirect()->back()->with('error', 'Gagal mengambil data dari API');
    }

    $data = $response->json()['data'];

    $perPage = 10; // Misalnya 10 item per halaman
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

    $users = new LengthAwarePaginator(
    $currentItems,
    count($data),
    $perPage,
    $currentPage,
    ['path' => Paginator::resolveCurrentPath()]
    );

    return view('pages.users.index', compact('users'));
    }

    //edit
    public function edit($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("https://back-end-absensi.vercel.app/api/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data user');
        }

        $user = $response->json()['data'];

        return view('pages.users.edit', compact('user'));
    }

    //update
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'role' => 'required|string|max:255',
        ]);

        $token = session('token');

        $data = [
            'fullName' => $request->fullName,
            'username' => $request->username,
            'role' => $request->role,
        ];

        $response = Http::withToken($token)
            ->put("https://back-end-absensi.vercel.app/api/users/{$id}", $data);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal memperbarui user');
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->delete("https://back-end-absensi.vercel.app/api/users/{$id}");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal menghapus user');
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
