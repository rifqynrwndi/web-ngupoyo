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

    public function registerFace(Request $request)
    {
        $token = session('token');

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $response = Http::withToken($token)
            ->attach(
                'image',
                file_get_contents($request->file('image')),
                $request->file('image')->getClientOriginalName()
            )
            ->post('https://back-end-absensi.vercel.app/api/face/register');

        if ($response->failed()) {
            \Log::error('Face register failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json(['message' => 'Gagal mendaftarkan wajah'], 500);
        }

        return response()->json(['message' => 'Pendaftaran wajah berhasil'], 200);
    }

    public function adminCheckInForm()
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get('https://back-end-absensi.vercel.app/api/users');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data user dari API');
        }

        $users = $response->json()['data'];

        return view('pages.absensi.checkin', compact('users'));
    }

    public function checkIn(Request $request, $userId)
    {
        $token = session('token');

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $googleApiKey = env('API_GOOGLE_MAPS');

        $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'latlng' => "{$latitude},{$longitude}",
            'language' => 'id',
            'key' => $googleApiKey,
        ]);

        $locationName = $request->input('locationName', '');

        if (empty($locationName)) {
            $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'latlng' => "{$latitude},{$longitude}",
                'language' => 'id',
                'key' => $googleApiKey,
            ]);

            if ($geocodeResponse->ok() && isset($geocodeResponse['results'][0]['place_id'])) {
                $placeId = $geocodeResponse['results'][0]['place_id'];

                $placeDetailResponse = Http::get("https://places.googleapis.com/v1/places/{$placeId}", [
                    'fields' => 'id,displayName',
                    'key' => $googleApiKey,
                ]);

                if ($placeDetailResponse->ok() && isset($placeDetailResponse['displayName']['text'])) {
                    $locationName = $placeDetailResponse['displayName']['text'];
                }
            }
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'locationName' => 'nullable|string',
        ]);

        try {
            $multipartData = [
                [
                    'name' => 'image',
                    'contents' => fopen($request->file('image')->getRealPath(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName(),
                ],
                [
                    'name' => 'latitude',
                    'contents' => $request->input('latitude'),
                ],
                [
                    'name' => 'longitude',
                    'contents' => $request->input('longitude'),
                ],
                [
                    'name' => 'locationName',
                    'contents' => $locationName,
                ],
            ];

            $response = Http::withToken($token)
                ->asMultipart()
                ->post("https://back-end-absensi.vercel.app/api/admin/attendance/check-in/{$userId}", $multipartData);

            $responseBody = $response->body();
            $decoded = json_decode($responseBody, true);

            if ($response->failed() || !$decoded) {
                \Log::error('Admin check-in failed or response not JSON', [
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Gagal melakukan check-in.'], 500);
                }

                return redirect()->back()->with('error', 'Gagal melakukan check-in.');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $decoded['message'] ?? 'Check-in berhasil dilakukan.']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil',
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin check-in exception', [
                'message' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat melakukan check-in.'], 500);
            }
            return response()->json([
                'error' => true,
                'message' => 'Check-In gagal',
            ]);
        }
    }

    public function adminCheckOutForm()
    {
        $token = session('token');

        $response = Http::withToken($token)
            ->get('https://back-end-absensi.vercel.app/api/users');

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Gagal mengambil data user dari API');
        }

        $users = $response->json()['data'];

        return view('pages.absensi.checkout', compact('users'));
    }

    public function checkOut(Request $request, $userId)
    {
        $token = session('token');

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $googleApiKey = env('API_GOOGLE_MAPS');

        $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'latlng' => "{$latitude},{$longitude}",
            'language' => 'id',
            'key' => $googleApiKey,
        ]);

        $locationName = $request->input('locationName', '');

        if (empty($locationName)) {
            $geocodeResponse = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'latlng' => "{$latitude},{$longitude}",
                'language' => 'id',
                'key' => $googleApiKey,
            ]);

            if ($geocodeResponse->ok() && isset($geocodeResponse['results'][0]['place_id'])) {
                $placeId = $geocodeResponse['results'][0]['place_id'];

                $placeDetailResponse = Http::get("https://places.googleapis.com/v1/places/{$placeId}", [
                    'fields' => 'id,displayName',
                    'key' => $googleApiKey,
                ]);

                if ($placeDetailResponse->ok() && isset($placeDetailResponse['displayName']['text'])) {
                    $locationName = $placeDetailResponse['displayName']['text'];
                }
            }
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'locationName' => 'nullable|string',
        ]);

        try {
            $multipartData = [
                [
                    'name' => 'image',
                    'contents' => fopen($request->file('image')->getRealPath(), 'r'),
                    'filename' => $request->file('image')->getClientOriginalName(),
                ],
                [
                    'name' => 'latitude',
                    'contents' => $request->input('latitude'),
                ],
                [
                    'name' => 'longitude',
                    'contents' => $request->input('longitude'),
                ],
                [
                    'name' => 'locationName',
                    'contents' => $locationName,
                ],
            ];

            $response = Http::withToken($token)
                ->asMultipart()
                ->post("https://back-end-absensi.vercel.app/api/admin/attendance/check-out/{$userId}", $multipartData);

            $responseBody = $response->body();
            $decoded = json_decode($responseBody, true);

            if ($response->failed() || !$decoded) {
                \Log::error('Admin check-out failed or response not JSON', [
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Gagal melakukan check-out.'], 500);
                }

                return redirect()->back()->with('error', 'Gagal melakukan check-out.');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $decoded['message'] ?? 'Check-out berhasil dilakukan.']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin check-out exception', [
                'message' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat melakukan check-out.'], 500);
            }
            return response()->json([
                'error' => true,
                'message' => 'Check-Out gagal',
            ]);

        }
    }
}
