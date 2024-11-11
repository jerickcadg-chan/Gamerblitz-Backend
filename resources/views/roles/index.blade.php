@extends('layouts.app', [
    'activePage' => 'role',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                        <form method="get">
                          <input type="text" class="form-control" name="name" placeholder="Cari nama role" value="{{ request('name') }}">
                        </form>
                    </div>
                    <div class="col-md-8 text-lg-end">
                        <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Nama </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $index => $role)
                        <tr>
                            <td>{{ $roles->firstItem() + $index }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @if (collect(config('array.default_role'))->contains($role->name))
                                    @include('master.action', [
                                        'view_url' => route('role.show', $role)
                                    ])
                                @else
                                    @include('master.action', [
                                        'view_url' => route('role.show', $role),
                                        'edit_url' => route('role.edit', $role),
                                        'delete_url' => route('role.destroy', $role)
                                    ])
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2">
                    {!! $roles->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
