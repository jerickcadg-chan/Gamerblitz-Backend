@extends('layouts.app', [
    'activePage' => 'statistic_affiliate',
])

@section('content')

<div class="stretch-card">
  <div class="card">
    <div class="card-body">

      <div class="page-header">
        <h3 class="page-title">Affiliate Ranking (NET Lifetime Commission)</h3>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Affiliate</th>
              <th>Email</th>
              <th>Total Commission (NET)</th>
              <th>Current Balance</th>
            </tr>
          </thead>
          <tbody>
@forelse ($affiliates as $index => $affiliate)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $affiliate->affiliate_name }}</td>
    <td>{{ $affiliate->affiliate_email }}</td>
    <td>{{ currency_format($affiliate->net_total) }}</td>
    <td>{{ currency_format($affiliate->current_balance) }}</td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center">No Data</td>
</tr>
@endforelse
</tbody>
        </table>
      </div>

    </div>
  </div>
</div>

@endsection
