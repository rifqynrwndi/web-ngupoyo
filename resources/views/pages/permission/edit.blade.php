@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Edit Permission</h1>
</div>

<div class="section-body">
    <form action="{{ route('permissions.update', $permission['_id']) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggalMulai" class="form-control" value="{{ $permission['tanggalMulai'] }}" required>
        </div>

        <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggalSelesai" class="form-control" value="{{ $permission['tanggalSelesai'] }}" required>
        </div>

        <div class="form-group">
            <label>Jenis Permission</label>
            <input type="text" name="jenisPermission" class="form-control" value="{{ $permission['jenisPermission'] }}" required>
        </div>

        <div class="form-group">
            <label>Alasan</label>
            <textarea name="alasan" class="form-control" required>{{ $permission['alasan'] }}</textarea>
        </div>

        <div class="form-group">
            <label>Link Dokumen Pendukung</label>
            <input type="url" name="dokumenPendukung" class="form-control" value="{{ $permission['dokumenPendukung'] }}">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
