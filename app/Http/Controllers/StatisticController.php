<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get('https://back-end-absensi.vercel.app/api/attendance/statistics');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data dari API');
        }

        $data = $response->json()['data'];

        // Filter check-ins dan check-outs
        $checkInsRaw = array_filter($data['users'], fn($user) => $user['type'] === 'check-in');
        $checkOutsRaw = array_filter($data['users'], fn($user) => $user['type'] === 'check-out');

        // Pagination manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $checkIns = new LengthAwarePaginator(
            array_slice($checkInsRaw, ($currentPage - 1) * $perPage, $perPage),
            count($checkInsRaw), $perPage, $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        $checkOuts = new LengthAwarePaginator(
            array_slice($checkOutsRaw, ($currentPage - 1) * $perPage, $perPage),
            count($checkOutsRaw), $perPage, $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        $month = $data['month'];

        return view('pages.statistic.index', compact('checkIns', 'checkOuts', 'month'));
    }
}
