@extends('layouts.app')

@section('title', 'eCommerce Dashboard')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-weight-bold">eCommerce Dashboard</h3>
        <form action="{{ route('ecommerce.toggle-maintenance') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn {{ $maintenanceMode ? 'btn-danger' : 'btn-outline-secondary' }}">
            <i class="mdi mdi-wrench me-1"></i>
            Maintenance: {{ $maintenanceMode ? 'ON' : 'OFF' }}
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="row mb-4">
    <div class="col-12">
      <form action="{{ route('ecommerce.dashboard') }}" method="GET" class="d-flex gap-3 align-items-end">
        <div>
          <label class="form-label">Month</label>
          <select name="month" class="form-select">
            @for ($i = 1; $i <= 12; $i++)
              <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
              </option>
            @endfor
          </select>
        </div>
        <div>
          <label class="form-label">Year</label>
          <select name="year" class="form-select">
            @for ($i = now()->year; $i >= now()->year - 5; $i--)
              <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
      </form>
    </div>
  </div>

  {{-- Stats Cards --}}
<div class="row">
  <div class="col-md-3 stretch-card grid-margin">
    <div class="card bg-gradient-primary card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Orders Count <i class="mdi mdi-chart-line mdi-24px float-right"></i></h4>
        <h2 class="mb-5">{{ number_format($ordersCount) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 stretch-card grid-margin">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Turnover <i class="mdi mdi-bookmark-outline mdi-24px float-right"></i></h4>
        <h2 class="mb-5">PHP {{ number_format($turnover, 2) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 stretch-card grid-margin">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Profit <i class="mdi mdi-diamond mdi-24px float-right"></i></h4>
        <h2 class="mb-5">PHP {{ number_format($profit, 2) }} ({{ $profitPercentage }}%)</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3 stretch-card grid-margin">
    <div class="card bg-gradient-warning card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Pending Orders <i class="mdi mdi-clock mdi-24px float-right"></i></h4>
        <h2 class="mb-5">{{ number_format($pendingOrdersCount) }}</h2>
      </div>
    </div>
  </div>
</div>


  {{-- Today Stats --}}
  <div class="row">
    <div class="col-md-6 stretch-card grid-margin">
      <div class="card">
  <div class="card-body">
    <h4 class="card-title">Today's Stats</h4>
    <div class="row">
      <div class="col-4">
        <p class="text-muted mb-1">Orders Today</p>
        <h4>{{ $todayOrdersCount }}</h4>
      </div>
      <div class="col-4">
        <p class="text-muted mb-1">Revenue Today</p>
        <h4>PHP {{ number_format($todayTurnover, 2) }}</h4>
      </div>
      <div class="col-4">
        <p class="text-muted mb-1">Profit Today</p>
        <h4>PHP {{ number_format($todayProfit, 2) }}</h4>
      </div>
    </div>
  </div>
</div>
    </div>
    <div class="col-md-6 stretch-card grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Shop Overview</h4>
          <div class="d-flex justify-content-between mt-4">
            <div>
              <p class="text-muted mb-1">Active Products</p>
              <h3>{{ number_format($productsCount) }}</h3>
            </div>
            <div>
              <p class="text-muted mb-1">Active Categories</p>
              <h3>{{ number_format($categoriesCount) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Weekly Chart --}}
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Last 7 Days Revenue</h4>
          <canvas id="weeklyChart" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const weeklyData = @json($weeklyData);
  const labels = weeklyData.map(d => d.date);
  const data = weeklyData.map(d => parseFloat(d.total));

  new Chart(document.getElementById('weeklyChart'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Revenue (PHP)',
        data: data,
        borderColor: 'rgb(75, 192, 192)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        tension: 0.1,
        fill: true
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'PHP ' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
</script>
@endpush
