@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Detail Izin</h1>
</div>

<div class="section-body">
    <div class="card">
        <div class="card-body">
            <p><strong>Tanggal Mulai:</strong> {{ $permission['tanggalMulai'] }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ $permission['tanggalSelesai'] }}</p>
            <p><strong>Jenis Izin:</strong> {{ $permission['jenisPermission'] }}</p>
            <p><strong>Alasan:</strong> {{ $permission['alasan'] }}</p>
            <p><strong>Dokumen Pendukung:</strong>
                @if (!empty($permission['dokumenPendukung']))
                    <a href="{{ $permission['dokumenPendukung'] }}" target="_blank">Lihat Dokumen</a>
                @else
                    Tidak ada
                @endif
            </p>

            @if ($permission['status'] !== 'Disetujui' && $permission['status'] !== 'Ditolak')
                <div class="mt-4">
                    {{-- Approve Button --}}
                    <form action="{{ route('permissions.approve', $permission['_id']) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Setujui
                        </button>
                    </form>

                    {{-- Reject Button --}}
                    <form action="{{ route('permissions.reject', $permission['_id']) }}" method="POST" class="d-inline mx-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </form>
                </div>
            @else
                <p><strong>Status:</strong> {{ $permission['status'] }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
@endsection
