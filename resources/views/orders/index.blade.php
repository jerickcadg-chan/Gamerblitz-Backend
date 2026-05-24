@php use App\Constants\StatusConst;use App\Models\PaymentMethod; @endphp
@extends('layouts.app', [
    'activePage' => 'order',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('order.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  {{-- Row 1: Order Volume Statistics --}}
  <div class="row">
    {{-- Successful Orders Today --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-white mb-2" style="opacity: 0.8;">Successful Orders Today</p>
              <h4 class="text-white">{{ number_format($orderStats['orders_today']) }}</h4>
              <small class="text-white" style="opacity: 0.7;">{{ Carbon\Carbon::today()->format('d M Y') }}</small>
              <div class="mt-2">
                <small class="text-white" style="opacity: 0.8;">Yesterday: {{ number_format($orderStats['orders_yesterday']) }}</small>
                @if($orderStats['orders_today_change'] >= 0)
                  <span class="badge ms-1" style="background-color: rgba(40, 167, 69, 0.9); color: white; font-size: 11px;">+{{ $orderStats['orders_today_change'] }}%</span>
                @else
                  <span class="badge ms-1" style="background-color: rgba(220, 53, 69, 0.9); color: white; font-size: 11px;">{{ $orderStats['orders_today_change'] }}%</span>
                @endif
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-cart text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Successful Orders This Week --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-white mb-2" style="opacity: 0.8;">Successful Orders This Week</p>
              <h4 class="text-white">{{ number_format($orderStats['orders_this_week']) }}</h4>
              <small class="text-white" style="opacity: 0.7;">{{ Carbon\Carbon::now()->startOfWeek()->format('d M') }} - {{ Carbon\Carbon::now()->endOfWeek()->format('d M') }}</small>
              <div class="mt-2">
                <small class="text-white" style="opacity: 0.8;">Last Week: {{ number_format($orderStats['orders_last_week']) }}</small>
                @if($orderStats['orders_week_change'] >= 0)
                  <span class="badge ms-1" style="background-color: rgba(40, 167, 69, 0.9); color: white; font-size: 11px;">+{{ $orderStats['orders_week_change'] }}%</span>
                @else
                  <span class="badge ms-1" style="background-color: rgba(220, 53, 69, 0.9); color: white; font-size: 11px;">{{ $orderStats['orders_week_change'] }}%</span>
                @endif
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-calendar-week text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Successful Orders This Month --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-white mb-2" style="opacity: 0.8;">Successful Orders This Month</p>
              <h4 class="text-white">{{ number_format($orderStats['orders_this_month']) }}</h4>
              <small class="text-white" style="opacity: 0.7;">{{ Carbon\Carbon::now()->format('F Y') }}</small>
              <div class="mt-2">
                <small class="text-white" style="opacity: 0.8;">Last Month: {{ number_format($orderStats['orders_last_month']) }}</small>
                @if($orderStats['orders_month_change'] >= 0)
                  <span class="badge ms-1" style="background-color: rgba(40, 167, 69, 0.9); color: white; font-size: 11px;">+{{ $orderStats['orders_month_change'] }}%</span>
                @else
                  <span class="badge ms-1" style="background-color: rgba(220, 53, 69, 0.9); color: white; font-size: 11px;">{{ $orderStats['orders_month_change'] }}%</span>
                @endif
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-calendar-month text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Must Action Orders --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-white mb-2" style="opacity: 0.8;">Pending Orders</p>
              <h4 class="text-white">{{ number_format($orderStats['must_action_orders']) }}</h4>
              <small class="text-white" style="opacity: 0.7;">On-process status</small>
              <div class="mt-2">
                <small class="text-white" style="opacity: 0.8;">Needs attention</small>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-alert-circle-outline text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Row 2: Product & User Performance --}}
  <div class="row">
    {{-- Most Ordered Product This Month - Top 3 --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div style="max-width: calc(100% - 70px); width: 100%;">
              <p class="text-white mb-2" style="opacity: 0.8;">Most Ordered This Month</p>
              @if(count($orderStats['most_ordered_products']) > 0)
                @foreach($orderStats['most_ordered_products'] as $index => $product)
                  <div class="d-flex justify-content-between align-items-center {{ $index < 2 ? 'mb-1' : '' }}">
                    <small class="text-white" style="opacity: {{ $index === 0 ? '1' : '0.8' }}; font-weight: {{ $index === 0 ? '600' : '400' }};" title="{{ $product->name }}">
                      {{ $index + 1 }}. {{ $product->name }}
                    </small>
                    <small class="text-white" style="opacity: 0.9;">{{ number_format($product->order_count) }}</small>
                  </div>
                @endforeach
              @else
                <h5 class="text-white">No orders yet</h5>
              @endif
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-fire text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Highest Earning Product This Month - Top 3 --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div style="width: calc(100% - 70px);">
              <p class="text-white mb-2" style="opacity: 0.8;">Highest Earning This Month</p>
              @if(count($orderStats['highest_earning_products']) > 0)
                @foreach($orderStats['highest_earning_products'] as $index => $product)
                  <div class="d-flex justify-content-between align-items-start {{ $index < 2 ? 'mb-1' : '' }}">
                    <small class="text-white" style="opacity: {{ $index === 0 ? '1' : '0.8' }}; font-weight: {{ $index === 0 ? '600' : '400' }}; flex: 1; padding-right: 8px;">
                      {{ $index + 1 }}. {{ $product['name'] }}
                    </small>
                    <small class="text-white text-nowrap" style="opacity: 0.9;">{{ currency_format($product['total_profit']) }}</small>
                  </div>
                @endforeach
              @else
                <h5 class="text-white">No earnings yet</h5>
              @endif
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px; flex-shrink: 0;">
              <i class="mdi mdi-trophy text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Top User This Month --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); min-height: 160px;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div style="max-width: calc(100% - 70px);">
              <p class="text-white mb-2" style="opacity: 0.8;">Top Users This Month</p>
              <div class="mb-1">
                <small class="text-white" style="opacity: 0.9; font-weight: 600;">By Amount:</small>
                @if($orderStats['top_user_month_amount'] ?? null)
                  <small class="text-white d-block" title="{{ $orderStats['top_user_month_amount']->name }}">{{ $orderStats['top_user_month_amount']->name }} ({{ currency_format($orderStats['top_user_month_amount']->total_amount) }})</small>
                @else
                  <small class="text-white d-block">-</small>
                @endif
              </div>
              <div>
                <small class="text-white" style="opacity: 0.9; font-weight: 600;">By Orders:</small>
                @if($orderStats['top_user_month_orders'] ?? null)
                  <small class="text-white d-block" title="{{ $orderStats['top_user_month_orders']->name }}">{{ $orderStats['top_user_month_orders']->name }} ({{ $orderStats['top_user_month_orders']->order_count }} orders)</small>
                @else
                  <small class="text-white d-block">-</small>
                @endif
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px;">
              <i class="mdi mdi-account-star text-white" style="font-size: 32px;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Order Health - Status Breakdown --}}
    <div class="col-lg-3 col-md-6 grid-margin stretch-card">
      <div class="card" style="background: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%); min-height: 160px;">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <p class="text-white mb-1" style="opacity: 0.8; font-size: 13px;">Order Health (This Month)</p>
              <div class="d-flex align-items-center">
                <h5 class="text-white mb-0 me-2">{{ $orderStats['success_rate'] }}%</h5>
                <small class="text-white" style="opacity: 0.8;">success rate</small>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 10px; flex-shrink: 0;">
              <i class="mdi mdi-chart-donut text-white" style="font-size: 22px;"></i>
            </div>
          </div>
          <div class="text-white mb-2" style="font-size: 12px;">
            <strong>Total: {{ number_format($orderStats['total_orders_this_month']) }}</strong> orders
          </div>
          <div style="font-size: 11px; line-height: 1.6;">
            @php
              $statusConfig = [
                'success' => ['color' => '#28a745', 'label' => 'Success'],
                'failed' => ['color' => '#dc3545', 'label' => 'Failed'],
                'on-process' => ['color' => '#ffc107', 'label' => 'On-Process'],
                'pending' => ['color' => '#17a2b8', 'label' => 'Pending'],
                'delayed' => ['color' => '#ff8000', 'label' => 'Delayed'],
                'expired' => ['color' => '#6c757d', 'label' => 'Expired'],
                'refunded' => ['color' => '#6f42c1', 'label' => 'Refunded'],
                'cancelled' => ['color' => '#343a40', 'label' => 'Cancelled'],
              ];
            @endphp
            @foreach($orderStats['status_breakdown'] as $status => $count)
              @if($count > 0)
                <div class="d-flex justify-content-between align-items-center text-white" style="opacity: 0.95;">
                  <span>
                    <span class="d-inline-block me-1" style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $statusConfig[$status]['color'] ?? '#6c757d' }};"></span>
                    {{ $statusConfig[$status]['label'] ?? ucwords(str_replace('-', ' ', $status)) }}
                  </span>
                  <span>{{ number_format($count) }}</span>
                </div>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="mt-3">
          <form>
            <div class="row mb-3 g-xl-2">
              @include('master.date-range', [
                'col' => 'col-md-3',
                'timePicker' => true,
             ])
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="order_code" value="{{ request('order_code') }}"
                       placeholder="Invoice Code">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="cust_account" value="{{ request('cust_account') }}"
                       placeholder="Customer Account">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <select class="form-control select2" name="status">
                  <option value="">-- All Status --</option>
                  @foreach(config('array.order.status') as $status)
                    <option
                      value="{{ $status }}" {{ request('status') == $status ? 'selected' : null }}>{{ ucwords($status) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 col-lg-4 col-xl-3 mb-2">
                <select class="form-control select2" name="product_id">
                  <option value="">-- All Products --</option>
                  @foreach (\App\Models\Product::all() as $product)
                    <option
                      value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product['name'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2 pt-2">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-responsive">
              <thead>
               <tr class="text-center">
                 <th rowspan="2">#</th>
                 <th rowspan="2">Code</th>
                 <th rowspan="2">Product</th>
                 <th rowspan="2">Customer Account</th>
                 <th rowspan="2">Turnover</th>
                 <th rowspan="2">Capital</th>
                 <th rowspan="2">Profit</th>
                 <th rowspan="2">Net Profit</th>
                 <th rowspan="2">Status</th>
                 <th rowspan="2">Updated By</th>
                 <th colspan="2">Buyer</th>
                 <th rowspan="2">Affiliate</th>
               </tr>
              <tr class="text-center">
                <th>Email</th>
                <th>Whatsapp</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($orders as $index => $order)
                <tr>
                  <td>{{ $orders->firstItem() + $index }}</td>
                  <td>
                    @if($order->provider_ref)
                      <p>{{ $order->provider_ref }}</p>
                    @endif
                    <p class="mt-3">
                      <a href="{{ route('order.show', $order->id) }}">{{ $order->code }}</a>
                    </p>
                    <span class="text-muted">{{ parse_date_time($order->created_at) }}</span>
                  </td>
                  <td>
                    <p class="mt-3">
                      <a href="{{ route('product_item.show', $order->product_item_id) }}"
                         target="_blank">{{ @$order->productItem->name }}</a>
                    </p>
                    <span class="text-muted">{{ @$order->productItem->product->name }}</span>
                  </td>
                  <td style="max-width: 250px;">
                    <div style="white-space: pre-wrap;">{!! $order->cust_account_format !!}</div>
                  </td>
                  <td>{{ currency_format($order->turnover) }}</td>
                  <td>{{ currency_format($order->capital) }}</td>
                  <td>{{ currency_format($order->total_income) }}</td>
                  <td>
                    @php
                      // Only apply gateway fees for non-balance payment methods
                      $balancePaymentMethods = ['gpds coin', 'gpds_coin', 'balance', 'wallet'];
                      $paymentMethodName = strtolower($order->paymentMethod?->name ?? '');
                      $hasGatewayFee = !in_array($paymentMethodName, $balancePaymentMethods) && $order->turnover > 0;
                      
                      $gatewayFee = $hasGatewayFee ? $order->turnover * 0.023 : 0;
                      $vatOnFee = $gatewayFee * 0.12;
                      $affiliateBonus = $order->affiliateHistory?->amount ?? 0;
                      $netProfit = $order->total_income - $gatewayFee - $vatOnFee - $affiliateBonus;
                    @endphp
                    <span class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ currency_format($netProfit) }}
                    </span>
                  </td>
                  <td class="text-center">
                    {!! $order->order_status_raw !!}
                    @can('Process Order')
                      <x-input-update-status :order="$order" />
                    @endcan
                  </td>
                  <td>{{ @$order->updater->name }}</td>
                  <td>{{ @$order->user->email ?? @$order->cust_email }}</td>
                  <td>
                    @if($order->cust_phone_number)
                      <a href="https://wa.me/{{ $order->cust_phone_number }}" target="_blank">{{ $order->cust_phone_number }}</a>
                    @endif
                  </td>
                  <td>
                    @if($order->affiliate_id)
                      Code: {{ $order->affiliate?->code ?? '-' }}<br>
                      Name: {{ $order->affiliate?->user?->name ?? '-' }}<br>
                      Bonus: {{ currency_format($order->affiliateHistory?->amount ?? 0) }}
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="13" class="text-center">No Data</td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $orders->appends(request()->query())->links() }}
  </div>
@endsection