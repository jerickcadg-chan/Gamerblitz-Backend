@extends('layouts.app', [
    'activePage' => 'product_item_category',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <table class="table table-nospace">
          <tr>
            <th>Product</th>
            <td>{{ $productItemCategory->product->name }}</td>
          </tr>
          <tr>
            <th>Name</th>
            <td>{{ $productItemCategory->name }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <a
          class="btn btn-primary"
          href="{{ route('product_item_categories.metas.create', ['product_item_category' => $productItemCategory]) }}"
        >Create Data</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th>Icon</th>
            <th>Action</th>
          </tr>
          </thead>
          <tbody>

          @forelse ($productItemCategory->metas as $meta)
            <tr>
              <td>
                <a href="{{ $meta->picture->url }}" target="_blank">
                  <img src="{{ $meta->picture->url }}" alt="{{ $productItemCategory->name }}" height="100" width="100">
                </a>
              </td>
              <td>
                @canany(['Edit Product Item', 'Delete Product Item'])
                  @include('master.action', [
                      'edit_url' => route('product_item_categories.metas.edit', [
                          'product_item_category' => $productItemCategory->id,
                          'meta' => $meta->id,
                      ]),
                      'delete_url' => route('product_item_categories.metas.destroy', [
                          'product_item_category' => $productItemCategory->id,
                          'meta' => $meta->id,
                      ])
                  ])
                @endcanany
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
    </div>
  </div>

@endsection
