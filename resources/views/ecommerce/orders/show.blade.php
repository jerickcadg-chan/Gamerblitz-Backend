@extends('layouts.app', [
    'activePage' => 'ecommerce_order',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Order #{{ $order->order_number }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('ecommerce_order.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order Details</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Capital</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Profit</th>
              </tr>
            </thead>
            <tbody>
              @php
                $totalCapital = 0;
                $totalProfit = 0;
              @endphp
              @foreach($order->items as $item)
                @php
                  $itemCapital = $item->capital_price * $item->quantity;
                  $itemProfit = $item->subtotal - $itemCapital;
                  $totalCapital += $itemCapital;
                  $totalProfit += $itemProfit;
                @endphp
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      @if($item->product && $item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}"
                          style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                      @endif
                      <div>
                        <div>{{ $item->product_name }}</div>
                        @if($item->variant_name)
                          <small class="text-muted">{{ $item->variant_name }}</small>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td>₱{{ number_format($item->price, 2) }}</td>
                  <td>₱{{ number_format($item->capital_price, 2) }}</td>
                  <td>{{ $item->quantity }}</td>
                  <td>₱{{ number_format($item->subtotal, 2) }}</td>
                  <td class="{{ $itemProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ₱{{ number_format($itemProfit, 2) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                <td>₱{{ number_format($order->subtotal, 2) }}</td>
                <td></td>
              </tr>
              @if($order->shipping_fee > 0)
                <tr>
                  <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                  <td>₱{{ number_format($order->shipping_fee, 2) }}</td>
                  <td></td>
                </tr>
              @endif
              @if($order->discount > 0)
                <tr>
                  <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                  <td>-₱{{ number_format($order->discount, 2) }}</td>
                  <td></td>
                </tr>
              @endif
              <tr class="table-light">
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td><strong>₱{{ number_format($order->total, 2) }}</strong></td>
                <td></td>
              </tr>
              <tr class="table-warning">
                <td colspan="4" class="text-end"><strong>Total Capital:</strong></td>
                <td><strong>₱{{ number_format($totalCapital, 2) }}</strong></td>
                <td></td>
              </tr>
              <tr class="{{ $totalProfit >= 0 ? 'table-success' : 'table-danger' }}">
                <td colspan="4" class="text-end"><strong>Total Profit:</strong></td>
                <td></td>
                <td><strong>₱{{ number_format($totalProfit, 2) }}</strong></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Shipping Information</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
<p><strong>Name:</strong> {{ $order->customer_name }}</p>
<p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
<p><strong>Email:</strong> {{ $order->customer_email }}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Address:</strong></p>
              <p>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_province }}<br>
                {{ $order->shipping_postal_code }}
              </p>
            </div>
          </div>
        </div>
      </div>

@if($order->paymentOrder)
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Payment Information</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <p><strong>Payment Code:</strong> {{ $order->paymentOrder->code }}</p>
        <p><strong>Payment Method:</strong> {{ $order->paymentOrder->paymentMethod->name ?? '-' }}</p>
        <p><strong>Payment Status:</strong>
          <span class="badge badge-{{ $order->paymentOrder->status == 'success' ? 'success' : ($order->paymentOrder->status == 'pending' ? 'warning' : 'secondary') }}">
            {{ ucfirst($order->paymentOrder->status) }}
          </span>
        </p>
      </div>
      <div class="col-md-6">
        <p><strong>Amount:</strong> ?{{ number_format($order->paymentOrder->total_price, 2) }}</p>
        <p><strong>Paid At:</strong> {{ $order->paymentOrder->updated_at?->format('d M Y H:i') ?? '-' }}</p>
      </div>
    </div>
  </div>
</div>
@endif

    </div>

    <div class="col-md-4">
      {{-- Order Summary Card with Capital/Profit --}}
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Order Summary</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr>
              <td><strong>Total Price:</strong></td>
              <td class="text-end">₱{{ number_format($order->total, 2) }}</td>
            </tr>
            <tr>
              <td><strong>Total Capital:</strong></td>
              <td class="text-end">₱{{ number_format($totalCapital, 2) }}</td>
            </tr>
            <tr class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">
              <td><strong>Total Profit:</strong></td>
              <td class="text-end"><strong>₱{{ number_format($totalProfit, 2) }}</strong></td>
            </tr>
          </table>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Order Status</h5>
        </div>
        <div class="card-body">
          @can('Edit Ecommerce Order')
            <form action="{{ route('ecommerce_order.update', $order) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                  @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                      {{ ucfirst($status) }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label for="tracking_number">Tracking Number</label>
                <input type="text" class="form-control" id="tracking_number" name="tracking_number"
                  value="{{ $order->tracking_number }}">
              </div>

              <div class="form-group">
                <label for="admin_notes">Admin Notes</label>
                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3">{{ $order->admin_notes }}</textarea>
              </div>

@if($order->paymentOrder)
<div class="form-group">
  <label for="payment_status">Payment Status</label>
  <select class="form-control" id="payment_status" name="payment_status">
    @foreach(['pending', 'success', 'expired', 'failed'] as $pStatus)
      <option value="{{ $pStatus }}" {{ $order->paymentOrder->status == $pStatus ? 'selected' : '' }}>
        {{ ucfirst($pStatus) }}
      </option>
    @endforeach
  </select>
  <small class="form-text text-muted">Changing payment status will be logged.</small>
</div>
@endif

              <button type="submit" class="btn btn-primary btn-block">Update Order</button>
            </form>
          @else
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            @if($order->tracking_number)
              <p><strong>Tracking:</strong> {{ $order->tracking_number }}</p>
            @endif
          @endcan
        </div>
      </div>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Status History</h5>
  </div>
  <div class="card-body" style="max-height: 300px; overflow-y: auto;">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>Date</th>
          <th>Status</th>
          <th>Changed By</th>
          <th>Note</th>
        </tr>
      </thead>
      <tbody>
        @forelse($order->statusHistories ?? [] as $history)
          <tr>
            <td><small>{{ $history->created_at->format('d-M-Y, H:i:s') }}</small></td>
            <td>
              @if(str_starts_with($history->status, 'payment:'))
                <span class="badge badge-info">{{ ucfirst(str_replace('payment:', 'Payment: ', $history->status)) }}</span>
              @else
                <span class="badge badge-secondary">{{ ucfirst($history->status) }}</span>
              @endif
            </td>
            <td><small>{{ $history->user->name ?? 'System' }}</small></td>
            <td><small>{{ $history->note ?? '-' }}</small></td>
          </tr>
        @empty
          <tr>
            <td><small>{{ $order->created_at->format('d-M-Y, H:i:s') }}</small></td>
            <td><span class="badge badge-secondary">{{ ucfirst($order->status) }}</span></td>
            <td><small>System</small></td>
            <td><small>Order created</small></td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Customer Information</h5>
        </div>
        <div class="card-body">
          @if($order->user)
            <p><strong>Name:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Phone:</strong> {{ $order->user->phone_number ?? '-' }}</p>
          @else
            <p class="text-muted">Guest Order</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('ecommerce_order.index') }}" class="btn btn-secondary">Back to Orders</a>
  </div>
@endsection