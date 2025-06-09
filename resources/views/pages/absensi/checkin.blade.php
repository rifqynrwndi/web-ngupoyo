@extends('layouts.app')

@section('title', 'Absensi Datang')

@push('style')
<link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Absensi Datang</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('attendances.index') }}">Attendance</a></div>
                <div class="breadcrumb-item">Absen Datang</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Absen Datang</h2>
            <p class="section-lead">Silakan gunakan kamera untuk absen wajah.</p>

            <div class="card">
                <div class="card-body">
                    <form id="checkin-form">
                        <div class="form-group">
                            <label for="user_id">Pilih User</label>
                            <select name="user_id" id="user_id" class="form-control selectric" required>
                                <option value="" selected disabled>Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user['_id'] }}">{{ $user['fullName'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location_mode">Lokasi Saat Ini</label>
                            <select id="location_mode" class="form-control" required>
                                <option value="current">Gunakan Lokasi Saat Ini</option>
                                <option value="office">Kantor PT Ngupoyo Rejeki Lestari Mulya</option>
                            </select>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <video id="webcam" autoplay playsinline style="transform: scaleX(-1);" width="320" height="240" class="rounded mb-3 border"></video>
                        <canvas id="canvas" style="display: none;"></canvas>
                        <br>
                        <button class="btn btn-success" onclick="captureFace()">Absen Sekarang</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Loading Overlay --}}
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

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

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

    document.addEventListener("DOMContentLoaded", function () {
        startWebcam();
    });

    window.addEventListener("beforeunload", function () {
        stopWebcam();
    });

    const loadingOverlay = document.getElementById('loading-overlay');

    function captureFace() {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        const selectedUserId = document.getElementById('user_id').value;
        const button = document.querySelector('button[onclick="captureFace()"]');

        if (!selectedUserId) {
            Swal.fire({
                icon: 'warning',
                title: 'User belum dipilih',
                text: 'Silakan pilih user terlebih dahulu.',
            });
            return;
        }

        video.pause();

        if (video.videoWidth === 0 || video.videoHeight === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Kamera belum siap',
                text: 'Silakan tunggu beberapa detik lalu coba lagi.',
            });
            video.play();
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        loadingOverlay.style.display = 'flex';

        canvas.toBlob(blob => {
            if (!blob) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menangkap gambar',
                    text: 'Silakan coba lagi.',
                });
                video.play();
                loadingOverlay.style.display = 'none';
                return;
            }

            const locationMode = document.getElementById('location_mode').value;
            let latitude = '';
            let longitude = '';
            let locationName = '';

            if (locationMode === 'office') {
                latitude = '-6.9307994';
                longitude = '110.5387057';
                locationName = 'PT Ngupoyo Rejeki Lestari Mulya';

                sendCheckIn(blob, selectedUserId, latitude, longitude, locationName);
            } else {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        latitude = position.coords.latitude.toString();
                        longitude = position.coords.longitude.toString();
                        locationName = '';

                        sendCheckIn(blob, selectedUserId, latitude, longitude, locationName);
                    },
                    function (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Ambil Lokasi',
                            text: 'Pastikan izin lokasi diaktifkan di browser.',
                        });
                        video.play();
                        loadingOverlay.style.display = 'none';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }
        }, 'image/jpeg');
    }

    function sendCheckIn(blob, selectedUserId, latitude, longitude, locationName) {
        const formData = new FormData();
        formData.append("image", blob, 'absen.jpg');
        formData.append("latitude", latitude);
        formData.append("longitude", longitude);
        formData.append("locationName", locationName);

        const button = document.querySelector('button[onclick="captureFace()"]');
        button.disabled = true;

        fetch(`/admin/attendance/check-in/${selectedUserId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(async res => {
            const data = await res.json().catch(() => null);
            if (!res.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absen',
                    text: data?.message || 'Terjadi kesalahan saat absen.',
                });
                throw new Error(data?.message || 'Request failed');
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Absen berhasil dilakukan.',
            }).then(() => {
                window.location.href = "{{ route('attendances.index') }}";
            });
        })
        .catch(err => {
            console.error("Fetch error:", err);
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: err.message || 'Terjadi kesalahan saat mengirim data.',
            });
        })
        .finally(() => {
            button.disabled = false;
            document.getElementById('webcam').play();
            loadingOverlay.style.display = 'none';
        });
    }
</script>
@endpush
