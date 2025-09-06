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

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="mt-3">
          <form>
            <div class="row mb-3 g-xl-2">
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="order_code" value="{{ request('order_code') }}" placeholder="Invoice Code">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="customer_name" value="{{ request('customer_name') }}" placeholder="Buyer Name">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <select class="form-control select2" name="status">
                  <option value="">All Orders</option>
                  <option value="in-process" {{ request('status') == 'in-process' ? 'selected' : null }}>In Process</option>
                  <option value="done" {{ request('status') == 'done' ? 'selected' : null }}>Done</option>
                  <option value="expired" {{ request('status') == 'expired' ? 'selected' : null }}>Expired</option>
                  <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : null }}>Canceled</option>
                </select>
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <select class="form-control select2" name="product_id">
                  <option value="">Select Product</option>
                  @foreach (\App\Models\Product::all() as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input type="date" class="form-control" name="date" value="{{ request('date') }}" placeholder="Date">
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
                <th rowspan="2">Price</th>
                <th rowspan="2">Capital</th>
                <th rowspan="2">Profit</th>
                <th rowspan="2">Status</th>
                <th colspan="2">Buyer</th>
              </tr>
              <tr class="text-center">
                <th>Email</th>
                <th>No HP</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($orders as $index => $order)
                <tr>
                  <td>{{ $orders->firstItem() + $index }}</td>
                  <td>
                    <p class="mt-3">
                      <a href="{{ route('order.show', $order->id) }}">{{ $order->code }}</a>
                    </p>
                    <span class="text-muted">{{ parse_date_time($order->created_at) }}</span>
                  </td>
                  <td>
                    <p class="mt-3">
                      <a href="{{ route('product_item.show', $order->product_item_id) }}" target="_blank">{{ @$order->productItem->name }}</a>
                    </p>
                    <span class="text-muted">{{ @$order->productItem->product->name }}</span>
                  </td>
                  <td>{{ currency_format($order->converted_price) }}</td>
                  <td>{{ currency_format($order->converted_capital) }}</td>
                  <td>{{ currency_format($order->converted_total_income) }}</td>
                  <td>
                    <span class="d-block">{!! $order->order_status_raw !!}</span>
                    @if ($order->status == \App\Constants\StatusConst::ON_PROCESS)
                      <form action="{{ route('order.status') }}" method="post" class="mt-3">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" name="status" class="btn btn-sm btn-primary" onclick="$('#status-{{ $order->id }}').val('{{ \App\Models\Order::DONE }}')">Done</button>
                        <button type="submit" name="status" class="btn btn-sm btn-danger" onclick="$('#status-{{ $order->id }}').val('{{ \App\Models\Order::CANCELED }}');return confirm('Are You Sure?');">Cancel</button>
                        <input type="hidden" id="status-{{ $order->id }}" name="status">
                      </form>
                    @endif
                  </td>
                  <td>{{ @$order->cust_email }}</td>
                  <td><a href="https://wa.me/{{ $order->cust_phone_number }}&text=Hi" target="_blank">{{ $order->cust_phone_number }}</a></td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%" class="text-center">No Data</td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">
            {!! $orders->appends(request()->query())->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
