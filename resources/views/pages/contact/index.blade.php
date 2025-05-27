@extends('layouts.app')

@section('title', 'Contacts')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Contacts</h1>
                <div class="section-header-button">
                    <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add New</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href=" {{ route ('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Contacts</a></div>
                    <div class="breadcrumb-item">All Contacts</div>
                </div>
            </div>
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Contacts</h2>
                <p class="section-lead">
                    You can manage all Contacts, such as editing, deleting and more.
                </p>


                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Contacts</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right">
                                    <form method="GET" action="{{ route('contacts.index') }}">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search by name" name="name">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="clearfix mb-3"></div>

                                <div class="table-responsive">
                                    <table class="table-striped table">
                                        <tr>

                                            <th>Nama</th>
                                            <th>Alamat</th>
                                            <th>Email</th>
                                            <th>Nomor HP</th>

                                            <th class="text-center">Action</th>
                                        </tr>
                                        @foreach ($contacts as $contact)
                                        <tr>
                                            <td>{{ ($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? '') ?: '-' }}</td>
                                            <td>{{ $contact['address'] ?? '-' }}</td>
                                            <td>{{ $contact['email'] ?? '-' }}</td>
                                            <td>{{ $contact['phone'] ?? '-' }}</td>
                                            <td class="text-center">
                                                @if (isset($contact['userId']['_id']))
                                                    <a href="{{ route('contacts.edit', $contact['userId']['_id']) }}" class="btn btn-sm btn-info mx-1">
                                                        <i class="fas fa-eye"></i> Edit
                                                    </a>
                                                @endif
                                                @if (isset($contact['userId']['_id']))
                                                    <form action="{{ route('contacts.destroy', $contact['userId']['_id']) }}" method="POST" class="d-inline mx-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger confirm-delete">
                                                            <i class="fas fa-times"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </table>
                                <div class="mt-3">
                                        {{ $contacts->links() }}
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
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
