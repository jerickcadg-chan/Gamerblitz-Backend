@extends('layouts.app', [
    'activePage' => 'user',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>No Handphone</th>
                        <td>{{ $user->phone_number }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ $user->role }}</td>
                    </tr>
                    <tr>
                        <th>Tgl Dibuat</th>
                        <td>{{ parse_date_time($user->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Tgl Diupdate</th>
                        <td>{{ parse_date_time($user->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
