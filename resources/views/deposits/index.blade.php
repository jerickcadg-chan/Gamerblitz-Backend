@extends('layouts.app', [
    'activePage' => 'deposit',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('deposit.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="get" class="mb-3">
          <div class="row">
            <div class="col-md-4">
              <input type="text" class="form-control" name="name" placeholder="Search user name" value="{{ request('name') }}">
            </div>
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
             <th>User</th>
             <th>Nominal</th>
             <th>Status</th>
             <th>Updated By</th>
             <th>Action</th>
           </tr>
          </thead>
          <tbody>
          @forelse ($deposits as $index => $deposit)
            <tr>
              <td>{{ $deposits->firstItem() + $index }}</td>
              <td>{{ parse_date_time($deposit->created_at) }}</td>
              <td>{{ $deposit->code }}</td>
              <td>{{ $deposit->user->name }}</td>
               <td>{{ currency_format($deposit->total_amount) }}</td>
               <td>{!! $deposit->status_raw !!}</td>
               <td>
                 @if($deposit->updater)
                   <a href="{{ route('user.show', $deposit->updater) }}">{{ $deposit->updater->name }}</a>
                 @else
                   -
                 @endif
               </td>
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
