<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PermissionController extends Controller
{
    //index
    public function index(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)
                        ->get('https://back-end-absensi.vercel.app/api/permission');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API');
        }

        $data = $response->json()['data'];

        $perPage = 10; // Misalnya 10 item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $currentItems = array_slice($data, ($currentPage - 1) * $perPage, $perPage);

        $permissions = new LengthAwarePaginator(
        $currentItems,
        count($data),
        $perPage,
        $currentPage,
        ['path' => Paginator::resolveCurrentPath()]
        );

        return view('pages.permission.index', compact('permissions'));
    }

    public function showModal($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("https://back-end-absensi.vercel.app/api/permission/{$id}");

        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal mengambil detail permission'
            ], 500);
        }

        return response()->json($response->json()['data']);
    }

    //create
    public function create()
    {
        return view('pages.permission.create');
    }

    //store
    public function store(Request $request)
    {
        $token = session('token');

        $validated = $request->validate([
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date|after_or_equal:tanggalMulai',
            'jenisPermission' => 'required|string|max:255',
            'alasan' => 'required|string',
            'dokumenPendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $multipartData = [
            ['name' => 'tanggalMulai', 'contents' => $request->input('tanggalMulai')],
            ['name' => 'tanggalSelesai', 'contents' => $request->input('tanggalSelesai')],
            ['name' => 'jenisPermission', 'contents' => $request->input('jenisPermission')],
            ['name' => 'alasan', 'contents' => $request->input('alasan')],
        ];

        if ($request->hasFile('dokumenPendukung')) {
            $file = $request->file('dokumenPendukung');
            $multipartData[] = [
                'name' => 'dokumenPendukung',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        $response = Http::withToken($token)
            ->asMultipart()
            ->post("https://back-end-absensi.vercel.app/api/permission", $multipartData);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal menyimpan data permission');
        }

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diajukan');
    }


    //edit
    public function edit($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("https://back-end-absensi.vercel.app/api/permission/{$id}");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data permission');
        }

        $permission = $response->json()['data'];

        return view('pages.permission.edit', compact('permission'));
    }


    //update
    public function update(Request $request, $id)
    {
        $token = session('token');

        $validated = $request->validate([
            'tanggalMulai' => 'required|date',
            'tanggalSelesai' => 'required|date',
            'jenisPermission' => 'required|string',
            'alasan' => 'required|string',
            'dokumenPendukung' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $multipartData = [
            [
                'name' => 'tanggalMulai',
                'contents' => $request->input('tanggalMulai')
            ],
            [
                'name' => 'tanggalSelesai',
                'contents' => $request->input('tanggalSelesai')
            ],
            [
                'name' => 'jenisPermission',
                'contents' => $request->input('jenisPermission')
            ],
            [
                'name' => 'alasan',
                'contents' => $request->input('alasan')
            ],
        ];

        if ($request->hasFile('dokumenPendukung')) {
            $file = $request->file('dokumenPendukung');
            $multipartData[] = [
                'name' => 'dokumenPendukung',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        $response = Http::withToken($token)
        ->asMultipart()
        ->patch("https://back-end-absensi.vercel.app/api/permission/{$id}", $multipartData);

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal memperbarui data permission');
        }

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    //delete
    public function destroy($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->delete("https://back-end-absensi.vercel.app/api/permission/{$id}");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal menghapus permission');
        }

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }

    public function approve($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->put("https://back-end-absensi.vercel.app/api/permission/{$id}/approve");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal menyetujui permission');
        }

        return redirect()->route('permissions.index')->with('success', 'Permission disetujui');
    }

    public function reject($id)
{
    $token = session('token');

    $multipartData = [
        [
            'name' => 'status',
            'contents' => 'Ditolak'
        ],
    ];

    $response = Http::withToken($token)
        ->asMultipart()
        ->patch("https://back-end-absensi.vercel.app/api/permission/{$id}/", $multipartData);

    if ($response->failed()) {
        return redirect()->back()->with('error', 'Gagal menolak permission');
    }

    return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditolak');
}

    }
