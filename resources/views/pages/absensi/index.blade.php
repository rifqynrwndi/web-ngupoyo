@extends('layouts.app')

@section('title', 'Attandance')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Attandance</h1>
                <div class="section-header-button">
                    <a href="{{ route('attendance.export.pdf') }}" target="_blank" class="btn btn-danger">Export PDF</a>
                    <a href="{{ route('attendance.export.excel') }}" target="_blank" class="btn btn-success">Export Excel</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Attendance</a></div>
                    <div class="breadcrumb-item">All Attendance</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Attandance List</h2>
                <p class="section-lead">
                    You can manage all attandances, such as editing, deleting, and more.
                </p>

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
                                                <th>Waktu</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($checkIns as $attendance)
                                                <tr>
                                                    <td>{{ $attendance['userId']['fullName'] }}</td>
                                                    <td>{{ $attendance['userId']['username'] }}</td>
                                                    <td>
                                                        <a href="https://www.google.com/maps?q={{ $attendance['location']['latitude'] }},{{ $attendance['location']['longitude'] }}"
                                                        target="_blank">
                                                            Lihat di Google Maps
                                                        </a>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance['timestamp'])->format('d M Y, H:i') }}</td>
                                                     <td class="text-center">
                                                        {{-- Show --}}
                                                        <a href="#"
                                                        class="btn btn-sm btn-primary mx-1 btn-show"
                                                        data-id="{{ $attendance['_id'] }}">
                                                            <i class="fas fa-eye"></i> Show
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data check-in.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    {{ $checkIns->links() }}
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
                                                <th>Waktu</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($checkOuts as $attendance)
                                                <tr>
                                                    <td>{{ $attendance['userId']['fullName'] }}</td>
                                                    <td>{{ $attendance['userId']['username'] }}</td>
                                                    <td>
                                                        <a href="https://www.google.com/maps?q={{ $attendance['location']['latitude'] }},{{ $attendance['location']['longitude'] }}"
                                                        target="_blank">
                                                            Lihat di Google Maps
                                                        </a>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance['timestamp'])->format('d M Y, H:i') }}</td>
                                                    <td class="text-center">
                                                        {{-- Show --}}
                                                        <a href="#"
                                                        class="btn btn-sm btn-primary mx-1 btn-show"
                                                        data-id="{{ $attendance['_id'] }}">
                                                            <i class="fas fa-eye"></i> Show
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data check-out.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    {{ $checkOuts->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="showModalLabel">Attendance Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="modal-content-body">
            <p class="text-center">Loading...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
    <script>
        function formatTanggalWaktu(datetimeStr) {
            const options = {
                day: '2-digit', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit',
                hour12: false
            };
            const date = new Date(datetimeStr);
            return date.toLocaleString('id-ID', options);
        }

            $(document).ready(function () {
            $('.btn-show').click(function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Tampilkan loading awal
                $('#modal-content-body').html('<p class="text-center">Loading...</p>');
                $('#showModal').modal('show');

                // Request AJAX
                $.ajax({
                    url: '/attendance/' + id,
                    method: 'GET',
                    success: function (res) {
                        let html = `
                            <table class="table table-bordered">
                                <tr><th>Nama</th><td>${res.userId?.fullName ?? '-'}</td></tr>
                                <tr><th>Username</th><td>${res.userId?.username ?? '-'}</td></tr>
                                <tr><th>Jenis</th><td>${res.type ?? '-'}</td></tr>
                                <tr><th>Waktu</th><td>${formatTanggalWaktu(res.timestamp)}</td></tr>
                                <tr><th>Lokasi</th>
                                    <td>
                                        <iframe
                                            width="100%"
                                            height="300"
                                            frameborder="0"
                                            style="border:0"
                                            referrerpolicy="no-referrer-when-downgrade"
                                            src="https://www.google.com/maps/embed/v1/place?key={{ env('API_GOOGLE_MAPS') }}&q=${res.location.latitude},${res.location.longitude}&zoom=16"
                                            allowfullscreen>
                                        </iframe>
                                    </td>
                                </tr>
                                <tr><th>Foto</th>
                                    <td>
                                        ${res.imageUrl ? `<img src="${res.imageUrl}" width="200" class="img-thumbnail">` : '<i>Tidak ada foto</i>'}
                                    </td>
                                </tr>
                            </table>
                        `;
                        $('#modal-content-body').html(html);
                    },
                    error: function () {
                        $('#modal-content-body').html('<p class="text-danger text-center">Gagal memuat data.</p>');
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
