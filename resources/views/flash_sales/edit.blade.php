@extends('layouts.app', [
    'activePage' => 'flash_sale',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('flash_sale.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $updateLink }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <label for="price_input">Normal Price</label>
                      <input type="text" class="form-control" name="current_pricce" id="price_input" placeholder="Enter Price" value="{{ $flash_sale->productItem->margin_price_public }}" readonly>
                    </div>
                    <div class="form-group">
                      <label for="price_input">Price</label>
                      <input type="text" class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" id="price_input" placeholder="Enter Price" value="{{ old('price', $flash_sale->price) }}" required>
                      @include('alerts.feedback', ['field' => 'price'])
                    </div>
                    <div class="form-group">
                      <label for="stock_input">Stock</label>
                      <input type="text" class="form-control {{ $errors->has('stock') ? ' is-invalid' : '' }}" name="stock" id="stock_input" placeholder="Enter Price" value="{{ old('stock', $flash_sale->stock) }}" required>
                      @include('alerts.feedback', ['field' => 'stock'])
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
