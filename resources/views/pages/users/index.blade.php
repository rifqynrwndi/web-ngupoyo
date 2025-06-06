@extends('layouts.app')

@section('title', 'Pegawai')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Pegawai</h1>
                <div class="section-header-button">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah Pegawai</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Pegawai</a></div>
                    <div class="breadcrumb-item">Semua Pegawai</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Daftar Pegawai</h2>
                <p class="section-lead">
                    Halaman ini menampilkan semua pegawai yang terdaftar di sistem. Anda dapat mengelola karyawan, seperti mengedit, menghapus, dan lainnya.
                </p>

                <div class="row mt-4">
                    <div class="col-12">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <h4>Semua Pegawai</h4>
                            </div>
                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Foto</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Role</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $profileSrc = $user['profilePicture'] === 'user.jpg'
                                                                ? asset('img/avatar/user.png')
                                                                : $user['profilePicture'];
                                                        @endphp
                                                        <img src="{{ $profileSrc }}" class="rounded-circle" width="60" height="60" >
                                                    </td>
                                                    <td>{{ $user['fullName'] }}</td>
                                                    <td>{{ $user['username'] }}</td>
                                                    <td><span class="badge badge-{{ $user['role'] === 'admin' ? 'success' : 'primary' }}">{{ ucfirst($user['role']) }}</span></td>                                                    <td class="text-center">
                                                        <a href="{{ route('users.edit', $user['_id']) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('users.destroy', $user['_id']) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No users found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="mt-3">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
