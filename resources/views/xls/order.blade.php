<table>
    <thead>
        <tr class="text-center">
            <th rowspan="2">No</th>
            <th rowspan="2">Pesanan</th>
            <th rowspan="2">Tgl Pesan</th>
            <th rowspan="2">Name Product</th>
            <th rowspan="2">Item Product</th>
            <th rowspan="2">Pembeli</th>
            <th rowspan="2">Email</th>
            <th rowspan="2">No Handphone</th>
            <th rowspan="2">Harga</th>
            <th rowspan="2">Biaya Admin</th>
            <th rowspan="2">Total Harga</th>
            <th rowspan="2">Diskon</th>
            <th rowspan="2">Modal</th>
            <th rowspan="2">Total Pendapatan</th>
            <th colspan="2">Status</th>
        </tr>
        <tr class="text-center">
            <th>Pembayaran</th>
            <th>Order</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $index => $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->code }}</td>
                <td>{{ parse_date_time($order->created_at) }}</td>
                <td>{{ $order->productItem->product->name }}</td>
                <td>{{ $order->productItem->name }}</td>
                <td>{{ @$order->userable->name }}</td>
                <td>{{ $order->cust_email }}</td>
                <td>{{ $order->cust_phone_number }}</td>
                <td>{{ $order->price }}</td>
                <td>{{ $order->admin_fee }}</td>
                <td>{{ $order->total_price }}</td>
                <td>{{ $order->discount_price }}</td>
                <td>{{ $order->capital }}</td>
                <td>{{ $order->total_income }}</td>
                <td>{!! $order->payment_status_raw !!}</td>
                <td>{!! $order->order_status_raw !!}</td>
            </tr>
        @empty
            <tr>
                <td colspan="100%" class="text-center">No Data</td>
            </tr>
        @endforelse
    </tbody>
</table>
