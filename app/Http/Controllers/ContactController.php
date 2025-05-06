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

    $contacts = new LengthAwarePaginator(
    $currentItems,
    count($data),
    $perPage,
    $currentPage,
    ['path' => Paginator::resolveCurrentPath()]
    );

    return view('pages.contact.index', compact('contacts'));
    }
}
