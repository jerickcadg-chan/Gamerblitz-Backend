@php use App\Constants\CurrencyConstant;use App\Constants\StatusConst;use App\Models\Setting; @endphp
@extends('layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
  @can('View Dashboard')
    {{-- FILTER SECTION --}}
    <div class="card mb-3 mb-md-4">
      <div class="card-body py-3">
        <form class="row align-items-end g-2 g-md-3">
          <div class="col-6 col-md-3">
            <label for="month-input" class="form-label mb-1">Month</label>
            <select id="month-input" class="form-control form-control-sm" name="month">
              @foreach(get_months() as $monthIndex => $month)
                <option value="{{ $monthIndex + 1 }}" {{ intVal($selectedMonth) === $monthIndex + 1 ? 'selected' : '' }}>{{ $month }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label for="year-input" class="form-label mb-1">Year</label>
            <select id="year-input" class="form-control form-control-sm" name="year">
              @foreach(get_years_reversed() as $year)
                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-md-2 mt-2 mt-md-0">
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="mdi mdi-filter-outline me-1"></i> Filter
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- GPDS RESELLER BALANCE --}}
    @if(isset($gpdsBalance))
    <div class="row mb-3 mb-md-4">
      <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #0f1923 0%, #1a2a3a 50%, #0f1923 100%); border: 1px solid #FF8C00; box-shadow: 0 0 20px rgba(255, 140, 0, 0.15);">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #FF8C00, #FFA500); border-radius: 10px;">
                    <i class="mdi mdi-wallet" style="font-size: 24px; color: #fff;"></i>
                  </div>
                  <div>
                    <p class="mb-0 text-muted small">GPDS Reseller Wallet Balance</p>
                    <h3 class="mb-0" style="color: #FF8C00; font-weight: 700; font-size: 1.5rem;">
                      @if($gpdsBalance['error'])
                        <span class="text-muted" style="font-size: 0.9rem;">Unable to fetch balance</span>
                      @else
                        {{ currency_format($gpdsBalance['balance']) }}
                      @endif
                    </h3>
                  </div>
                </div>
                @if($gpdsBalance['error'])
                  <small class="text-danger"><i class="mdi mdi-alert-circle me-1"></i>{{ $gpdsBalance['error'] }}</small>
                @else
                  <small class="text-muted">This is your remaining balance on the main GPDS platform. Orders placed on this site will deduct from this wallet.</small>
                @endif
              </div>
              <div class="text-end d-none d-sm-block">
                <small class="text-muted d-block mb-1">Currency: {{ $gpdsBalance['currency'] }}</small>
                <span class="badge" style="background: rgba(255, 140, 0, 0.2); color: #FF8C00; font-size: 0.7rem;">
                  <i class="mdi mdi-sync me-1"></i>Live from GPDS
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- QUICK ACTIONS --}}
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
              <a href="{{ route('order.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-cart-outline me-1"></i> View Orders
              </a>
              <a href="{{ route('product.index') }}" class="btn btn-outline-info btn-sm">
                <i class="mdi mdi-package-variant me-1"></i> Manage Products
              </a>
              <a href="{{ route('user.index') }}" class="btn btn-outline-success btn-sm">
                <i class="mdi mdi-account-group me-1"></i> Manage Users
              </a>
              <a href="{{ route('statistic.order') }}" class="btn btn-outline-warning btn-sm">
                <i class="mdi mdi-chart-bar me-1"></i> View Reports
              </a>
              @if($pendingOrders > 0)
                <a href="{{ route('order.index', ['status' => 'pending']) }}" class="btn btn-danger btn-sm">
                  <i class="mdi mdi-alert-circle me-1"></i> {{ $pendingOrders }} Pending Orders
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- MAIN STATS ROW 1: Orders, Turnover, Gross Profit, Net Profit --}}
    <div class="row mb-3 mb-md-4 g-2 g-md-3">
      {{-- Orders Count Card --}}
      <div class="col-6 col-lg-3">
        <div class="card bg-gradient-primary h-100">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="mb-1 text-white-50 small">Orders Count</p>
                <h3 class="text-white mb-0 fs-5 fs-md-4">{{ number_format($orderSum['total']) }}</h3>
              </div>
              <div class="stat-icon flex-shrink-0 ms-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 50%;">
                <i class="mdi mdi-cart-outline" style="font-size: 22px; color: #fff;"></i>
              </div>
            </div>
            <p class="mb-0 mt-2 text-white-50 small text-truncate">
              <span class="text-white">{{ number_format($orderToday['total']) }}</span> orders today
            </p>
          </div>
        </div>
      </div>

      {{-- Turnover Card --}}
      <div class="col-6 col-lg-3">
        <div class="card bg-gradient-info h-100">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="mb-1 text-white-50 small">Turnover</p>
                <h3 class="text-white mb-0 fs-5 fs-md-4 text-truncate">{{ currency_format($orderSum['turnover']) }}</h3>
              </div>
              <div class="stat-icon flex-shrink-0 ms-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 50%;">
                <i class="mdi mdi-cash-multiple" style="font-size: 22px; color: #fff;"></i>
              </div>
            </div>
            <p class="mb-0 mt-2 text-white-50 small text-truncate">
              <span class="text-white">{{ currency_format($orderToday['turnover']) }}</span> today
            </p>
          </div>
        </div>
      </div>

      {{-- Gross Profit Card --}}
      <div class="col-6 col-lg-3">
        <div class="card bg-gradient-success h-100">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="mb-1 text-white-50 small">Gross Profit</p>
                <h3 class="text-white mb-0 fs-5 fs-md-4 text-truncate">{{ currency_format($orderSum['profit']) }}</h3>
              </div>
              <div class="stat-icon flex-shrink-0 ms-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 50%;">
                <i class="mdi mdi-trending-up" style="font-size: 22px; color: #fff;"></i>
              </div>
            </div>
            <p class="mb-0 mt-2 text-white-50 small">
              Margin: <span class="text-white">{{ $orderSum['profitMargin'] }}%</span>
            </p>
          </div>
        </div>
      </div>

      {{-- Net Profit Card - GOLD HIGHLIGHTED --}}
      <div class="col-6 col-lg-3">
        <div class="card h-100 net-profit-card" style="background: linear-gradient(135deg, #b8860b 0%, #daa520 30%, #f4d03f 50%, #daa520 70%, #b8860b 100%); border: 2px solid #ffd700; box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="mb-1 small" style="color: rgba(255,255,255,0.7);">Net Profit</p>
                <h3 class="mb-0 fs-5 fs-md-4 text-truncate" style="color: #fff; font-weight: 700; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">{{ currency_format($netProfitStats['net_profit']) }}</h3>
              </div>
              <div class="stat-icon flex-shrink-0 ms-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.25); border-radius: 50%;">
                <i class="mdi mdi-wallet-giftcard" style="font-size: 22px; color: #fff;"></i>
              </div>
            </div>
            <p class="mb-0 mt-2 small" style="color: rgba(255,255,255,0.7);">
              Net Margin: <span style="color: #fff; font-weight: 600;">{{ $netProfitStats['net_margin'] }}%</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- STATS ROW 2: Deductions & Info --}}
    <div class="row mb-3 mb-md-4 g-2 g-md-3">
      {{-- Gateway Fees Card --}}
      <div class="col-6 col-lg-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #dc3545; box-shadow: 0 0 10px rgba(220, 53, 69, 0.2);">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="text-muted mb-1 small">Gateway Fees</p>
                <h4 class="text-danger fs-6 fs-md-5 mb-1 text-truncate">-{{ currency_format($netProfitStats['gateway_fees']) }}</h4>
                <small class="text-muted d-block text-truncate">Orders: -{{ currency_format($netProfitStats['order_gateway_fees']) }}</small>
                <small class="text-muted d-block text-truncate">Deposits: -{{ currency_format($netProfitStats['deposit_gateway_fees']) }}</small>
              </div>
              <div class="d-none d-md-flex align-items-center justify-content-center flex-shrink-0 ms-2" style="width: 44px; height: 44px; background: rgba(220, 53, 69, 0.2); border-radius: 10px;">
                <i class="mdi mdi-bank-transfer text-danger" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- VAT on Fees Card --}}
      <div class="col-6 col-lg-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #ffc107; box-shadow: 0 0 10px rgba(255, 193, 7, 0.2);">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="text-muted mb-1 small">VAT on Fees (12%)</p>
                <h4 class="text-warning fs-6 fs-md-5 mb-1 text-truncate">-{{ currency_format($netProfitStats['vat_on_fees']) }}</h4>
                <small class="text-muted d-block">12% VAT on gateway fees</small>
                <small class="text-muted d-block">&nbsp;</small>
              </div>
              <div class="d-none d-md-flex align-items-center justify-content-center flex-shrink-0 ms-2" style="width: 44px; height: 44px; background: rgba(255, 193, 7, 0.2); border-radius: 10px;">
                <i class="mdi mdi-percent text-warning" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Total Users Card --}}
      <div class="col-6 col-lg-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #17a2b8;">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="text-muted mb-1 small">Total Users</p>
                <h4 class="text-info fs-6 fs-md-5 mb-1">{{ number_format($userStats['total']) }}</h4>
                <small class="text-muted d-block text-truncate"><i class="mdi mdi-account-plus"></i> {{ $userStats['newThisMonth'] }} new this month</small>
                <small class="text-muted d-block">&nbsp;</small>
              </div>
              <div class="d-none d-md-flex align-items-center justify-content-center flex-shrink-0 ms-2" style="width: 44px; height: 44px; background: rgba(23, 162, 184, 0.2); border-radius: 10px;">
                <i class="mdi mdi-account-group text-info" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Total Products Card --}}
      <div class="col-6 col-lg-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid #20c997;">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1 min-width-0">
                <p class="text-muted mb-1 small">Total Products</p>
                <h4 class="text-success fs-6 fs-md-5 mb-1">{{ number_format($productStats['total']) }}</h4>
                <small class="text-success d-block"><i class="mdi mdi-circle-medium"></i> {{ $productStats['active'] }} active</small>
                <small class="text-muted d-block">&nbsp;</small>
              </div>
              <div class="d-none d-md-flex align-items-center justify-content-center flex-shrink-0 ms-2" style="width: 44px; height: 44px; background: rgba(32, 201, 151, 0.2); border-radius: 10px;">
                <i class="mdi mdi-package-variant text-success" style="font-size: 24px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TODAY'S SUMMARY --}}
    <div class="row mb-3 mb-md-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header py-2 py-md-3">
            <h5 class="card-title mb-0 fs-6 fs-md-5">Today's Summary</h5>
          </div>
          <div class="card-body p-2 p-md-3">
            <div class="row text-center g-2">
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">Orders</p>
                <h5 class="mb-0 fs-6">{{ number_format($orderToday['total']) }}</h5>
              </div>
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">Turnover</p>
                <h5 class="mb-0 fs-6 text-truncate">{{ currency_format($orderToday['turnover']) }}</h5>
              </div>
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">Gross Profit</p>
                <h5 class="mb-0 text-success fs-6 text-truncate">{{ currency_format($orderToday['profit']) }}</h5>
              </div>
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">Gateway Fees</p>
                <h5 class="mb-0 text-danger fs-6 text-truncate">-{{ currency_format($netProfitToday['gateway_fees']) }}</h5>
              </div>
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">VAT on Fees</p>
                <h5 class="mb-0 text-warning fs-6 text-truncate">-{{ currency_format($netProfitToday['vat_on_fees']) }}</h5>
              </div>
              <div class="col-4 col-md-2">
                <p class="text-muted mb-1 small" style="font-size: 0.7rem;">Net Profit</p>
                <h5 class="mb-0 fs-6 text-truncate" style="color: #daa520; font-weight: 700;">{{ currency_format($netProfitToday['net_profit']) }}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- PENDING ORDERS ALERT --}}
    @if($pendingOrders > 0)
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="mdi mdi-alert-circle mdi-24px me-2"></i>
          <div>
            <strong>{{ $pendingOrders }} Pending Orders</strong> require your attention.
            <a href="{{ route('order.index', ['status' => 'pending']) }}" class="alert-link ms-2">View Pending Orders</a>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- CHARTS ROW --}}
    <div class="row mb-3 mb-md-4 g-2 g-md-3">
      <div class="col-12 col-lg-8 mb-3 mb-lg-0">
        <div class="card h-100">
          <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
              <h5 class="card-title mb-0 fs-6 fs-md-5">Weekly Transactions</h5>
              <a href="{{ route('statistic.order') }}" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
            <div id="last-week-chart" style="min-height: 250px;"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card h-100">
          <div class="card-body p-2 p-md-3">
            <h5 class="card-title mb-3 fs-6 fs-md-5">Orders by Status</h5>
            <div id="orders-status-chart" style="min-height: 250px;"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- RECENT ORDERS --}}
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body p-2 p-md-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
              <h5 class="card-title mb-0 fs-6 fs-md-5">Recent Orders</h5>
              <a href="{{ route('order.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th class="small">CODE</th>
                    <th class="small d-none d-sm-table-cell">DATE</th>
                    <th class="small">AMOUNT</th>
                    <th class="small">STATUS</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentOrders as $order)
                    <tr>
                      <td class="small"><a href="{{ route('order.show', $order->id) }}" class="text-info text-decoration-none">{{ $order->code }}</a></td>
                      <td class="small d-none d-sm-table-cell">{{ $order->created_at->format('M d, Y H:i') }}</td>
                      <td class="small">{{ currency_format($order->turnover) }}</td>
                      <td>
                        @if($order->status === StatusConst::SUCCESS)
                          <span class="badge bg-success" style="font-size: 0.65rem;">Success</span>
                        @elseif($order->status === StatusConst::PENDING)
                          <span class="badge bg-warning" style="font-size: 0.65rem;">Pending</span>
                        @elseif($order->status === StatusConst::FAILED)
                          <span class="badge bg-danger" style="font-size: 0.65rem;">Failed</span>
                        @else
                          <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $order->status }}</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted small">No recent orders</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  @endcan
