@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Detail Permission</h1>
</div>

<div class="section-body">
    <div class="card">
        <div class="card-body">
            <p><strong>Tanggal Mulai:</strong> {{ $permission['tanggalMulai'] }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ $permission['tanggalSelesai'] }}</p>
            <p><strong>Jenis Permission:</strong> {{ $permission['jenisPermission'] }}</p>
            <p><strong>Alasan:</strong> {{ $permission['alasan'] }}</p>
            <p><strong>Dokumen Pendukung:</strong>
                @if (!empty($permission['dokumenPendukung']))
                    <a href="{{ $permission['dokumenPendukung'] }}" target="_blank">Lihat Dokumen</a>
                @else
                    Tidak ada
                @endif
            </p>
        </div>
    </div>

    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
