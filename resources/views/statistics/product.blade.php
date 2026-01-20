@php use App\Constants\CurrencyConstant;use App\Models\Setting; @endphp
@extends('layouts.app', [
    'activePage' => 'statistic_product',
])

@section('content')

  @if(session('error'))
    <div class="mb-4">
      <div class="alert alert-danger">
        {{ session('error') }}
      </div>
    </div>
  @endif

  <form>
    <div class="row g-4">
      <div class="col-xl-3">
        <input type="text" name="daterange" class="form-control daterange" id="daterange-input"/>
      </div>
      <div class="col d-flex align-items-center">
        <button type="submit" class="btn btn-primary">Filter</button>
      </div>
    </div>
  </form>

  <div class="pt-4 stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="page-header">
          <h3 class="page-title">Product Statistic {{ parse_date_format($startDate) }}
            - {{ parse_date_format($endDate) }}</h3>
        </div>
        {{-- <div id="order-statistic-chart"></div> --}}

        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>Product</th>
              <th>Count</th>
              <th>Turnover</th>
              <th>Profit</th>
              <th>Margin</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
              <tr>
                <td>{{ $order['product_name'] }}</td>
                <td>{{ $order['count'] }}</td>
                <td>{{ currency_format($order['turnover']) }}</td>
                <td>{{ currency_format($order['profit']) }}</td>
                <td>{{ $order['profit_margin'] }}%</td>
              </tr>
            @empty
              <tr>
                <td colspan="100%" class="text-center">No Data</td>
              </tr>
            @endforelse
            <tr>
              <td><strong>Orders Total</strong></td>
              <td>{{ $orders->sum('count') }}</td>
              <td>{{ currency_format($orders->sum('turnover')) }}</td>
              <td>{{ currency_format($orders->sum('profit')) }}</td>
              <td>{{ $orders->sum('profit_margin') }}%</td>
            </tr>
            <tr>
              <td><strong>Average</strong></td>
              <td>{{ round($orders->avg('count')) }}</td>
              <td>{{ currency_format($orders->avg('turnover')) }}</td>
              <td>{{ currency_format($orders->avg('profit')) }}</td>
              <td>{{ round($orders->avg('profit_margin')) }}%</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('assets')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    initDaterange()

    function initDaterange() {
      $('.daterange').daterangepicker({
        startDate: "{{ $startDate->format('Y-m-d') }}",
        endDate: "{{ $endDate->format('Y-m-d') }}",
        locale: {
          format: 'YYYY-MM-DD'
        }
      })
    }

    function formatter(value, options) {
      return new Intl.NumberFormat("{{ CurrencyConstant::localeByCode(Setting::getBaseCurrency()) }}", {
        style: "currency",
        currency: "{{ Setting::getBaseCurrency() }}",
      }).format(value);
    }
  </script>
@endpush
