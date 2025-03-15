@extends('layouts.app', [
    'activePage' => 'order',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('order.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
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
                                <input class="form-control" type="text" name="order_code" value="{{ request('order_code') }}" placeholder="Nomor invoice">
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                                <input class="form-control" type="text" name="customer_name" value="{{ request('customer_name') }}" placeholder="Nama pembeli">
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                                <select class="form-control" name="status">
                                    <option value="">Semua Pesanan</option>
                                    <option value="in-process" {{ request('status') == 'in-process' ? 'selected' : null }}>Menunggu Proses</option>
                                    <option value="done" {{ request('status') == 'done' ? 'selected' : null }}>Pesanan Selesai</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : null }}>Pesanan Kedaluwarsa</option>
                                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : null }}>Pesanan Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                                <select class="form-control" name="product_id">
                                    <option value="">Pilih Produk</option>
                                    @foreach (\App\Models\Product::all() as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                                <input type="date" class="form-control" name="date" value="{{ request('date') }}" placeholder="Tanggal">
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-2 mb-2 pt-2">
                                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
                            </div>
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-responsive">
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Pesanan</th>
                                    <th rowspan="2">Nama Produk</th>
                                    <th rowspan="2">Harga</th>
                                    <th colspan="2">Pembeli</th>
                                    <th colspan="2">Status</th>
                                </tr>
                                <tr class="text-center">
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Pembayaran</th>
                                    <th>Order</th>
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
                                            <span class="text-muted">{{ parse_date_time($order->created_at) }}<span>
                                        </td>
                                        <td>
                                            <p class="mt-3">
                                                <a href="{{ route('product_item.show', $order->product_item_id) }}" target="_blank">{{ @$order->productItem->name }}</a>
                                            </p>
                                            <span class="text-muted">{{ @$order->productItem->product->name }}</span>
                                        </td>
                                        <td>{{ rp_format($order->total_price) }}</td>
                                        <td>{{ @$order->cust_email }}</td>
                                        <td><a href="https://web.whatsapp.com/send?phone={{ $order->cust_phone_number }}&text=Hai Kak" target="_blank">{{ $order->cust_phone_number }}</a></td>
                                        <td>{!! $order->payment_status_raw !!}</td>
                                        <td>
                                            <p>{!! $order->order_status_raw !!}</p>
                                            @if ($order->order_status == \App\Models\Order::INPROCESS)
                                                <form action="{{ route('order.status') }}" method="post" class="mt-3">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                    <button type="submit" name="status" class="btn btn-sm btn-primary" onclick="$('#status-{{ $order->id }}').val('{{ \App\Models\Order::DONE }}')">Selesai</button>
                                                    <button type="submit" name="status" class="btn btn-sm btn-danger" onclick="$('#status-{{ $order->id }}').val('{{ \App\Models\Order::CANCELED }}');return confirm('Yakin akan membatalkan pesanan?');">Batal</button>
                                                    <input type="hidden" id="status-{{ $order->id }}" name="status">
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
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
