@extends('layouts.app', [
    'activePage' => 'order',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('order.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <table class="table table-nospace table-hover">
              <tr>
                <th>Invoice Code</th>
                <td>{{ $order->code }}</td>
              </tr>
              <tr>
                <th>Created At</th>
                <td>{{ parse_date_time_full($order->created_at) }}</td>
              </tr>
              <tr>
                <th>Order</th>
                <td>
                  <p>{{ $order->productItem->name }} ({{ $order->qty }} Item)</p>
                  <span class="text-muted">{{ $order->productItem->product->name }} ({{ ucfirst($order->productItem->product->productCategory->name) }})</span>
                </td>
              </tr>
              <tr>
                <th>Customer Number</th>
                <td>{!! $order->cust_account_format !!}</td>
              </tr>
              <tr>
                <th>Buyer</th>
                <td>
                  @if ($order->user)
                    <p><a href="{{ route('user.show', $order->user->id) }}" target="_blank">{{ $order->user->name }}</a></p>
                  @endif
                  <span class="text-muted">Email = {{ $order->cust_email }}</span><br>
                  <span class="text-muted">Whatsapp = <a href="https://web.whatsapp.com/send?phone={{ $order->cust_phone_number }}&text=Hai Kak" target="_blank">{{ $order->cust_phone_number }}</a></span>
                </td>
              </tr>
              <tr>
                <th>Price</th>
                <td>{{ currency_format($order->converted_price) }}</td>
              </tr>
              <tr>
                <th>Discount</th>
                <td>{{ currency_format($order->converted_discount_price) }}</td>
              </tr>
              <tr>
                <th>Turnover</th>
                <td>{{ currency_format($order->converted_turnover) }}</td>
              </tr>
              <tr>
                <th>Admin Fee</th>
                <td>{{ currency_format($order->converted_admin_fee) }}</td>
              </tr>
              <tr>
                <th>Total Price</th>
                <td>{{ currency_format($order->converted_total_price) }}</td>
              </tr>
              <tr>
                <th>Capital</th>
                <td>{{ currency_format($order->converted_capital) }}</td>
              </tr>
              <tr>
                <th>Profit</th>
                <td>{{ currency_format($order->converted_total_income) }}</td>
              </tr>
              <tr>
                <th>Payment Method</th>
                <td>{{ strtoupper($order->payment_method) }}</td>
              </tr>
              @if ($order->productItem->product->category == 'voucher')
                @if ($order->voucher)
                  <tr>
                    <th>Voucher</th>
                    <td><a href="{{ route('voucher.show', $order->voucher->id) }}" target="_blank">{{ $order->voucher->serial_number }}</a></td>
                  </tr>
                @endif
              @endif
              <tr>
                <th>External Payment ID</th>
                <td>{{ $order->payment_id }}</td>
              </tr>
              <tr>
                <th>Status Order</th>
                <td>{!! $order->order_status_raw !!}</td>
              </tr>
              <tr>
                <th>Settlement Date</th>
                <td>{{ parse_date_time_full($order->settlement_date) }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-header">
            <b>Order Status History</b>
          </div>
          <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>#</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              @forelse ($order->histories->where('type', 'order') as $history)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $history->status }}</td>
                  <td>{{ parse_date_time($history->created_at) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%">No Data</td>
                </tr>
              @endforelse
              </tbody>
            </table>
            @if ($order->order_status == \App\Models\Order::INPROCESS)
              <form action="{{ route('order.status') }}" method="post" class="mt-3">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" name="status" class="btn btn-sm btn-primary" onclick="$('#status').val('{{ \App\Models\Order::DONE }}')">Done</button>
                <button type="submit" name="status" class="btn btn-sm btn-danger" onclick="$('#status').val('{{ \App\Models\Order::CANCELED }}');return confirm('Are You Sure?');">Cancel</button>
                <input type="hidden" id="status" name="status">
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
