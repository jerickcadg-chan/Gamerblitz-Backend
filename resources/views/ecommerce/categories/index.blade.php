@extends('layouts.app', [
    'activePage' => 'ecommerce_category',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('ecommerce_category.index') }}">{{ $title }}</a></li>
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
              <input type="text" class="form-control" name="name" placeholder="Search category name"
                value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            @can('Create Ecommerce Category')
              <a href="{{ $createLink }}" class="btn btn-primary">Create Category</a>
            @endcan
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Slug</th>
              <th>Products</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($categories as $index => $category)
              <tr>
                <td>{{ $categories->firstItem() + $index }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                  @if($category->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  @can('Edit Ecommerce Category')
                    <a href="{{ route('ecommerce_category.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                  @endcan
                  @can('Delete Ecommerce Category')
                    <form action="{{ route('ecommerce_category.destroy', $category) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  @endcan
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">No categories found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div class="mt-2">
          {!! $categories->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
