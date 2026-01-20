@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Logs</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th> Provider </th>
              <th> Synced At </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($fetchVariantJobs as $index => $fetchVariantJob)
              <tr>
                <td>{{ $fetchVariantJob->command_name }}</td>
                <td>{{ parse_date_time_full($fetchVariantJob->created_at) }}</td>
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