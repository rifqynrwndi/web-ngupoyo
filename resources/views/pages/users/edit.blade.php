@extends('layouts.app')

@section('title', 'Edit Data Pegawai')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Pegawai</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Pegawai</a></div>
                    <div class="breadcrumb-item">Edit Pegawai</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Pegawai</h2>

                <div class="card">
                    <form action="{{ route('users.update', $user['_id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h4>Update Data Pegawai</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Foto Profil</label>

                                @if(!empty($user['profilePicture']))
                                    <div class="mb-2">
                                        <img id="preview" src="{{ $user['profilePicture'] }}" alt="Foto" width="200" class="img-thumbnail">
                                    </div>
                                @endif

                                <input type="file" name="profilePicture" class="form-control" accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control @error('fullName') is-invalid @enderror"
                                    name="fullName" value="{{ old('fullName', $user['fullName']) }}">
                                @error('fullName')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username', $user['username']) }}">
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control @error('role') is-invalid @enderror" name="role" id="role" required>
                                    <option value="user" {{ old('role', $user['role'] ?? '') === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ old('role', $user['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Update</button>
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
        reader.onload = function(){
            const output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
