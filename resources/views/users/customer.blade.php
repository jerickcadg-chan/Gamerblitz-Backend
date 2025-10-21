@extends('layouts.app', [
    'activePage' => 'customer',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="get">
          <div class="row mb-2">
            <div class="col-md-4 mb-2">
              <input type="text" class="form-control" name="name" placeholder="Search user name" value="{{ request('name') }}">
            </div>
            <div class="col-md-4 mb-2">
              <input type="text" class="form-control" name="email" placeholder="Search user email" value="{{ request('email') }}">
            </div>
            <div class="col-md-4 mb-2">
              <input type="text" class="form-control" name="phone" placeholder="Search user phone number" value="{{ request('phone') }}">
            </div>
            <div class="col-md-4 pt-2">
              <div class="form-check form-check-inline">
                <label class="form-check-label" for="banned">
                  <input class="form-check-input" type="checkbox" id="banned" name="banned" value="1" {{ request('banned') ? 'checked' : '' }}>
                  Only Banned Users
                </label>
              </div>
            </div>
            <div class="col-md-4 pt-2">
              <div class="form-check form-check-inline">
                <label class="form-check-label" for="affiliate">
                  <input class="form-check-input" type="checkbox" id="affiliate" name="affiliate" value="1" {{ request('affiliate') ? 'checked' : '' }}>
                  Only Affiliate Users
                </label>
              </div>
            </div>
            <div class="col-md-4 pt-2">
              <button type="submit" class="btn btn-sm btn-primary">Search</button>
              <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
            </div>
          </div>
        </form>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Balance</th>
            <th>Role</th>
            <th>Status</th>
            <th>Verified At</th>
            <th>Is Affiliate</th>
            <th>Affiliate Bonus</th>
            <th>Action</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($users as $index => $user)
            <tr>
              <td>{{ $users->firstItem() + $index }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->phone_number }}</td>
              <td>{{ currency_format($user->balance->amount ?? 0) }}</td>
              <td>{{ $user->role }}</td>
              <td>
                @if($user->banned_at)
                  <span class="badge bg-danger">Banned</span>
                @else
                  <span class="badge bg-success">Active</span>
                @endif
              </td>
              <td>{{ $user->email_verified_at ? parse_date_time($user->email_verified_at) : "-" }}</td>
              <td>{{ $user->affiliate?->status === 'active' ? "✅" : "❌ " }}</td>
              <td>{{ currency_format($user->affiliate?->balance ?? 0) }}</td>
              <td>
                @if ($user->id == 1)
                  @include('master.action', [
                      'view_url' => route('user.show', $user),
                      'edit_url' => route('user.edit', $user),
                  ])
                @else
                  <a class="btn btn-gradient-info btn-sm" data-bs-toggle="tooltip" title="Manual Balance" data-bs-placement="top" href="{{ route('user.top-up-manual', $user) }}">
                      <i class="mdi mdi-cash menu-icon"></i>
                  </a>
                  @include('master.action', [
                      'view_url' => route('user.show', $user),
                      'edit_url' => route('user.edit', $user),
                      // 'delete_url' => route('user.destroy', $user)
                  ])
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">No Data</td>
            </tr>
          @endforelse
          </tbody>
        </table>
        <div class="mt-2">
          {!! $users->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
