<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ContactController extends Controller
{
    public function index(Request $request)
    {
    $token = session('token');

    $response = Http::withToken($token)
                    ->get('https://back-end-absensi.vercel.app/api/contact/all');

    if ($response->failed()) {
        return redirect()->back()->with('error', 'Gagal mengambil data dari API');
    }

    $data = $response->json()['data'];

    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

    $contacts = new LengthAwarePaginator($currentItems, count($data), $perPage, $currentPage,
    ['path' => Paginator::resolveCurrentPath()]
    );

    return view('pages.contact.index', compact('contacts'));
    }

    public function create()
    {
        return view('pages.contact.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|numeric',
        ]);

        $token = session('token');

        $data = [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ];

        $response = Http::withToken($token)
                        ->post('https://back-end-absensi.vercel.app/api/contact', $data);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal menambahkan kontak');
        }

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan');
    }

    public function edit($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
                        ->get("https://back-end-absensi.vercel.app/api/contact/{$id}");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API');
        }

        $contact = $response->json()['data'];

        return view('pages.contact.edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|numeric',
        ]);

        $token = session('token');

        $data = [
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'address' => $request->address,
            'phone' => $request->phone,
        ];

        $response = Http::withToken($token)
                        ->patch("https://back-end-absensi.vercel.app/api/contact/{$id}", $data);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal memperbarui data kontak');
        }

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui');
    }

    public function destroy($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
                        ->delete("https://back-end-absensi.vercel.app/api/contact/{$id}");

        if ($response->successful()) {
            return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus kontak.');
    }
}
