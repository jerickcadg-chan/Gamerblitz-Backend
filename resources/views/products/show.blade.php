@extends('layouts.app', [
    'activePage' => 'product',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-nospace">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Kode</th>
                        <td>{{ $product->code }}</td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><a href="{{ $product->full_slug }}" target="_blank">{{ $product->full_slug }}</a></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! $product->statusView !!}</td>
                    </tr>
                    <tr>
                        <th>Company</th>
                        <td>{{ $product->company }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ ucfirst($product->category) }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{!! $product->description !!}</td>
                    </tr>
                    <tr>
                        <th>Cara Pemesanan</th>
                        <td>{!! $product->how_to_order !!}</td>
                    </tr>
                    <tr>
                        <th>Tgl Dibuat</th>
                        <td>{{ parse_date_time($product->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Tgl Diupdate</th>
                        <td>{{ parse_date_time($product->updated_at) }}</td>
                    </tr>
                    <tr>
                        <th>Gambar</th>
                        <td>
                          <img src="{{ $product->product_picture }}" class="w-25" alt="{{ $product->product_picture }}"/>
                        </td>
                    </tr>
                    <tr>
                        <th>Cover</th>
                        <td>
                          <img src="{{ $product->product_cover }}" class="w-25" alt="{{ $product->product_cover }}"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @if ($product->productItems)
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="card-title">
                    <h4>Item Produk</h4>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Produk </th>
                            <th> Harga </th>
                            <th> Stok </th>
                            <th> Modal </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->productItems as $index => $productItem)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $productItem->name }}</td>
                                <td>{{ rp_format($productItem->price) }}</td>
                                <td>{{ $productItem->stock }}</td>
                                <td>{{ rp_format($productItem->capital) }}</td>
                                <td>
                                    @include('master.action', [
                                        'view_url' => route('product_item.show', $productItem),
                                        'edit_url' => route('product_item.edit', $productItem),
                                        'delete_url' => route('product_item.destroy', $productItem)
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endsection
