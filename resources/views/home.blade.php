@php use App\Constants\CurrencyConstant; @endphp
@extends('layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
@can('View Dashboard')
<form>
  <div class="row">
    <div class="col">
      <label for="month-input" class="d-block mb-2">Month</label>
      <select id="month-input" class="form-control" name="month" autocomplete="off">
        @foreach(get_months() as $monthIndex => $month)
        <option value="{{ $monthIndex + 1 }}" {{ intVal($selectedMonth) === $monthIndex + 1 ? 'selected' : '' }}>{{ $month }}</option>
        @endforeach
      </select>
    </div>
    <div class="col">
      <label for="year-input" class="d-block mb-2">Year</label>
      <select id="year-input" class="form-control" name="year" autocomplete="off">
        @foreach(get_years_reversed() as $year)
        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
      </select>
    </div>
    <div class="col d-flex align-items-center">
      <button type="submit" class="btn btn-sm btn-primary mt-3">Filter</button>
    </div>
  </div>
</form>

<div class="row pt-4 mb-4">
  <div class="col-md-4">
    <div class="card bg-gradient-dark card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Orders Count <i class="mdi mdi-chart-line mdi-24px float-right"></i>
        </h4>
        <h2>{{ numbering($orderSum['total']) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-gradient-dark card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Turnover <i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
        </h4>
        <h2>{{ currency_format($orderSum['turnover']) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-gradient-dark card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Profit <i class="mdi mdi-diamond mdi-24px float-right"></i>
        </h4>
        <h2>{{ currency_format($orderSum['profit']) }} <small>({{ $orderSum['profitMargin'] }}%)</small></h2>
      </div>
    </div>
  </div>
</div>

<div class="row pt-4">
  <div class="col-md-8">
    <div class="stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="page-header">
            <h3 class="page-title">Latest 1 Week Transactions</h3>
            <a href="{{ route('statistic.order') }}">Read more</a>
          </div>
          <div id="last-week-chart"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-img-holder">
      <div class="card-body">
        <h5 class="mb-0 font-weight-normal">Orders Count Today</h5>
        <h3 class="mb-4">{{ numbering($orderToday['total']) }}</h3>
        <h5 class="mb-0 font-weight-normal">Turnover Today</h5>
        <h3 class="mb-4">{{ currency_format($orderToday['turnover']) }}</h3>
        <h5 class="mb-0 font-weight-normal">Profit Today</h5>
        <h3 class="mb-4">{{ currency_format($orderToday['profit']) }}</h3>
      </div>
    </div>
  </div>
</div>
@endcan
@endsection

@php
    $currencyCode = \App\Models\Setting::getByKey(\App\Models\Setting::KEY_BASE_CURRENCY);
    $meta = CurrencyConstant::metadata($currencyCode);
@endphp

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let options = {
  chart: {
    type: 'area'
  },
  series: [{
    name: 'Turnover',
    data: {{ Js::from($orderPastWeek['turnover']) }},
  }, {
    name: 'Profit',
    data: {{ Js::from($orderPastWeek['profit']) }}
  }],
  yaxis: {
    labels: {
      formatter: value => formatRupiah(value)
    }
  },
  xaxis: {
    categories: {{ Js::from($orderPastWeek['days']) }}
  }
}

let chart = new ApexCharts(document.querySelector("#last-week-chart"), options);

chart.render();

function formatRupiah(value, options) {
  const round = options?.round || true;
  const roundedVal = round ? Math.round(value) : value;
  return new Intl.NumberFormat("{{ $meta['locale'] }}", {
    style: "currency",
    currency: "{{ $currencyCode }}",
    minimumFractionDigits: 0,
  }).format(roundedVal);
}
</script>
@endpush
