@extends('layouts.app', [
    'activePage' => 'product_item_price',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman Atur Harga </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">Atur Harga</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-12 text-lg-end">
                        <button type="submit" class="btn btn-primary">Tambah data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

