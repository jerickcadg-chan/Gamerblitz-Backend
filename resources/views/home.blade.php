@extends('layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
<div class="page-header">
  <h3 class="page-title"> Dashboard</h3>
</div>

<form>
  <div class="row g-2 mb-4">
    <div class="col-lg-5">
      <label for="month-input">Bulan</label>
      <select id="month-input" class="form-control" name="month" autocomplete="off">
        @foreach(get_months() as $monthIndex => $month)
        <option value="{{ $monthIndex + 1 }}" {{ intVal($selectedMonth) === $monthIndex + 1 ? 'selected' : '' }}>{{ $month }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-lg-5">
      <label for="year-input">Tahun</label>
      <select id="year-input" class="form-control" name="year" autocomplete="off">
        @foreach(get_years_reversed() as $year)
        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-lg-2 d-flex align-items-center">
      <button type="submit" class="btn btn-sm btn-primary">Filter</button>
    </div>
  </div>
</form>

<div class="col-lg-12">
  <div class="row">
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
</div>

{{-- <div class="col-md-6 mt-4">
  <div class="card">
    <div class="card-header">
      <b>Progres Harian</b>
    </div>
    <div class="card-body">
      <table class="table table-hover table-bordered">
        <thead class="bg-gradient-danger text-white">
          <tr>
            <th>Tanggal</th>
            <th>Est. Laba</th>
            <th>Jumlah Pesanan</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($progress as $p)
          <tr>
            <td>{{ parse_date($p->date) }}</td>
            <td align="right">{{ rp_format($p->total_income) }}</td>
            <td>{{ currency_format($p->count) }}</td>
          </tr>
          @endforeach
          <tr>
            <td colspan="2" align="right">{{ rp_format($progress->sum('total_income')) }}</td>
            <td>{{ $progress->sum('count') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div> --}}
@endsection
