<table>
  <tr><td>Transaction Code</td><td>{{ $order->code }}</td></tr>
  <tr><td>Account Information</td><td>{!! $order->cust_account_format !!}</td></tr>
  <tr><td>Product</td><td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td></tr>
  <tr><td>Transaction Amount</td><td>{{ currency_format($order->total_price) }}</td></tr>
  <tr><td>Order Status</td><td>{{ $order->status }}</td></tr>
  @if($order->serial_number)
    <tr><td>Serial Number</td><td>{{ $order->serial_number }}</td></tr>
  @endif
  @if($order->note)
    <tr><td>Note</td><td>{{ $order->note }}</td></tr>
  @endif
</table>
