@extends('layouts.app', [
    'activePage' => 'order',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
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
                                <th>Nomor Invoice</th>
                                <td>{{ $order->code }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>{{ parse_date_time_full($order->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>Pesanan</th>
                                <td>
                                    <p>{{ $order->productItem->name }} ({{ $order->qty }} Item)</p>
                                    <span class="text-muted">{{ $order->productItem->product->name }} ({{ ucfirst($order->productItem->product->category) }})</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Informasi Akun</th>
                                <td>{!! $order->cust_account_format !!}</td>
                            </tr>
                            <tr>
                                <th>Pembeli</th>
                                <td>
                                    @if ($order->user)
                                        <p><a href="{{ route('user.show', $order->user->id) }}" target="_blank">{{ $order->user->name }}</a></p>
                                    @endif
                                    <span class="text-muted">Email = {{ $order->cust_email }}</span><br>
                                    <span class="text-muted">No HP = <a href="https://web.whatsapp.com/send?phone={{ $order->cust_phone_number }}&text=Hai Kak" target="_blank">{{ $order->cust_phone_number }}</a></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Harga</th>
                                <td>
                                    <p>{{ rp_format($order->price) }}</p>
                                    <span class="text-muted">Diskon = {{ rp_format($order->discount_price) }} {{ @$order->discount->name }}</span> <br>
                                    <span class="text-muted">Biaya Admin = {{ rp_format($order->admin_fee) }}</span> <br>
                                    <span class="text-muted">Total Dibayar Customer = {{ rp_format($order->total_price) }}</span> <br>
                                </td>
                            </tr>
                            <tr>
                                <th>Diskon</th>
                                <td>{{ rp_format($order->discount_price) }}</td>
                            </tr>
                            <tr>
                                <th>Modal</th>
                                <td>{{ rp_format($order->capital) }}</td>
                            </tr>
                            <tr>
                                <th>Total Pendapatan</th>
                                <td>{{ rp_format($order->total_income) }}</td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran</th>
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
                                <th>ID Pembayaran External</th>
                                <td>{{ $order->payment_id }}</td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td>{!! $order->payment_status_raw !!}</td>
                            </tr>
                            <tr>
                                <th>Status Order</th>
                                <td>{!! $order->order_status_raw !!}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lunas</th>
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
                        <h4>Riwayat Status Pembayaran</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->histories->where('type', 'payment') as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->status }}</td>
                                        <td>{{ parse_date_time($payment->created_at) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <h4>Riwayat Status Order</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
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
                                        <td colspan="100%">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($order->order_status == \App\Models\Order::INPROCESS)
                            <form action="{{ route('order.status') }}" method="post" class="mt-3">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" name="status" class="btn btn-sm btn-primary" onclick="$('#status').val('{{ \App\Models\Order::DONE }}')">Selesai</button>
                                <button type="submit" name="status" class="btn btn-sm btn-danger" onclick="$('#status').val('{{ \App\Models\Order::CANCELED }}');return confirm('Yakin akan membatalkan pesanan?');">Batal</button>
                                <input type="hidden" id="status" name="status">
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
