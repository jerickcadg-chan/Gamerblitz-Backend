@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                    </div>
                    <div class="col-md-8 text-lg-end">
                        <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th>Nama</th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payment_methods as $index => $payment_method)
                        <tr>
                            <td>{{ ++$loop->index }}</td>
                            <td>{{ $payment_method->name }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('payment_method.show', $payment_method),
                                    'edit_url' => route('payment_method.edit', $payment_method),
                                    'delete_url' => route('payment_method.destroy', $payment_method)
                                ])
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
                    {!! $payment_methods->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

