@extends('layouts.app', [
    'activePage' => 'report',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('report.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Order Report</h4>
        </div>
        <div class="card-body">
          <form method="post" action="{{ route('report.get.order') }}">
            @csrf
            <div class="form-group">
              <label for="order_start_date">Start Date</label>
              <input type="date" name="order_start_date" class="form-control" value="{{ old('order_start_date') }}" required>
            </div>
            <div class="form-group">
              <label for="order_end_date">End Date</label>
              <input type="date" name="order_end_date" class="form-control" value="{{ old('order_end_date') }}" required>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success w-100"><i class="mdi mdi-download"></i> Download</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6 grid-margin stretch-card">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">User Report</h4>
        </div>
        <div class="card-body">
          <form method="post" action="{{ route('report.get.user') }}">
            @csrf
            <div class="form-group">
              <label for="user_start_date">Start Date</label>
              <input type="date" name="user_start_date" class="form-control" value="{{ old('user_start_date') }}" required>
            </div>
            <div class="form-group">
              <label for="user_end_date">End Date</label>
              <input type="date" name="user_end_date" class="form-control" value="{{ old('user_end_date') }}" required>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-success w-100"><i class="mdi mdi-download"></i> Download</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
