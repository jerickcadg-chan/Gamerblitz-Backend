@extends('layouts.app', [
    'activePage' => 'product',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
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
            <th>Name</th>
            <td>{{ $product->name }}</td>
          </tr>
          <tr>
            <th>Code</th>
            <td>{{ $product->code }}</td>
          </tr>
          <tr>
            <th>Slug</th>
            <td>{{ $product->slug }}</td>
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
            <th>Category</th>
            <td>{{ $product->productCategory->name }}</td>
          </tr>
          <tr>
            <th>Description</th>
            <td>{!! $product->description !!}</td>
          </tr>
          <tr>
            <th>How to Order</th>
            <td>{!! $product->how_to_order !!}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ parse_date_time($product->created_at) }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ parse_date_time($product->updated_at) }}</td>
          </tr>
          <tr>
            <th>Picture</th>
            <td>
              <a href="{{ $product->product_picture }}" target="_blank">
                <img src="{{ $product->product_picture }}" class="w-25" alt="{{ $product->name }}"/>
              </a>
            </td>
          </tr>
          <tr>
            <th>Cover</th>
            <td>
              <a href="{{ $product->product_cover }}" target="_blank">
                <img src="{{ $product->product_cover }}" class="w-25" alt="{{ $product->name }}"/>
              </a>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection
