@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th>Product</th>
                        <td>
                            <p>{{ $productItem->name }}</p>
                            <p class="text-muted">{{ $productItem->product->name }} ({{ ucfirst($productItem->product->category) }})</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Harga Umum</th>
                        <td>{{ rp_format($productItem->margin_price_public) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Reseller Silver</th>
                        <td>{{ rp_format($productItem->margin_price_silver) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Reseller Gold</th>
                        <td>{{ rp_format($productItem->margin_price_gold) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Reseller VIP</th>
                        <td>{{ rp_format($productItem->margin_price_vip) }}</td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td>{{ $productItem->stock ? currency_format($productItem->stock) : "∞" }}</td>
                    </tr>
                    <tr>
                        <th>Modal</th>
                        <td>{{ rp_format($productItem->capital) }}</td>
                    </tr>
                    @if ($productItem->product->category == \App\Constants\ProductConstant::VOUCHER)
                        <tr>
                            <th>Page Voucher</th>
                            <td><a href="{{ route('voucher.index', ['product_item_id' => $productItem->id]) }}" target="_blank">Klik disini</a> </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Created At</th>
                        <td>{{ parse_date_time($productItem->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ parse_date_time($productItem->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
