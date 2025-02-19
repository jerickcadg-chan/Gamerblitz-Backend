@extends('layouts.app', [
    'activePage' => 'product_item_category',
])

@use('\App\Constants\product_item_categoryConstant')

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
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
                    {{-- <div class="col-md-8 text-lg-end"> --}}
                    {{--     <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a> --}}
                    {{-- </div> --}}
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Nama </th>
                            <th> Produk </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productItemCategories as $index => $product_item_category)
                        <tr>
                            <td>{{ $productItemCategories->firstItem() + $index }}</td>
                            <td>{{ $product_item_category->name }}</td>
                            <td>{{ $product_item_category->product->name }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2">
                    {!! $productItemCategories->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

