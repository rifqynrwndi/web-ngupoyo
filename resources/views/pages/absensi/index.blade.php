@extends('layouts.app')

@section('title', 'Rekap Absensi')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Absensi</h1>
                <div class="section-header-button">
                    <a href="{{ route('attendance.export.pdf') }}" target="_blank" class="btn btn-danger">Export PDF</a>
                    <a href="{{ route('attendance.export.excel') }}" target="_blank" class="btn btn-success">Export Excel</a>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#faceModal">
                    Daftarkan Wajah
                </button>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('attendances.index') }}">Absensi</a></div>
                    <div class="breadcrumb-item">Semua Absensi</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Rekap Absensi</h2>
                <p class="section-lead">
                    Anda dapat mengelola semua data absensi, seperti mengedit, menghapus, dan lainnya.
                </p>

                <div class="row">
                    <div class="col-12">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Rekap Absensi Datang</h4>
                                <a href="{{ route('admin.attendance.check-in.form') }}" class="btn btn-primary">
                                    Absen Datang
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table-striped table">
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
                                                    <td>{{ isset($attendance['userId']['fullName']) ? $attendance['userId']['fullName'] : 'Nama tidak tersedia' }}</td>
                                                    <td>{{ isset($attendance['userId']['username']) ? $attendance['userId']['username'] : 'Username tidak tersedia' }}</td>
                                                    <td>
                                                        <a href="https://www.google.com/maps?q={{ urlencode($attendance['location']['name'] ?? '') }}"
                                                        target="_blank">
                                                            {{ $attendance['location']['name'] ?? 'Lokasi tidak tersedia' }}
                                                        </a>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance['timestamp'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
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
                                    {{ $checkIns->appends(['checkout-page' => request('checkout-page')])->links() }}
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Rekap Absensi Pulang</h4>
                                <a href="{{ route('admin.attendance.check-out.form') }}" class="btn btn-primary">
                                    Absen Pulang
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table-striped table">
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
                                                    <td>{{ isset($attendance['userId']['fullName']) ? $attendance['userId']['fullName'] : 'Nama tidak tersedia' }}</td>
                                                    <td>{{ isset($attendance['userId']['username']) ? $attendance['userId']['username'] : 'Username tidak tersedia' }}</td>
                                                    <td>
                                                        <a href="https://www.google.com/maps?q={{ $attendance['location']['name'] }}"
                                                        target="_blank">
                                                            {{ $attendance['location']['name'] }}
                                                        </a>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance['timestamp'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</td>
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
                                    {{ $checkOuts->appends(['checkin-page' => request('checkin-page')])->links() }}
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
          <h5 class="modal-title" id="showModalLabel">Detail Kehadiran</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="modal-content-body">
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div id="loading-overlay"
     style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background-color: rgba(0, 0, 0, 0.6); z-index: 9999;
            display: none; align-items: center; justify-content: center;
            flex-direction: column; color: white; font-family: 'Segoe UI', sans-serif;
            backdrop-filter: blur(4px); text-align: center;">
    <div>
        <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-4 mb-0" style="font-size: 1.2rem; font-weight: 500;">
            Sedang memproses wajah...<br>Mohon tunggu sebentar.
        </p>
    </div>
</div>
@endsection

<div class="modal fade" id="faceModal" tabindex="-1" role="dialog" aria-labelledby="faceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftarkan Wajah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="stopWebcam()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <video id="webcam" autoplay playsinline width="320" height="240" class="rounded mb-3 border"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <br>
                <button class="btn btn-success" onclick="captureFace()">Ambil & Daftarkan</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let stream;

        function startWebcam() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(s => {
                    stream = s;
                    document.getElementById('webcam').srcObject = stream;
                })
                .catch(err => {
                    alert('Tidak bisa mengakses kamera: ' + err.message);
                });
        }

        function stopWebcam() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }

        $('#faceModal').on('shown.bs.modal', function () {
            startWebcam();
        });

        $('#faceModal').on('hidden.bs.modal', function () {
            stopWebcam();
            const video = document.getElementById('webcam');
            video.srcObject = null;
        });

        const loadingOverlay = document.getElementById('loading-overlay');

        function captureFace() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            video.pause();

            if (video.videoWidth === 0 || video.videoHeight === 0) {
                alert('Kamera belum siap. Silakan tunggu beberapa detik lalu coba lagi.');
                video.play();
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            loadingOverlay.style.display = 'block';

            canvas.toBlob(blob => {
                if (!blob) {
                    alert('Gagal menangkap gambar dari webcam. Coba lagi.');
                    video.play();
                    loadingOverlay.style.display = 'none';
                    return;
                }

                const formData = new FormData();
                formData.append("image", blob, 'captured.jpg');

                const btn = document.querySelector('button[onclick="captureFace()"]');
                btn.disabled = true;

                loadingOverlay.style.display = 'flex';


                fetch('/register-face', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mendaftarkan wajah');
                    return response.json();
                })
                .then(data => {
                    console.log('Berhasil:', data.message);
                    $('#faceModal').modal('hide');
                    stopWebcam();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message,
                    });
                })
                .finally(() => {
                    btn.disabled = false;
                    video.play();
                    loadingOverlay.style.display = 'none';
                });
            }, 'image/jpeg');
        }
    </script>


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
                        const locationName = res.location?.name || 'Lokasi tidak diketahui';
                        const encodedLocation = encodeURIComponent(locationName);

                        const mapIframe = `
                            <iframe
                                width="100%"
                                height="300"
                                frameborder="0"
                                style="border:0"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q=${encodedLocation}&output=embed"
                                allowfullscreen>
                            </iframe>
                        `;

                        let html = `
                            <table class="table table-bordered">
                                <tr><th>Nama</th><td>${res.userId?.fullName ?? '-'}</td></tr>
                                <tr><th>Username</th><td>${res.userId?.username ?? '-'}</td></tr>
                                <tr><th>Waktu</th><td>${formatTanggalWaktu(res.timestamp)}</td></tr>
                                <tr><th>Nama Lokasi</th><td>${locationName}</td></tr>
                                <tr><th>Lokasi</th><td>${mapIframe}</td></tr>
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
