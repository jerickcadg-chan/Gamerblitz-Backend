@extends('layouts.app', [
    'activePage' => 'product',
])

@use('\App\Constants\ProductConstant')

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-4 mb-2">
            <form method="get">
              <input type="text" class="form-control" name="name" placeholder="Search product name"
                value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>No</th>
              <th>Picture</th>
              <th>Name</th>
              <th>Category</th>
              <th>Provider</th>
              <th>Items</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $index => $product)
              <tr>
                <td>{{ $products->firstItem() + $index }}</td>
                <td>
                  <a href="{{ $product->product_picture }}" target="_blank">
                    <img src="{{ $product->product_picture }}" class="w-25 object-fit-cover" alt="{{ $product->name }}"/>
                  </a>
                </td>
                <td>
                  <div class="mb-2">{{ $product->name }}</div>
                  <strong class="small">Code: {{ $product->code }}</strong>
                </td>
                <td>{{ $product->productCategory?->name }}</td>
                <td>
                  @isset($product->provider)
                    <div class="mb-2">
                      {{ App\Constants\ProviderConstant::AVAILABLE_PROVIDER[$product->provider] ?? '-' }}
                      ({{ App\Constants\CountryConstant::name($product->provider_country) ?? '-' }})
                    </div>
                    <strong class="small">Code: {{ $product->provider_code }}</strong>
                  @else
                    -
                  @endisset
                </td>
                <td>
                  <a
                    href="{{ route('product_item.index', ['product_id' => $product->id]) }}">{{ $product->product_items_count }}</a>
                </td>
                <td>{!! $product->statusView !!}</td>
                <td>
                  @include('master.action', [
                      'view_url' => route('product.show', $product),
                      'edit_url' => route('product.edit', $product),
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
