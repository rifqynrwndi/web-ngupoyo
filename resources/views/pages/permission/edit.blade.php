@extends('layouts.app')

@section('title', 'Edit Izin')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Izin</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Izin</a></div>
                <div class="breadcrumb-item">Edit Izin</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card">
                <div class="card-header"><h4>Edit Izin</h4></div>
                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission['_id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Tanggal Mulai -->
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tanggalMulai" class="form-control"
                                value="{{ \Carbon\Carbon::parse($permission['tanggalMulai'])->format('Y-m-d') }}" required>
                        </div>

                        <!-- Tanggal Selesai -->
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="tanggalSelesai" class="form-control"
                                value="{{ \Carbon\Carbon::parse($permission['tanggalSelesai'])->format('Y-m-d') }}" required>
                        </div>

                        <!-- Jenis Permission -->
                        <div class="form-group">
                            <label>Jenis Izin</label>
                            <select class="form-control @error('jenisPermission') is-invalid @enderror" name="jenisPermission" id="jenisPermission" required>
                                <option value="Izin" {{ old('jenisPermission', $user['jenisPermission'] ?? '') === 'Izin' ? 'selected' : '' }}>Izin</option>
                                <option value="Sakit" {{ old('jenisPermission', $user['jenisPermission'] ?? '') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="Cuti" {{ old('jenisPermission', $user['jenisPermission'] ?? '') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            </select>
                        </div>

                        <!-- Alasan -->
                        <div class="form-group">
                            <label>Alasan</label>
                            <textarea name="alasan" class="form-control" required>{{ $permission['alasan'] }}</textarea>
                        </div>

                        <!-- Dokumen Pendukung -->
                        <div class="form-group">
                            <label>Dokumen Pendukung (Image)</label>

                            @if(!empty($permission['dokumenPendukung']))
                                <div class="mb-2">
                                    <img id="preview" src="{{ $permission['dokumenPendukung'] }}" alt="Dokumen Pendukung" width="200" class="img-thumbnail">
                                </div>
                            @endif

                            <input type="file" name="dokumenPendukung" class="form-control" accept="image/*" onchange="previewImage(event)">
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
