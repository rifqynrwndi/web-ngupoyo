<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function index()
    {
        $token = session('token');

        // Ambil semua data via API
        $usersResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/users');
        $attendanceResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/attendance/all');
        $reportResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/attendance/report');
        $permissionResponse = Http::withToken($token)->get('https://back-end-absensi.vercel.app/api/permission');

        if ($usersResponse->failed() || $attendanceResponse->failed() || $reportResponse->failed() || $permissionResponse->failed()) {
            return back()->with('error', 'Gagal mengambil data dashboard dari API');
        }

        $totalUsers = count($usersResponse->json('data'));
        $totalAttendances = count($attendanceResponse->json('data'));
        $totalPermissions = count($permissionResponse->json('data'));

        // Pisahkan check-in dan check-out terbaru
        $attendances = collect($attendanceResponse->json('data'))->sortByDesc('timestamp');

        $recentCheckIns = $attendances->where('type', 'check-in')->take(5)->values();
        $recentCheckOuts = $attendances->where('type', 'check-out')->take(5)->values();

        $recentPermissions = collect($permissionResponse->json('data'))
            ->sortByDesc('createdAt')
            ->take(5)
            ->values();

        // Gabungkan check-in dan check-out user pada hari ini
        $todayDate = now()->format('Y-m-d');
        $todayCheckins = collect($reportResponse->json('data'))
            ->filter(function ($item) use ($todayDate) {
                return str_starts_with($item['timestamp'], $todayDate);
            });

        // Kelompokkan berdasarkan userId
        $groupedToday = $todayCheckins->groupBy(function ($item) {
            return $item['userId']['_id'];
        });

        // Gabungkan check-in dan check-out dalam satu baris
        $mergedTodayCheckins = $groupedToday->map(function ($items) {
            $checkIn = $items->firstWhere('type', 'check-in');
            $checkOut = $items->firstWhere('type', 'check-out');

            return [
                'user' => $checkIn['userId']['fullName'] ?? ($checkOut['userId']['fullName'] ?? '-'),
                'date' => $checkIn['timestamp'] ?? $checkOut['timestamp'] ?? now()->toDateString(),
                'check_in' => $checkIn['timestamp'] ?? null,
                'check_out' => $checkOut['timestamp'] ?? null,
            ];
        })->values();

        return view('pages.dashboard', compact(
            'totalUsers',
            'totalAttendances',
            'totalPermissions',
            'recentCheckIns',
            'recentCheckOuts',
            'recentPermissions',
            'mergedTodayCheckins'
        ));
    }
}
