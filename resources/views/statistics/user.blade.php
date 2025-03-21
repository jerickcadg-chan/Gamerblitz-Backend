@extends('layouts.app', [
    'activePage' => 'statistic_user',
])

@section('content')
<form>
  <div class="row">
    <div class="col">
      <label for="month-input" class="d-block">Tanggal Mulai</label>
      <input type="date" name="startDate" class="form-control" required value="{{ $startDate }}" autocomplete="off" />
    </div>
    <div class="col">
      <label for="year-input" class="d-block">Tanggal Selesai</label>
      <input type="date" name="endDate" class="form-control" required value="{{ $endDate }}" autocomplete="off" />
    </div>
    <div class="col d-flex align-items-center">
      <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </div>
  </div>
</form>

<div class="pt-4">
  <div class="page-header">
    <h3 class="page-title"> Statistik User {{ parse_date_format($startDate) }} - {{ parse_date_format($endDate) }}</h3>
  </div>
  <div id="user-statistic-chart"></div>
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
    name: 'User',
    data: {{ Js::from($count) }},
  }],
  xaxis: {
    categories: {{ Js::from($days) }}
  }
}

var chart = new ApexCharts(document.querySelector("#user-statistic-chart"), options);

chart.render();
</script>
@endpush
