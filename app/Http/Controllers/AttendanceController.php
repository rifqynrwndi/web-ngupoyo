<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class AttendanceController extends Controller
{
    //index
    public function index(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)
                        ->get('https://back-end-absensi.vercel.app/api/attendance/all');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API');
        }

        $data = $response->json()['data'];

        $checkIns = array_filter($data, fn($item) => $item['type'] === 'check-in');
        $checkOuts = array_filter($data, fn($item) => $item['type'] === 'check-out');

        // Paginasi
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $paginatedCheckIns = new LengthAwarePaginator(
            array_slice($checkIns, ($currentPage - 1) * $perPage, $perPage),
            count($checkIns),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        $paginatedCheckOuts = new LengthAwarePaginator(
            array_slice($checkOuts, ($currentPage - 1) * $perPage, $perPage),
            count($checkOuts),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('pages.absensi.index', [
            'checkIns' => $paginatedCheckIns,
            'checkOuts' => $paginatedCheckOuts,
        ]);
    }

    public function show($id)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get("https://back-end-absensi.vercel.app/api/attendance/{$id}");

        if ($response->failed()) {
            return response()->json([
                'message' => 'Gagal mengambil detail attendance'
            ], 500);
        }

        return response()->json($response->json()['data']);
    }

    public function exportPdf(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get('https://back-end-absensi.vercel.app/api/attendance/export/pdf');

        if (!$response->ok()) {
            return redirect()->back()->with('error', 'Gagal mengunduh file PDF.');
        }

        return response($response->body(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename=attendance-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get('https://back-end-absensi.vercel.app/api/attendance/export/excel');

        if (!$response->ok()) {
            return redirect()->back()->with('error', 'Gagal mengunduh file Excel.');
        }

        return response($response->body(), 200)
            ->header('Content-Type', 'application/xlsx')
            ->header('Content-Disposition', 'attachment; filename=attendance-report.xlsx');
    }
}
