@extends('layouts.app')

@section('title', 'Izin')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Izin</h1>
            <div class="section-header-button">
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">Tambah Izin</a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route ('dashboard.index') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Izin</a></div>
                <div class="breadcrumb-item">Semua Izin</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('layouts.alert')
                </div>
            </div>

            <h2 class="section-title">Izin</h2>
            <p class="section-lead">
                Anda dapat mengelola semua izin yang terdaftar di sistem.
            </p>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Daftar Izin</h4>
                            <div class="card-header-action">
                                <form method="GET" action="{{ route('permissions.index') }}" class="d-flex">
                                    <input type="text" name="name" class="form-control" placeholder="Search by name" value="{{ request('name') }}">
                                    <button class="btn btn-primary ml-2"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive table-striped">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenis</th>
                                            <th>Alasan</th>
                                            <th>Tanggal Izin</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($permissions as $permission)
                                            <tr>
                                                <td>{{ $permission['userId']['fullName'] ?? '-' }}</td>
                                                <td>{{ $permission['jenisPermission'] ?? '-' }}</td>
                                                <td>{{ $permission['alasan'] ?? '-' }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($permission['tanggalMulai'])->translatedFormat('d M Y') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($permission['tanggalSelesai'])->translatedFormat('d M Y') }}
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $permission['status'] ?? 'Pending';
                                                    @endphp
                                                    <span class="badge
                                                        {{ $status == 'Disetujui' ? 'badge-success' : ($status == 'Ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{-- Show --}}
                                                    <a href="#"
                                                       class="btn btn-sm btn-primary mx-1 btn-show"
                                                       data-id="{{ $permission['_id'] }}">
                                                        <i class="fas fa-eye"></i> Show
                                                    </a>

                                                    {{-- Edit --}}
                                                    <a href="{{ route('permissions.edit', $permission['_id']) }}"
                                                       class="btn btn-sm btn-info mx-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>

                                                    {{-- Delete --}}
                                                    <form action="{{ route('permissions.destroy', $permission['_id']) }}"
                                                          method="POST"
                                                          class="d-inline mx-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger confirm-delete">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No permission data available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="float-right m-3">
                                {{ $permissions->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal Show -->
<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="showModalLabel">Detail Izin</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="modal-content-body">
            <p class="text-center">Loading...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
    function formatTanggalWaktu(datetimeStr) {
        const options = {
            day: '2-digit', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
            hour12: false
        };
        const date = new Date(datetimeStr);
        return date.toLocaleString('id-ID', options);
    }

    $(document).ready(function () {
        $('.btn-show').click(function (e) {
            e.preventDefault();
            var id = $(this).data('id');

            // Tampilkan loading awal
            $('#modal-content-body').html('<p class="text-center">Loading...</p>');
            $('#showModal').modal('show');

            // Request AJAX
            $.ajax({
            url: '/permissions/' + id + '/show-modal',
            method: 'GET',
            success: function (res) {
                let filePreview = '-';
                if (res.dokumenPendukung) {
                    // Cek jika file adalah gambar
                    const isImage = /\.(jpg|jpeg|png|gif)$/i.test(res.dokumenPendukung);
                    if (isImage) {
                        filePreview = `<img src="${res.dokumenPendukung}" class="img-fluid rounded" alt="Dokumen Pendukung">`;
                    } else {
                        filePreview = `<a href="${res.dokumenPendukung}" target="_blank" class="btn btn-outline-primary">Lihat Dokumen</a>`;
                    }
                }

                let html = `
                    <table class="table table-bordered">
                        <tr><th>Nama</th><td>${res.userId?.fullName ?? '-'}</td></tr>
                        <tr><th>Jenis</th><td>${res.jenisPermission ?? '-'}</td></tr>
                        <tr><th>Alasan</th><td>${res.alasan ?? '-'}</td></tr>
                        <tr><th>Tanggal Mulai</th><td>${formatTanggalWaktu(res.tanggalMulai)}</td></tr>
                        <tr><th>Tanggal Selesai</th><td>${formatTanggalWaktu(res.tanggalSelesai)}</td></tr>
                        <tr><th>Status</th><td>${res.status ?? '-'}</td></tr>
                        <tr><th>Dokumen Pendukung</th><td>${filePreview}</td></tr>
                    </table>
                `;

                if (res.status === 'Disetujui') {
                    html += `
                        <div class="text-center mt-3">
                            <form action="/permissions/${res._id}/reject" method="POST" class="d-inline form-reject">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    `;
                } else if (res.status === 'Ditolak') {
                    html += `
                        <div class="text-center mt-3">
                            <form action="/permissions/${res._id}/approve" method="POST" class="d-inline form-approve">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </form>
                        </div>
                    `;
                } else if (res.status !== 'Disetujui' && res.status !== 'Ditolak') {
                    html += `
                        <div class="text-center mt-3">
                            <form action="/permissions/${res._id}/approve" method="POST" class="d-inline form-approve">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </form>

                            <form action="/permissions/${res._id}/reject" method="POST" class="d-inline form-reject ml-2">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                        </div>
                    `;
                }
                $('#modal-content-body').html(html);
            },
            error: function (xhr) {
                console.error(xhr);
                $('#modal-content-body').html('<p class="text-danger text-center">Failed to load data.</p>');
            }
        });
    });
});
</script>
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
