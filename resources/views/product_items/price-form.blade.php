@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Product Item Price</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('product-item.price-form-update') }}">
            @csrf
            @method('PUT')
            <div class="form-group">
              <label for="product_id" class="required">Product</label>
              <select name="product_id" class="form-control select2" id="product_id" required>
                <option value="0">All Product</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="marginInput">Margin Public</label>
              <input type="number" name="margin" class="form-control" id="marginInput" placeholder="Enter margin public price (%)" step="0.01">
              @include('alerts.feedback', ['field' => 'margin'])
            </div>
            <div class="form-group">
              <label for="marginSilverInput">Margin Reseller Silver</label>
              <input type="number" name="margin_silver" class="form-control" id="marginSilverInput" placeholder="Enter margin for reseller silver (%)" step="0.01">
              @include('alerts.feedback', ['field' => 'margin_silver'])
            </div>
            <div class="form-group">
              <label for="marginGoldInput">Margin Reseller Gold</label>
              <input type="number" name="margin_gold" class="form-control" id="marginGoldInput" placeholder="Enter margin for reseller silver (%)" step="0.01">
              @include('alerts.feedback', ['field' => 'margin_gold'])
            </div>
            <div class="form-group">
              <label for="marginVipInput">Margin Reseller Vip</label>
              <input type="number" name="margin_vip" class="form-control" id="marginVipInput" placeholder="Enter margin for reseller silver (%)" step="0.01">
              @include('alerts.feedback', ['field' => 'margin_vip'])
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('product_item.index') }}" class="btn btn-light">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
