@extends('layouts.app', [
    'activePage' => 'account',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('account.index') }}">{{ $title }}</a></li>
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
                            <th> Title </th>
                            <th> Price </th>
                            <th> Kode </th>
                            <th> Winrate </th>
                            <th> Skin </th>
                            <th> Heroes </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $index => $account)
                        <tr>
                            <td>{{ $accounts->firstItem() + $index }}</td>
                            <td><a href="{{ $account->full_slug }}" target="_blank">{{ $account->title }}</a></td>
                            <td>{{ rp_format($account->price) }}</td>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->winrate }}</td>
                            <td>{{ $account->skin }}</td>
                            <td>{{ $account->heroes }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('account.show', $account),
                                    'edit_url' => route('account.edit', $account),
                                    'delete_url' => route('account.destroy', $account)
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
                    {!! $accounts->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

