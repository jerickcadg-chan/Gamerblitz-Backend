@extends('layouts.app', [
    'activePage' => 'product_item_category',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form action="" method="GET">
          <div class="row">
            <div class="col-md-4 mb-2">
              <input type="text" class="form-control" name="name" placeholder="Search item category name"
                value="{{ request('name') }}">
            </div>
            <div class="col-md-4 mb-2">
              <select class="form-control select2" name="product_id" id="product_input">
                <option value="">--- All Products ---</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>
                    {{ $product->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <div class="pt-2">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('product_item_category.index') }}" class="btn btn-danger btn-sm">Reset</a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-12 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>No</th>
              <th> Name </th>
              <th> Product </th>
              <th> Action </th>
            </tr>
          </thead>
          <tbody>
            @forelse ($productItemCategories as $index => $product_item_category)
              <tr>
                <td>{{ $productItemCategories->firstItem() + $index }}</td>
                <td>{{ $product_item_category->name }}</td>
                <td>{{ $product_item_category->product->name }}</td>
                <td>
                  @include('master.action', [
                      'view_url' => route('product_item_category.show', $product_item_category),
                      'edit_url' => route('product_item_category.edit', $product_item_category),
                      'delete_url' => route('product_item_category.destroy', $product_item_category),
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
        <div class="mt-2">
          {!! $productItemCategories->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
