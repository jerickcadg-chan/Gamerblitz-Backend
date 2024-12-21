@extends('layouts.app', [
    'activePage' => 'flash_sale',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('flash_sale.index') }}">{{ $title }}</a></li>
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
                        <td>{{ $flash_sale->name }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td>{{ $flash_sale->start_date->format('d M Y h:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Berakhir</th>
                        <td>{{ $flash_sale->end_date->format('d M Y h:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! $flash_sale->status_view !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @if ($flash_sale->items)
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
                            <th> Harga flas sale </th>
                            <th> Harga asli </th>
                            <th> Stok </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flash_sale->items as $index => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->productItem->name }}</td>
                                <td>{{ rp_format($item->price) }}</td>
                                <td>{{ rp_format($item->productItem->real_price) }}</td>
                                <td>{{ $item->stock }}</td>
                                <td>
                                    {{-- @include('master.action', [ --}}
                                    {{--     'view_url' => route('flash_sale_item.show', $item), --}}
                                    {{--     'edit_url' => route('flash_sale_item.edit', $item), --}}
                                    {{--     'delete_url' => route('flash_sale_item.destroy', $item) --}}
                                    {{-- ]) --}}
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

