@extends('layouts.app', [
    'activePage' => 'ecommerce_product',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('ecommerce_product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-3 mb-2">
            <form method="get" class="d-flex gap-2">
              <input type="text" class="form-control" name="name" placeholder="Search product name"
                value="{{ request('name') }}">
          </div>
          <div class="col-md-3 mb-2">
              <select name="category_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </form>
          </div>
          <div class="col-md-6 text-lg-end">
            @can('Create Ecommerce Product')
              <a href="{{ $createLink }}" class="btn btn-primary">Create Product</a>
            @endcan
          </div>
        </div>
        <table class="table-bordered table-hover table">
		<thead>
            <tr>
              <th>No</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Variants</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
		</thead>          <tbody>
            @forelse ($products as $index => $product)
              <tr>
                <td>{{ $products->firstItem() + $index }}</td>
                <td>
                  @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                      style="width: 50px; height: 50px; object-fit: cover;">
                  @else
                    <span class="text-muted">No image</span>
                  @endif
                </td>
                <td>
                  <div class="mb-1">{{ $product->name }}</div>
                  <small class="text-muted">{{ $product->slug }}</small>
                </td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td>
                  @if($product->sale_price)
                    <del class="text-muted">₱{{ number_format($product->price, 2) }}</del><br>
                    <strong class="text-success">₱{{ number_format($product->sale_price, 2) }}</strong>
                  @else
                    ₱{{ number_format($product->price, 2) }}
                  @endif
                </td>
                <td>
                  @if($product->track_stock)
                    {{ $product->stock }}
                  @else
                    <span class="text-muted">Not tracked</span>
                  @endif
                </td>
<td>
  @if($product->variantOptions->count() > 0)
    <span class="badge badge-info">{{ $product->variantOptions->count() }} options</span>
    <small class="d-block text-muted">{{ $product->variants->count() }} values</small>
  @else
    <span class="text-muted">-</span>
  @endif
</td>
                <td>
                  @if($product->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                  @if($product->is_featured)
                    <span class="badge badge-info">Featured</span>
                  @endif
                </td>
                <td>
                  @can('Edit Ecommerce Product')
                    <a href="{{ route('ecommerce_product.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                  @endcan
                  @can('Delete Ecommerce Product')
                    <form action="{{ route('ecommerce_product.destroy', $product) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  @endcan
                </td>
              </tr>
            @empty
	<tr>
                <td colspan="9" class="text-center">No products found</td>
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
