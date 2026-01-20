@extends('layouts.app', [
    'activePage' => 'statistic_user',
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
      <input type="text" name="daterange" class="form-control daterange" id="daterange-input" />
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
        <h3 class="page-title"> Statistik User {{ parse_date_format($startDate) }} - {{ parse_date_format($endDate) }}</h3>
      </div>
      <div id="user-statistic-chart"></div>
    </div>
  </div>
</div>

@endsection

@push('assets')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
initChart()
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

function initChart() {
  var options = {
    chart: {
      type: 'area'
    },
    series: [{
      name: 'User',
      data: {{ Js::from($count) }},
    }],
    xaxis: {
      categories: {{ Js::from($days) }}
    }
  }

  var chart = new ApexCharts(document.querySelector("#user-statistic-chart"), options);

  chart.render();
}
</script>
@endpush
