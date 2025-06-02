@extends('layouts.app')

@section('title', 'Dashboard')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Admin</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('dashboard.index') }}">Dashboard</a></div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Karyawan</h4></div>
                        <div class="card-body">{{ $totalUsers ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-calendar-check"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Absensi</h4></div>
                        <div class="card-body">{{ $totalAttendances ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-user-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Izin</h4></div>
                        <div class="card-body">{{ $totalPermissions ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kehadiran Hari Ini -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Kehadiran Hari Ini</h4></div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Check-In</th>
                                    <th>Check-Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mergedTodayCheckins as $item)
                                <tr>
                                    <td>{{ $item['user'] }}</td>
                                    <td>{{ date('d M Y', strtotime($item['date'])) }}</td>
                                    <td>{{ $item['check_in'] ? date('H:i:s', strtotime($item['check_in'])) : '-' }}</td>
                                    <td>{{ $item['check_out'] ? date('H:i:s', strtotime($item['check_out'])) : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">Belum ada kehadiran hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absensi Terbaru: Check-In & Check-Out -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h4>Check-In Terbaru</h4></div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentCheckIns as $checkin)
                                <tr>
                                    <td>{{ $checkin['userId']['fullName'] ?? '-' }}</td>
                                    <td>{{ date('d M Y', strtotime($checkin['timestamp'])) }}</td>
                                    <td>{{ date('H:i:s', strtotime($checkin['timestamp'])) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">Tidak ada data check-in.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h4>Check-Out Terbaru</h4></div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentCheckOuts as $checkout)
                                <tr>
                                    <td>{{ $checkout['userId']['fullName'] ?? '-' }}</td>
                                    <td>{{ date('d M Y', strtotime($checkout['timestamp'])) }}</td>
                                    <td>{{ date('H:i:s', strtotime($checkout['timestamp'])) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center">Tidak ada data check-out.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Izin Terbaru -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Izin Terbaru</h4></div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPermissions as $izin)
                                <tr>
                                    <td>{{ $izin['userId']['fullName'] ?? '-' }}</td>
                                    <td>
                                        {{ isset($izin['tanggalMulai']) ? date('d M Y', strtotime($izin['tanggalMulai'])) : '-' }} -
                                        {{ isset($izin['tanggalSelesai']) ? date('d M Y', strtotime($izin['tanggalSelesai'])) : '-' }}
                                    </td>
                                    <td>{{ $izin['alasan'] ?? '-' }}</td>
                                    <td>
                                        <span class="badge
                                            {{ $izin['status'] == 'Disetujui' ? 'badge-success' : ($izin['status'] == 'Ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($izin['status']) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">Tidak ada data izin.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
@endsection