@endsection

@php
  $currencyCode = Setting::getByKey(Setting::KEY_BASE_CURRENCY);
  $meta = CurrencyConstant::metadata($currencyCode);
@endphp

@push('js')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    // Weekly Transactions Chart
    let weeklyOptions = {
      chart: {
        type: 'area',
        height: 300,
        toolbar: { show: false },
        background: 'transparent'
      },
      series: [{
        name: 'Turnover',
        data: {!! Js::from($orderPastWeek['turnover']) !!},
      }, {
        name: 'Profit',
        data: {!! Js::from($orderPastWeek['profit']) !!}
      }],
      colors: ['#00d4ff', '#00e396'],
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.5,
          opacityTo: 0.1,
        }
      },
      stroke: { curve: 'smooth', width: 2 },
      yaxis: {
        labels: {
          formatter: value => formatter(value),
          style: { colors: '#8b949e' }
        }
      },
      xaxis: {
        categories: {!! Js::from($orderPastWeek['days']) !!},
        labels: { style: { colors: '#8b949e' } }
      },
      grid: { borderColor: '#3d4556' },
      legend: { labels: { colors: '#e4e6eb' } },
      tooltip: { theme: 'dark' }
    };
    new ApexCharts(document.querySelector("#last-week-chart"), weeklyOptions).render();

    // Orders by Status Chart
    let statusData = @json($ordersByStatus);
    let statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
    let statusValues = Object.values(statusData);
    
    if (statusValues.length > 0) {
      let statusOptions = {
        chart: {
          type: 'donut',
          height: 300,
          background: 'transparent'
        },
        series: statusValues,
        labels: statusLabels,
        colors: ['#00e396', '#feb019', '#ff4560', '#775dd0', '#00d4ff'],
        legend: {
          position: 'bottom',
          labels: { colors: '#e4e6eb' }
        },
        plotOptions: {
          pie: {
            donut: {
              labels: {
                show: true,
                total: {
                  show: true,
                  label: 'Total',
                  color: '#e4e6eb'
                }
              }
            }
          }
        },
        tooltip: { theme: 'dark' }
      };
      new ApexCharts(document.querySelector("#orders-status-chart"), statusOptions).render();
    } else {
      document.querySelector("#orders-status-chart").innerHTML = '<p class="text-muted text-center mt-5">No orders this period</p>';
    }

    function formatter(value) {
      return new Intl.NumberFormat("{{ CurrencyConstant::localeByCode(Setting::getBaseCurrency()) }}", {
        style: "currency",
        currency: "{{ Setting::getBaseCurrency() }}",
      }).format(value);
    }
  </script>
@endpush
