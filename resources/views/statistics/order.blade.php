@extends('layouts.app', [
    'activePage' => 'statistic_order',
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
    <h3 class="page-title"> Statistik Transaksi {{ parse_date_format($startDate) }} - {{ parse_date_format($endDate) }}</h3>
  </div>
  <div id="order-statistic-chart"></div>
</div>

<div class="pt-4 stretch-card">
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Omset</th>
            <th>Profit</th>
            <th>Margin</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
          <tr>
            <td>{{ $order['date'] }}</td>
            <td>{{ $order['count'] }}</td>
            <td>{{ $order['turnover'] }}</td>
            <td>{{ $order['profit'] }}</td>
            <td>{{ $order['profit_margin'] }}%</td>
          </tr>
          @empty
          <tr>
            <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
          </tr>
          @endforelse
          <tr>
            <td><strong>Total Transaksi</strong></td>
            <td>{{ $orders->sum('count') }}</td>
            <td>{{ $orders->sum('turnover') }}</td>
            <td>{{ $orders->sum('profit') }}</td>
            <td>{{ $orders->sum('profit_margin') }}%</td>
          </tr>
          <tr>
            <td><strong>Rata - rata</strong></td>
            <td>{{ round($orders->avg('count')) }}</td>
            <td>{{ round($orders->avg('turnover')) }}</td>
            <td>{{ round($orders->avg('profit')) }}</td>
            <td>{{ round($orders->avg('profit_margin')) }}%</td>
          </tr>
        </tbody>
      </table>
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
    data: {{ Js::from($orders->pluck('turnover')) }},
  }, {
    name: 'Profit',
    data: {{ Js::from($orders->pluck('profit')) }}
  }],
  yaxis: {
    labels: {
      formatter: value => formatRupiah(value)
    }
  },
  xaxis: {
    categories: {{ Js::from($orders->pluck('date')) }}
  }
}

var chart = new ApexCharts(document.querySelector("#order-statistic-chart"), options);

chart.render();

function formatRupiah(value, options) {
  const round = options?.round || true;
  const roundedVal = round ? Math.round(value) : value;
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(roundedVal);
}
</script>
@endpush
