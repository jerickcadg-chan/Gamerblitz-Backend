@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    @include('product_items.filter')

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-12 text-lg-end">
                        <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Produk </th>
                            <th> Kode </th>
                            <th> Harga </th>
                            <th> Stok </th>
                            <th> Modal </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productItems as $index => $productItem)
                        <tr>
                            <td>{{ $productItems->firstItem() + $index }}</td>
                            <td>{{ $productItem->product->name }} {{ $productItem->name }}</td>
                            <td>{{ $productItem->code }}</td>
                            <td>{{ rp_format($productItem->price) }}</td>
                            <td>{{ $productItem->stock }}</td>
                            <td>{{ rp_format($productItem->capital) }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('product_item.show', $productItem),
                                    'edit_url' => route('product_item.edit', $productItem),
                                    'delete_url' => route('product_item.destroy', $productItem)
                                ])
                                @if ($productItem->product->category == \App\Constants\ProductConstant::VOUCHER)
                                    <button class="btn btn-gradient-success btn-rounded btn-icon" data-hover="tooltip" title="Kelola Voucher" data-placement="top"
                                       onclick="window.open('{{ route('voucher.index', ['product_item_id' => $productItem->id]) }}', '_blank')"> <i class="mdi mdi-cash"></i>
                                   </button>
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
                <div class="mt-2">
                    {!! $productItems->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
