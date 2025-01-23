@extends('layouts.app', [
    'activePage' => 'product',
])

@use('\App\Constants\ProductConstant')

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                        <form method="get">
                          <input type="text" class="form-control" name="name" placeholder="Cari nama produk" value="{{ request('name') }}">
                        </form>
                    </div>
                    <div class="col-md-8 text-lg-end">
                        <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Nama </th>
                            <th> Kode </th>
                            <th> Kategori </th>
                            <th> Status </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
                            <td><a href="{{ $product->full_slug }}" target="_blank">{{ $product->name }}</a></td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->product_category }}</td>
                            <td>{!! $product->statusView !!}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('product.show', $product),
                                    'edit_url' => route('product.edit', $product),
                                    'delete_url' => route('product.destroy', $product)
                                ])
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
                    {!! $products->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
