@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  @include('product_items.filter')

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-md-12 text-lg-end">
            <a href="{{ route('product-item.price-form') }}" class="btn btn-info">Adjust Price Margin</a>
             <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>No</th>
              <th>Product</th>
              <th>Code</th>
              <th>Currency</th>
              <th>Provider</th>
              <th>Capital</th>
              <th>Margin</th>
              <th>Margin Reseller Silver</th>
              <th>Margin Reseller Gold</th>
              <th>Margin Reseller VIP</th>
              <th>Public Price</th>
              <th>Reseller Silver Price</th>
              <th>Reseller Gold Price</th>
              <th>Reseller VIP Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($productItems as $index => $productItem)
              <tr>
                <td>{{ $productItems->firstItem() + $index }}</td>
                <td>{{ $productItem->product->name }} {{ $productItem->name }}</td>
                <td>{{ $productItem->code }}</td>
                <td>{{ $productItem->currency_code }}</td>
                <td>{{ $productItem->product->provider }}</td>
                <td>{{ currency_format($productItem->capital, $productItem->currency_code) }}</td>
                <td>{{ $productItem->margin_percentage ?? 0 }} %</td>
                <td>{{ $productItem->margin_silver ?? 0 }} %</td>
                <td>{{ $productItem->margin_gold ?? 0 }} %</td>
                <td>{{ $productItem->margin_vip ?? 0 }} %</td>
                <td>{{ currency_format($productItem->margin_price_public, $productItem->currency_code) }}</td>
                <td>{{ currency_format($productItem->margin_price_silver, $productItem->currency_code) }}</td>
                <td>{{ currency_format($productItem->margin_price_gold, $productItem->currency_code) }}</td>
                <td>{{ currency_format($productItem->margin_price_vip, $productItem->currency_code) }}</td>
                <td>{{ $productItem->stock === null ? '∞' : $productItem->stock }}</td>
                <td>{{ $productItem->status }}</td>
                <td>
                  @include('master.action', [
                      'view_url' => route('product_item.show', $productItem),
                      'edit_url' => route('product_item.edit', $productItem)
                  ])
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="100%" class="text-center">No Data</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-2">
          {!! $productItems->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
