@extends('layouts.app')

@section('title', 'Statistic')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Statistik Bulan {{ $month }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Statistic</a></div>
                    <div class="breadcrumb-item">All Statistics</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Statistic List</h2>
                <div class="row mt-4">
                    <div class="col-12">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <h4>Check-In Attendance</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                        <th>Waktu Datang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($checkIns as $attendance)
                                        <tr>
                                            <td>{{ $attendance['fullName'] }}</td>
                                            <td>{{ $attendance['username'] }}</td>
                                            <td>
                                                <a href="https://www.google.com/maps?q={{ $attendance['location']['name'] }}"
                                                target="_blank">
                                                    {{ $attendance['location']['name'] }}
                                                </a>
                                            </td>
                                            <td>{{ $attendance['date'] }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $attendance['time'])->format('H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data check-in.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                    </table>
                                {{ $checkIns->appends(['checkout-page' => request('checkout-page')])->links() }}
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Check-Out Attendance</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                        <th>Waktu Pulang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($checkOuts as $attendance)
                                        <tr>
                                            <td>{{ $attendance['fullName'] }}</td>
                                            <td>{{ $attendance['username'] }}</td>
                                            <td>
                                                <a href="https://www.google.com/maps?q={{ $attendance['location']['name'] }}"
                                                target="_blank">
                                                    {{ $attendance['location']['name'] }}
                                                </a>
                                            </td>
                                            <td>{{ $attendance['date'] }}</td>
                                            <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $attendance['time'])->format('H:i') }}</td>                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data check-out.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                                    {{ $checkOuts->appends(['checkin-page' => request('checkin-page')])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </section>
    </div>
  </div>
@endsection

@push('scripts')
    </script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
