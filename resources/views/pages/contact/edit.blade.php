@extends('layouts.app')

@section('title', 'Edit Kontak')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Edit Kontak</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{route('contacts.index')}}">Kontak</a></div>
                    <div class="breadcrumb-item">Edit Kontak</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Edit Kontak</h2>

                <div class="card">
                    <form action="{{ route('contacts.update', $contact['userId']['_id']) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="card-header">
                            <h4>Update Kontak</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Depan</label>
                                <input type="text" class="form-control @error('firstName') is-invalid @enderror"
                                    name="firstName" value="{{ old('firstName', $contact['firstName']) }}">
                                @error('firstName')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Nama Belakang</label>
                                <input type="text" class="form-control @error('lastName') is-invalid @enderror"
                                    name="lastName" value="{{ old('lastName', $contact['lastName']) }}">
                                @error('lastName')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $contact['email']) }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    name="address" value="{{ old('address', $contact['address']) }}">
                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Nomor HP</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ old('phone', $contact['phone']) }}">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
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
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
@endpush
