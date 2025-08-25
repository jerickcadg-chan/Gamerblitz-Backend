@extends('layouts.app', [
    'activePage' => 'deposit',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('deposit.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="get" class="mb-3">
          <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control" name="name" placeholder="Cari nama user" value="{{ request('name') }}">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" name="code" placeholder="Cari kode deposit" value="{{ request('code') }}">
            </div>
            <div class="col-md-2 mb-2 pt-2">
              <button type="submit" class="btn btn-sm btn-primary">Cari</button>
              <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
            </div>
          </div>
        </form>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Kode</th>
            <th>User</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($deposits as $index => $deposit)
            <tr>
              <td>{{ $deposits->firstItem() + $index }}</td>
              <td>{{ parse_date_time($deposit->created_at) }}</td>
              <td>{{ $deposit->code }}</td>
              <td>{{ $deposit->user->name }}</td>
              <td>{{ rp_format($deposit->total_amount) }}</td>
              <td>{!! $deposit->status_raw !!}</td>
              <td>
                @include('master.action', [
                    'view_url' => route('deposit.show', $deposit),
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
          {!! $deposits->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
