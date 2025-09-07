@extends('layouts.app', [
    'activePage' => 'exchange_rate',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('exchange_rate.index') }}">{{ $title }}</a></li>
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
              <input type="text" class="form-control" name="name" placeholder="Search currency"
                value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>No</th>
              <th> Currency </th>
              <th> Rate </th>
              <th> Action </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($exchangeRates as $index => $exchangeRate)
              <tr>
                <td>{{ 1 + $index }}</td>
                <td>{{ $exchangeRate->currency_code }}</td>
                <td>{{ currency_format($exchangeRate->rate, 8) }}</td>
                <td>
                  @if ($exchangeRate->currency_code !== 'USD')
                    @include('master.action', [
                        'view_url' => route('exchange_rate.show', $exchangeRate),
                        'edit_url' => route('exchange_rate.edit', $exchangeRate),
                        'delete_url' => route('exchange_rate.destroy', $exchangeRate),
                    ])
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="100%" class="text-center">No Data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
