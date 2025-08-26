@extends('layouts.app', [
    'activePage' => 'discount',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('discount.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-4 mb-2">
            <form method="get">
              <input type="text" class="form-control" name="name" placeholder="Search discount name" value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th> # </th>
            <th> Name </th>
            <th> Discount </th>
            <th> Period </th>
            <th> Status </th>
            <th> Action </th>
          </tr>
          </thead>
          <tbody>
          @forelse ($discounts as $index => $discount)
            <tr>
              <td>{{ $discounts->firstItem() + $index }}</td>
              <td>{{ $discount->name }} {!! $discount->code != null ? '<span class="text-primary">('. $discount->code .')</span>' : null !!}</td>
              <td>{{ $discount->discount }}</td>
              <td>{{ parse_date($discount->start_date) }} - {{ parse_date($discount->end_date) }}</td>
              <td>{!! $discount->status_label !!}</td>
              <td>
                @include('master.action', [
                    'view_url' => route('discount.show', $discount),
                    'edit_url' => route('discount.edit', $discount),
                    'delete_url' => route('discount.destroy', $discount)
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
          {!! $discounts->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
