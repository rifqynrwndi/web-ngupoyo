@extends('layouts.app')

@section('title', 'Tambah Pegawai')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Tambah Pegawai</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('users.index') }}">Pegawai</a></div>
                    <div class="breadcrumb-item">Tambah Pegawai</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Pegawai</h2>
                <div class="card">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="card-header"><h4>Form Pegawai</h4></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="fullName">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input id="password" name="password" type="password" class="form-control" placeholder="Enter password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small id="passwordHelpBlock" class="form-text text-muted mt-2">
                                    <ul id="password-criteria" style="padding-left: 20px; list-style: disc;">
                                        <li id="length" class="text-danger">Minimal 6 karakter</li>
                                        <li id="uppercase" class="text-danger">Minimal satu huruf besar</li>
                                        <li id="lowercase" class="text-danger">Minimal satu huruf kecil</li>
                                        <li id="number" class="text-danger">Minimal satu angka</li>
                                    </ul>
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Konfirmasi password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Simpan Data Pegawai</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const targetInput = document.querySelector(button.getAttribute('data-target'));
            const icon = button.querySelector('i');
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Live validation for password
    document.getElementById('password').addEventListener('input', function() {
        const value = this.value;
        document.getElementById('length').classList.toggle('text-success', value.length >= 6);
        document.getElementById('length').classList.toggle('text-danger', value.length < 6);

        document.getElementById('uppercase').classList.toggle('text-success', /[A-Z]/.test(value));
        document.getElementById('uppercase').classList.toggle('text-danger', !/[A-Z]/.test(value));

        document.getElementById('lowercase').classList.toggle('text-success', /[a-z]/.test(value));
        document.getElementById('lowercase').classList.toggle('text-danger', !/[a-z]/.test(value));

        document.getElementById('number').classList.toggle('text-success', /[0-9]/.test(value));
        document.getElementById('number').classList.toggle('text-danger', !/[0-9]/.test(value));
    });
</script>

<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const targetInput = document.querySelector(button.getAttribute('data-target'));
            const icon = button.querySelector('i');
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }); 
</script>
@endpush
