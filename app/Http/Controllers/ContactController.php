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

    $name = $request->query('name');
    if ($name) {
        $data = array_filter($data, function ($contact) use ($name) {
            $fullName = strtolower(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''));
            return str_contains($fullName, strtolower($name));
        });
    }

    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();

    $currentItems = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

    $contacts = new LengthAwarePaginator(
        $currentItems,
        count($data),
        $perPage,
        $currentPage,
        [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]
    );

    return view('pages.contact.index', compact('contacts'));
    }

    public function create()
    {
        $token = session('token');

        $meResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/auth/me');

        if ($meResponse->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data user.');
        }

        $user = $meResponse->json('data');
        $userId = $user['_id'] ?? null;

        if (!$userId) {
            return redirect()->back()->with('error', 'User ID tidak ditemukan.');
        }

        $contactResponse = Http::withToken($token)->get("https://back-end-absensi.vercel.app/api/contact/{$userId}");

        if ($contactResponse->ok() && $contactResponse->json('data')) {
            return redirect()->route('contacts.index')->with('error', 'Anda sudah memiliki kontak.');
        }

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

    public function adminCheckIn(Request $request, $userId)
    {
        $token = session('token');

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'locationName' => 'nullable|string',
        ]);

        try {
            $response = Http::withToken($token)
                ->attach(
                    'image',
                    file_get_contents($request->file('image')),
                    $request->file('image')->getClientOriginalName()
                )
                ->asMultipart()
                ->post("https://back-end-absensi.vercel.app/api/admin/attendance/check-in/{$userId}", [
                    [
                        'name' => 'latitude',
                        'contents' => $request->input('latitude')
                    ],
                    [
                        'name' => 'longitude',
                        'contents' => $request->input('longitude')
                    ],
                    [
                        'name' => 'locationName',
                        'contents' => $request->input('locationName') ?? ''
                    ],
                ]);

            if ($response->failed()) {
                \Log::error('Admin check-in failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return redirect()->back()->with('error', 'Gagal melakukan check-in.');
            }

            return redirect()->back()->with('success', 'Check-in berhasil dilakukan.');
        } catch (\Exception $e) {
            \Log::error('Admin check-in exception', [
                'message' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan check-in.');
        }
    }

}
