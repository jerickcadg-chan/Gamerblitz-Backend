@extends('layouts.app', [
    'activePage' => 'product_category',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_category.index') }}">{{ $title }}</a></li>
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
              <input type="text" class="form-control" name="name" placeholder="Search product category name" value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th> # </th>
            <th> Name </th>
            <th> Action </th>
          </tr>
          </thead>
          <tbody>
          @forelse ($productCategories as $index => $product_category)
            <tr>
              <td>{{ $productCategories->firstItem() + $index }}</td>
              <td>{{ $product_category->name }}</td>
              <td>
                @include('master.action', [
                    'view_url' => route('product_category.show', $product_category),
                    'edit_url' => route('product_category.edit', $product_category),
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
          {!! $productCategories->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
