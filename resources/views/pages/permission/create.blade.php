@extends('layouts.app')

@section('title', 'Advanced Forms')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Advanced Forms</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Forms</a></div>
                    <div class="breadcrumb-item">Permission</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Permission</h2>
                <div class="card">
                    <form action="{{ route('permissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-header"><h4>Form Permission</h4></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Jenis Permission</label>
                                <select class="form-control @error('jenisPermission') is-invalid @enderror" name="jenisPermission" id="jenisPermission" required>
                                    <option value="Izin" {{ 'Izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="Sakit" {{ 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Cuti" {{ 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Alasan</label>
                                <textarea name="alasan" class="form-control" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="datetime-local" name="tanggalMulai" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="datetime-local" name="tanggalSelesai" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Dokumen Pendukung (Image)</label>

                                <div class="mb-2">
                                    <img id="preview"
                                         src="{{ $permission['dokumenPendukung'] ?? '' }}"
                                         alt="Dokumen Pendukung"
                                         width="200"
                                         class="img-thumbnail"
                                         style="{{ empty($permission['dokumenPendukung']) ? 'display:none;' : '' }}">
                                </div>

                                <input type="file" name="dokumenPendukung" class="form-control" accept="image/*" onchange="previewImage(event)">
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Ajukan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endpush
