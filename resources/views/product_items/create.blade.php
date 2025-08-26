@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data List</li>
            </ol>
        </nav>
    </div>

    {{-- <div class="col-lg-12 grid-margin stretch-card"> --}}
    {{--     <div class="card"> --}}
    {{--         <div class="card-body"> --}}
    {{--             <form method="POST" action="{{ $storeLink }}"> --}}
    {{--                 @csrf --}}
    {{--                 <div class="form-group"> --}}
    {{--                     <label for="input_product_id" class="required">Product</label> --}}
    {{--                     <select class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="product_id" id="input_product_id" required> --}}
    {{--                         <option value="">Pilih Product</option> --}}
    {{--                         @foreach ($products as $product) --}}
    {{--                             <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option> --}}
    {{--                         @endforeach --}}
    {{--                     </select> --}}
    {{--                     @include('alerts.feedback', ['field' => 'product_id']) --}}
    {{--                 </div> --}}
    {{--                 <div class="form-group"> --}}
    {{--                     <label for="input_name" class="required">Name</label> --}}
    {{--                     <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Enter name" value="{{ old('name') }}" required> --}}
    {{--                     @include('alerts.feedback', ['field' => 'name']) --}}
    {{--                 </div> --}}
    {{--                 <div class="form-group"> --}}
    {{--                     <label for="input_code" class="required">Kode</label> --}}
    {{--                     <input type="text" name="code" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" id="input_code" placeholder="Enter name" value="{{ old('code') }}" required> --}}
    {{--                     @include('alerts.feedback', ['field' => 'code']) --}}
    {{--                 </div> --}}
    {{--                 <div class="row"> --}}
    {{--                     <div class="col-md-6"> --}}
    {{--                         <div class="form-group"> --}}
    {{--                             <label for="input_price" class="required">Harga Umum</label> --}}
    {{--                             <input type="number" name="price" class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}" id="input_price" placeholder="Enter harga umum" value="{{ old('price') }}" min="1" required> --}}
    {{--                             @include('alerts.feedback', ['field' => 'price']) --}}
    {{--                         </div> --}}
    {{--                     </div> --}}
    {{--                     <div class="col-md-6"> --}}
    {{--                         <div class="form-group"> --}}
    {{--                             <label for="input_price_reseller" class="required">Harga Reseller</label> --}}
    {{--                             <input type="number" name="price_reseller" class="form-control {{ $errors->has('price_reseller') ? ' is-invalid' : '' }}" id="input_price_reseller" placeholder="Enter harga reseller" value="{{ old('price_reseller') }}" min="1" required> --}}
    {{--                             @include('alerts.feedback', ['field' => 'price_reseller']) --}}
    {{--                         </div> --}}
    {{--                     </div> --}}
    {{--                 </div> --}}
    {{--                 <div class="form-group"> --}}
    {{--                     <label for="input_capital" class="required">Modal</label> --}}
    {{--                     <input type="number" name="capital" class="form-control {{ $errors->has('capital') ? ' is-invalid' : '' }}" id="input_capital" placeholder="Enter modal" value="{{ old('capital') }}" min="1" required> --}}
    {{--                     @include('alerts.feedback', ['field' => 'capital']) --}}
    {{--                 </div> --}}
    {{--                 <div class="form-group"> --}}
    {{--                     <label for="input_stock" class="required">Stok</label> --}}
    {{--                     <input type="number" name="stock" class="form-control {{ $errors->has('stock') ? ' is-invalid' : '' }}" id="input_stock" placeholder="Enter stok" value="{{ old('stock') }}" min="1" required> --}}
    {{--                     @include('alerts.feedback', ['field' => 'stock']) --}}
    {{--                 </div> --}}
    {{--                 <button type="submit" class="btn btn-primary">Submit</button> --}}
    {{--                 <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a> --}}
    {{--             </form> --}}
    {{--         </div> --}}
    {{--     </div> --}}
    {{-- </div> --}}
@endsection
