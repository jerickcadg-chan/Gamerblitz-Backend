@extends('layouts.app', [
    'activePage' => 'user',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
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
            <th>Name</th>
            <td>{{ $user->name }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
          </tr>
          <tr>
            <th>Phone Number</th>
            <td>{{ $user->phone_number }}</td>
          </tr>
          <tr>
            <th>Role</th>
            <td>{{ $user->role }}</td>
          </tr>
          <tr>
            <th>Balance</th>
            <td>{{ $user->balance->amount ?? 0 }}</td>
          </tr>
          <tr>
            <th>Affiliate</th>
            @isset($user->affiliate)
              <td>{{ $user->affiliate?->code }} - {{ $user->affiliate->status }}</td>
            @else
              <td>-</td>
            @endisset
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ parse_date_time($user->created_at) }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ parse_date_time($user->updated_at) }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="get" class="mb-3">
          <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control" name="code" placeholder="Search deposit code" value="{{ request('code') }}">
            </div>
            <div class="col-md-4 mb-2 pt-2">
              <button type="submit" class="btn btn-sm btn-primary">Search</button>
              <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
            </div>
          </div>
        </form>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Kode</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($deposits as $index => $deposit)
            <tr>
              <td>{{ $deposits->firstItem() + $index }}</td>
              <td>{{ parse_date_time($deposit->created_at) }}</td>
              <td>{{ $deposit->code }}</td>
              <td>{{ currency_format($deposit->total_amount) }}</td>
              <td>{!! $deposit->status_raw !!}</td>
              <td>
                @include('master.action', [
                    'view_url' => route('deposit.show', $deposit),
                ])
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="100%" class="text-center">No Data</td>
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
