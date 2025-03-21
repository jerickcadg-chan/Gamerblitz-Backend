@extends('layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
<form>
  <div class="row g-2 mb-4">
    <div class="col-xl-5">
      <label for="month-input">Bulan</label>
      <select id="month-input" class="form-control" name="month" autocomplete="off">
        @foreach(get_months() as $monthIndex => $month)
        <option value="{{ $monthIndex + 1 }}" {{ intVal($selectedMonth) === $monthIndex + 1 ? 'selected' : '' }}>{{ $month }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xl-5">
      <label for="year-input">Tahun</label>
      <select id="year-input" class="form-control" name="year" autocomplete="off">
        @foreach(get_years_reversed() as $year)
        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-xl-2 d-flex align-items-center">
      <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </div>
  </div>
</form>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-gradient-danger card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Jumlah Transaksi <i class="mdi mdi-chart-line mdi-24px float-right"></i>
        </h4>
        <h2>{{ $orderSum['total'] }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Omset <i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
        </h4>
        <h2>{{ rp_format($orderSum['turnover']) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Profit <i class="mdi mdi-diamond mdi-24px float-right"></i>
        </h4>
        <h2>{{ rp_format($orderSum['profit']) }} <small>({{ $orderSum['profitPercent'] }}%)</small></h2>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-8">
    <div id="last-week-chart"></div>
  </div>
  <div class="col-md-4">
    <div class="card bg-gradient-primary card-img-holder text-white">
      <div class="card-body">
        <h4 class="mb-0 font-weight-normal">Jumlah Transaksi Hari Ini</h4>
        <h3 class="mb-4">{{ rp_format($orderToday['total']) }}</h3>
        <h4 class="mb-0 font-weight-normal">Omset Hari ini</h4>
        <h3 class="mb-4">{{ rp_format($orderToday['turnover']) }}</h3>
        <h4 class="mb-0 font-weight-normal">Profit Hari Ini</h4>
        <h3 class="mb-4">{{ rp_format($orderToday['profit']) }}</h3>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
var options = {
  chart: {
    type: 'area'
  },
  series: [{
    name: 'Omset',
    data: {{ Js::from($orderPastWeek['turnover']) }},
  }, {
    name: 'Profit',
    data: {{ Js::from($orderPastWeek['profit']) }}
  }],
  xaxis: {
    categories: {{ Js::from($orderPastWeek['days']) }}
  }
}

var chart = new ApexCharts(document.querySelector("#last-week-chart"), options);

chart.render();
</script>
@endpush
