@extends('layouts.app', [
    'activePage' => 'ecommerce_order',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('ecommerce_order.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-3 mb-2">
            <form method="get" class="d-flex gap-2">
              <input type="text" class="form-control" name="code" placeholder="Search order code"
                value="{{ request('code') }}">
          </div>
          <div class="col-md-3 mb-2">
              <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                  <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                  </option>
                @endforeach
              </select>
            </form>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>No</th>
              <th>Order Code</th>
              <th>Customer</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($orders as $index => $order)
              <tr>
                <td>{{ $orders->firstItem() + $index }}</td>
                <!-- CHANGE: Use order_number instead of order_code -->
                <td>{{ $order->order_number }}</td>
                <td>
                  <div>{{ $order->user->name ?? 'Guest' }}</div>
                  <small class="text-muted">{{ $order->user->email ?? $order->shipping_email }}</small>
                </td>
                <td>{{ $order->items->count() }} items</td>
                <!-- CHANGE: Use total instead of total_amount -->
                <td>PHP {{ number_format($order->total, 2) }}</td>
                <td>
                  @switch($order->status)
                    @case('pending')
                      <span class="badge badge-warning">Pending</span>
                      @break
                    @case('processing')
                      <span class="badge badge-info">Processing</span>
                      @break
                    @case('shipped')
                      <span class="badge badge-primary">Shipped</span>
                      @break
                    @case('delivered')
                      <span class="badge badge-success">Delivered</span>
                      @break
                    @case('cancelled')
                      <span class="badge badge-danger">Cancelled</span>
                      @break
                    @default
                      <span class="badge badge-secondary">{{ $order->status }}</span>
                  @endswitch
                </td>
                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                <td>
                  <a href="{{ route('ecommerce_order.show', $order) }}" class="btn btn-sm btn-info">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center">No orders found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-2">
          {!! $orders->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection